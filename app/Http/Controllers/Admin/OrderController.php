<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Support\OrderPresenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function data(Request $request)
    {
        $draw = (int) $request->input('draw', 1);
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $search = trim((string) $request->input('search.value', ''));

        $columns = [
            0 => 'order_id',
            1 => 'created_at',
            2 => 'customer_name',
            3 => 'created_at',
            4 => 'amount_due',
            5 => 'payment_method_type',
            6 => 'status',
        ];
        $orderColIdx = (int) $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderCol = $columns[$orderColIdx] ?? 'created_at';

        $base = $this->applyOrderFilters(Order::query(), $request);
        $total = (clone $base)->count();

        if ($search !== '') {
            $statusAliases = [
                'menunggu' => 'pending',
                'pending' => 'pending',
                'diverifikasi' => 'verified',
                'verified' => 'verified',
                'verifikasi' => 'verified',
                'lunas' => 'paid',
                'paid' => 'paid',
                'dikirim' => 'shipped',
                'shipped' => 'shipped',
                'siap diambil' => 'shipped',
                'selesai' => 'completed',
                'completed' => 'completed',
                'batal' => 'cancelled',
                'cancelled' => 'cancelled',
                'dibatalkan' => 'cancelled',
            ];
            $needle = mb_strtolower($search);
            $statusMatch = null;
            foreach ($statusAliases as $alias => $value) {
                if (str_contains($alias, $needle)) {
                    $statusMatch = $value;
                    break;
                }
            }

            $base->where(function ($q) use ($search, $statusMatch) {
                $like = '%'.$search.'%';
                $q->where('order_id', 'like', $like)
                    ->orWhere('customer_name', 'like', $like)
                    ->orWhere('customer_email', 'like', $like)
                    ->orWhere('customer_phone', 'like', $like);
                if ($statusMatch) {
                    $q->orWhere('status', $statusMatch);
                }
            });
        }

        $filtered = (clone $base)->count();

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)
            ->take($length > 0 ? $length : 10)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows->map(fn ($o) => $this->serializeOrder($o))->all(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->applyOrderFilters(Order::query(), $request)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusLabels = [
            'pending' => 'Menunggu Pembayaran',
            'verified' => 'Pembayaran Diterima',
            'paid' => 'Pembayaran Lunas',
            'shipped' => 'Pesanan Dikirim / Siap Diambil',
            'completed' => 'Pesanan Selesai',
            'cancelled' => 'Pesanan Dibatalkan',
        ];
        $paymentTypeLabels = ['dp' => 'DP (50%)', 'full' => 'Bayar Lunas'];
        $shippingLabels = ['kirim' => 'Dikirim', 'pickup' => 'Ambil di Tempat'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pesanan');

        $headers = [
            'ID Pesanan',
            'Tanggal',
            'Nama Pelanggan',
            'Email',
            'Telepon',
            'Pengiriman',
            'Lokasi/Alamat',
            'Metode Pembayaran',
            'Tipe Pembayaran',
            'Tanggal Pembayaran',
            'Subtotal',
            'Dibayar',
            'Sisa',
            'Status',
            'Item',
        ];

        foreach ($headers as $i => $label) {
            $sheet->setCellValue([$i + 1, 1], $label);
        }

        $lastCol = $sheet->getHighestColumn();
        $headerRange = 'A1:'.$lastCol.'1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $row = 2;
        foreach ($orders as $order) {
            $itemsText = collect($order->item ?? [])->map(function ($line) {
                $qty = (int) ($line['qty'] ?? 0);
                $price = (int) ($line['price'] ?? 0) + (int) ($line['fee'] ?? 0);
                $variant = trim(implode(' · ', array_filter([$line['category'] ?? '', $line['sleeve'] ?? '', $line['size'] ?? ''])));
                $name = $line['name'] ?? '-';

                return $qty.'× '.$name.($variant !== '' ? ' ('.$variant.')' : '').' @ Rp'.number_format($price, 0, ',', '.');
            })->implode("\n");

            $paymentMethod = match ($order->payment_method_type) {
                'bank' => 'Transfer Bank · '.($order->payment_data['label'] ?? '-'),
                'qris' => 'QRIS',
                default => ucfirst((string) $order->payment_method_type),
            };

            $address = $order->shipping_method === 'kirim'
                ? (string) $order->customer_address
                : ucfirst((string) $order->pickup_location);

            $subtotal = (int) $order->subtotal;
            $paid = (int) $order->amount_due;
            $remaining = max(0, $subtotal - $paid);

            $values = [
                $order->order_id,
                optional($order->created_at)->format('Y-m-d H:i'),
                $order->customer_name,
                $order->customer_email,
                $order->customer_phone,
                $shippingLabels[$order->shipping_method] ?? $order->shipping_method,
                $address,
                $paymentMethod,
                $paymentTypeLabels[$order->payment_type] ?? $order->payment_type,
                optional($order->payment_proof_uploaded_at)->format('Y-m-d H:i'),
                $subtotal,
                $paid,
                $remaining,
                $statusLabels[$order->status] ?? $order->status,
                $itemsText,
            ];

            foreach ($values as $i => $value) {
                $sheet->setCellValue([$i + 1, $row], $value);
            }
            $row++;
        }

        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle('A2:'.$lastCol.$lastRow)->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            $sheet->getStyle('K2:M'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        foreach (range(1, count($headers)) as $colIdx) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIdx))->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $filename = 'pesanan-'.now()->format('Ymd-His').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'ok' => true,
            'html' => $this->renderDetail($order),
            'title' => 'Detail Pesanan '.$order->order_id,
        ]);
    }

    public function invoice(Order $order)
    {
        $data = OrderPresenter::invoiceData($order);

        $pdf = Pdf::loadView('pdf.order-invoice', [
            'order' => $data,
            'isTicketOrder' => $this->isTicketOrder($order),
            'qrDataUri' => $data['checkin_url'] ? $this->generateQrDataUri($data['checkin_url']) : null,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('invoice-'.$order->order_id.'.pdf');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,verified,paid,shipped,completed,cancelled'],
            'tracking' => ['nullable', 'string', 'max:100'],
            'pickup_address' => ['nullable', 'string', 'max:255'],
            'pickup_contact_name' => ['nullable', 'string', 'max:100'],
            'pickup_contact_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $next = $validated['status'];
        $current = $order->status;

        $allowed = match ($current) {
            'pending' => ['verified', 'cancelled'],
            'verified' => ['paid', 'cancelled', 'pending'],
            'paid' => ['shipped', 'cancelled'],
            'shipped' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => ['pending'],
            default => [],
        };

        if (! in_array($next, $allowed, true)) {
            return response()->json([
                'ok' => false,
                'message' => 'Transisi status tidak diperbolehkan.',
            ], 422);
        }

        // Full payment sudah lunas sejak awal → langsung 'paid' saat pembayaran diverifikasi.
        if ($next === 'verified' && $order->payment_type === 'full') {
            $next = 'paid';
        }

        // Pesanan kirim wajib punya nomor resi sebelum ditandai dikirim.
        // Resi bisa dikirim bersama aksi ini (alur gabungan "Tandai Dikirim").
        if ($next === 'shipped' && $order->shipping_method === 'kirim') {
            if (! empty($validated['tracking'])) {
                $order->shipping_tracking = trim($validated['tracking']);
            }
            if (empty($order->shipping_tracking)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Input nomor resi terlebih dahulu sebelum menandai dikirim.',
                ], 422);
            }
        }

        // Pesanan pickup wajib punya titik temu (alamat & kontak) per-pesanan sebelum ditandai siap diambil.
        if ($next === 'shipped' && $order->shipping_method === 'pickup') {
            foreach (['pickup_address', 'pickup_contact_name', 'pickup_contact_phone'] as $field) {
                if ($request->filled($field)) {
                    $order->{$field} = trim((string) $request->input($field));
                }
            }
            if (empty($order->pickup_address) || empty($order->pickup_contact_phone)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Lengkapi alamat & nomor kontak pengambilan terlebih dahulu.',
                ], 422);
            }
        }

        // Tandai lunas manual: DP yang dilunasi tanpa bukti dari pembeli (konfirmasi admin).
        $manualSettlement = false;
        if ($next === 'paid' && $order->payment_type === 'dp' && empty($order->dp_settlement_verified_at)) {
            $order->dp_settlement_verified_at = now();
            $order->dp_settlement_uploaded_at = $order->dp_settlement_uploaded_at ?? now();
            $manualSettlement = true;
        }

        $order->status = $next;
        if (in_array($next, ['verified', 'paid', 'shipped', 'completed'], true)) {
            $order->verified_at = $order->verified_at ?? now();
        } elseif ($next === 'pending') {
            $order->verified_at = null;
        }
        $order->completed_at = $next === 'completed' ? now() : ($next === 'pending' ? null : $order->completed_at);
        $order->save();

        // Terbitkan e-tiket (QR check-in) begitu pembayaran tiket diterima admin.
        $this->ensureCheckinCode($order);

        return response()->json([
            'ok' => true,
            'message' => $manualSettlement ? 'Pesanan ditandai lunas.' : 'Status pesanan diperbarui.',
            'stats' => $this->statsFor($request->input('filter_category')),
            'stockHtml' => $this->stockCardsHtml(),
        ]);
    }

    public function updateShipping(Request $request, Order $order)
    {
        if ($order->shipping_method !== 'kirim') {
            return response()->json(['ok' => false, 'message' => 'Pesanan bukan tipe kirim.'], 422);
        }

        $validated = $request->validate([
            'tracking' => ['required', 'string', 'max:100'],
        ]);

        $order->update(['shipping_tracking' => $validated['tracking']]);

        return response()->json(['ok' => true, 'message' => 'Nomor resi tersimpan.']);
    }

    public function updatePickup(Request $request, Order $order)
    {
        if ($order->shipping_method !== 'pickup') {
            return response()->json(['ok' => false, 'message' => 'Pesanan bukan tipe ambil di tempat.'], 422);
        }

        $validated = $request->validate([
            'pickup_address' => ['required', 'string', 'max:255'],
            'pickup_contact_name' => ['nullable', 'string', 'max:100'],
            'pickup_contact_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]{8,20}$/'],
        ], [
            'pickup_contact_phone.regex' => 'Nomor kontak hanya boleh angka (format internasional, mis. 6281234567890).',
        ]);

        $order->update($validated);

        return response()->json(['ok' => true, 'message' => 'Info pengambilan tersimpan.']);
    }

    public function destroy(Request $request, Order $order)
    {
        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'ok' => true,
            'message' => 'Pesanan dibatalkan.',
            'stats' => $this->statsFor($request->input('filter_category')),
            'stockHtml' => $this->stockCardsHtml(),
        ]);
    }

    public function syncPayment(Request $request, Order $order)
    {
        if ($order->status === 'cancelled') {
            return response()->json(['ok' => false, 'message' => 'Pesanan sudah dibatalkan.'], 422);
        }

        $validated = $request->validate([
            'amount_due' => ['required', 'integer', 'min:1', 'max:'.(int) $order->subtotal],
        ]);

        $order->update(['amount_due' => $validated['amount_due']]);

        return response()->json([
            'ok' => true,
            'message' => 'Total pembayaran diperbarui.',
            'stats' => $this->statsFor($request->input('filter_category')),
            'order' => [
                'order_id' => $order->order_id,
                'amount_due' => (int) $order->amount_due,
                'subtotal' => (int) $order->subtotal,
                'remaining' => max(0, (int) $order->subtotal - (int) $order->amount_due),
            ],
        ]);
    }

    public function uploadPaymentProof(Request $request, Order $order)
    {
        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'type' => ['nullable', 'in:payment,settlement'],
        ]);

        $type = $validated['type'] ?? 'payment';

        // Bukti pelunasan hanya berlaku untuk pesanan DP.
        if ($type === 'settlement') {
            if ($order->payment_type !== 'dp') {
                return response()->json(['ok' => false, 'message' => 'Pesanan bukan tipe DP.'], 422);
            }

            $replacing = (bool) $order->dp_settlement_proof;
            if ($replacing) {
                Storage::disk('public')->delete($order->dp_settlement_proof);
            }

            $path = $request->file('proof')->store('proofs/settlements', 'public');
            $order->update([
                'dp_settlement_proof' => $path,
                'dp_settlement_uploaded_at' => $order->dp_settlement_uploaded_at ?? now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => $replacing ? 'Bukti pelunasan berhasil diganti.' : 'Bukti pelunasan berhasil ditambahkan.',
                'stats' => $this->statsFor($request->input('filter_category')),
            ]);
        }

        // Bukti transfer awal / pembayaran.
        $replacing = (bool) $order->payment_proof;
        if ($replacing) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('proof')->store('proofs', 'public');
        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => $replacing ? 'Bukti transfer berhasil diganti.' : 'Bukti transfer berhasil ditambahkan.',
            'stats' => $this->statsFor($request->input('filter_category')),
        ]);
    }

    public function uploadDpProof(Request $request, Order $order)
    {
        if ($order->payment_type !== 'dp') {
            return response()->json(['ok' => false, 'message' => 'Pesanan bukan tipe DP.'], 422);
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        if ($order->dp_settlement_proof) {
            Storage::disk('public')->delete($order->dp_settlement_proof);
        }

        $path = $validated['proof']->store('proofs/settlements', 'public');
        $update = [
            'dp_settlement_proof' => $path,
            'dp_settlement_uploaded_at' => $order->dp_settlement_uploaded_at ?? now(),
            'dp_settlement_verified_at' => now(),
        ];
        // Pelunasan terverifikasi → naikkan status ke 'paid' (lunas).
        if ($order->status === 'verified') {
            $update['status'] = 'paid';
        }
        $order->update($update);

        return response()->json([
            'ok' => true,
            'message' => 'Bukti pelunasan tersimpan & terverifikasi.',
            'stats' => $this->statsFor($request->input('filter_category')),
            'stockHtml' => $this->stockCardsHtml(),
        ]);
    }

    public function verifySettlement(Request $request, Order $order)
    {
        if ($order->payment_type !== 'dp') {
            return response()->json(['ok' => false, 'message' => 'Pesanan bukan tipe DP.'], 422);
        }

        if (empty($order->dp_settlement_proof)) {
            return response()->json(['ok' => false, 'message' => 'Belum ada bukti pelunasan untuk diverifikasi.'], 422);
        }

        if ($order->dp_settlement_verified_at) {
            return response()->json(['ok' => false, 'message' => 'Pelunasan sudah diverifikasi.'], 422);
        }

        $update = ['dp_settlement_verified_at' => now()];
        // Pelunasan terverifikasi → naikkan status ke 'paid' (lunas).
        if ($order->status === 'verified') {
            $update['status'] = 'paid';
            $order->verified_at = $order->verified_at ?? now();
        }
        $order->update($update);

        return response()->json([
            'ok' => true,
            'message' => 'Pelunasan DP terverifikasi.',
            'stats' => $this->statsFor($request->input('filter_category')),
            'stockHtml' => $this->stockCardsHtml(),
        ]);
    }
}
