<?php

namespace App\Http\Controllers;

use App\Mail\OrderInvoice;
use App\Models\Order;
use App\Support\OrderPresenter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('pages.admin.dashboard', [
            'title' => 'Beranda',
            'stats' => $this->orderStats(),
            'stockCards' => $this->stockCards(),
        ]);
    }

    private function stockCards(): array
    {
        $catalog = [
            'the-7ourney' => ['name' => 'THE 7OURNEY', 'limit' => self::MERCH_STOCK_LIMITS['the-7ourney'] ?? 0],
        ];

        $cards = [];
        foreach ($catalog as $slug => $meta) {
            $sold = $this->countSoldForSlug($slug);
            $limit = (int) $meta['limit'];
            $remaining = $limit > 0 ? max(0, $limit - $sold) : null;
            $progress = $limit > 0 ? min(100, (int) round(($sold / $limit) * 100)) : 0;
            $cards[] = [
                'slug' => $slug,
                'name' => $meta['name'],
                'sold' => $sold,
                'limit' => $limit,
                'remaining' => $remaining,
                'progress' => $progress,
            ];
        }

        return $cards;
    }

    private function stockCardsHtml(): string
    {
        return view('pages.admin._partials.stock-cards', ['stockCards' => $this->stockCards()])->render();
    }

    private function orderStats(): array
    {
        $confirmed = ['verified', 'paid', 'shipped', 'completed'];

        return [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'verified' => Order::where('status', 'verified')->count(),
            'paid' => Order::where('status', 'paid')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'confirmed' => Order::whereIn('status', $confirmed)->count(),
            'settled' => Order::whereIn('status', ['paid', 'shipped', 'completed'])->count(),
            'revenue' => Order::whereIn('status', $confirmed)->sum('amount_due'),
        ];
    }

    public function ordersData(Request $request)
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

    private function applyOrderFilters($query, Request $request)
    {
        $paymentType = $request->input('filter_payment_type');
        if (in_array($paymentType, ['dp', 'full'], true)) {
            $query->where('payment_type', $paymentType);
        }

        $statusFilter = $request->input('filter_status');
        if (in_array($statusFilter, ['pending', 'verified', 'paid', 'shipped', 'completed', 'cancelled'], true)) {
            $query->where('status', $statusFilter);
        }

        $dateFrom = $request->input('filter_date_from');
        $dateTo = $request->input('filter_date_to');
        if ($dateFrom) {
            try {
                $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            } catch (\Throwable $e) {
            }
        }
        if ($dateTo) {
            try {
                $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            } catch (\Throwable $e) {
            }
        }

        return $query;
    }

    public function exportOrders(Request $request): StreamedResponse
    {
        $orders = $this->applyOrderFilters(Order::query(), $request)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusLabels = [
            'pending' => 'Menunggu Pembayaran',
            'verified' => 'Pembayaran Diterima',
            'paid' => 'Pembayaran Lunas',
            'shipped' => 'Pesanan Dikirim',
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

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,verified,paid,shipped,completed,cancelled'],
            'tracking' => ['nullable', 'string', 'max:100'],
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

        $prev = $order->status;

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

        // Email transaksional sesuai status baru.
        if ($next === 'verified' && $prev !== 'verified' && $order->payment_type === 'dp') {
            // DP diterima → ajakan pelunasan.
            $this->sendOrderMail($order, 'dp-verified');
        } elseif ($next === 'paid' && $prev !== 'paid') {
            // Lunas: full payment → invoice; DP (manual/terverifikasi) → konfirmasi lunas.
            $this->sendOrderMail($order, $order->payment_type === 'full' ? 'invoice' : 'settlement-verified');
        } elseif ($next === 'shipped' && $prev !== 'shipped') {
            // Dikirim (kurir) → kirim resi; pickup → info lokasi & kontak pengurus.
            $this->sendOrderMail($order, $order->shipping_method === 'pickup' ? 'pickup-ready' : 'shipped');
        }

        return response()->json([
            'ok' => true,
            'message' => $manualSettlement ? 'Pesanan ditandai lunas.' : 'Status pesanan diperbarui.',
            'stats' => $this->orderStats(),
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

    public function destroyOrder(Order $order)
    {
        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'ok' => true,
            'message' => 'Pesanan dibatalkan.',
            'stats' => $this->orderStats(),
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
            'stats' => $this->orderStats(),
            'order' => [
                'order_id' => $order->order_id,
                'amount_due' => (int) $order->amount_due,
                'subtotal' => (int) $order->subtotal,
                'remaining' => max(0, (int) $order->subtotal - (int) $order->amount_due),
            ],
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
        $alreadyVerified = (bool) $order->dp_settlement_verified_at;
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

        if (! $alreadyVerified) {
            $this->sendOrderMail($order, 'settlement-verified');
        }

        return response()->json([
            'ok' => true,
            'message' => 'Bukti pelunasan tersimpan & terverifikasi.',
            'stats' => $this->orderStats(),
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

        $this->sendOrderMail($order, 'settlement-verified');

        return response()->json([
            'ok' => true,
            'message' => 'Pelunasan DP terverifikasi.',
            'stats' => $this->orderStats(),
            'stockHtml' => $this->stockCardsHtml(),
        ]);
    }

    private function sendOrderMail(Order $order, string $mode): void
    {
        try {
            Mail::to($order->customer_email)
                ->send(new OrderInvoice(OrderPresenter::mailData($order), $mode));
        } catch (\Throwable $e) {
            Log::error('Failed to send order mail', [
                'order_id' => $order->order_id,
                'mode' => $mode,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function showOrder(Order $order)
    {
        return response()->json([
            'ok' => true,
            'html' => $this->renderDetail($order),
            'title' => 'Detail Pesanan '.$order->order_id,
        ]);
    }

    private function serializeOrder(Order $order): array
    {
        $statusMeta = $this->statusMeta($order->status, $order->shipping_method);
        $payment = $this->paymentLabel($order);

        $amountHtml = '<div class="font-bold text-foreground">Rp'.number_format($order->amount_due, 0, ',', '.').'</div>';
        if ($order->payment_type === 'dp') {
            $amountHtml .= '<div class="text-[10px] text-onyx">DP dari Rp'.number_format($order->subtotal, 0, ',', '.').'</div>';
        }

        $createdAt = $order->created_at;
        $dateLabel = $createdAt
            ? $createdAt->locale('id')->translatedFormat('d F Y')
            : '-';
        $timeLabel = $createdAt ? $createdAt->format('H:i').' WIB' : '';

        $itemCount = collect($order->item ?? [])->sum(fn ($l) => (int) ($l['qty'] ?? 0));
        $itemHtml = '<span class="inline-flex items-center justify-center rounded-md bg-skull px-2 py-0.5 text-[12px] font-bold text-foreground ring-1 ring-mercury">'.$itemCount.'×</span>';

        return [
            'order_id' => '<button type="button" data-action="detail" data-order="'.e($order->order_id).'" class="order-id-link font-mono text-[13px] font-semibold text-primary hover:underline focus:outline-none focus-visible:underline">'.e($order->order_id).'</button>',
            'customer' => '<div class="text-[13px] font-semibold text-foreground">'.e($order->customer_name).'</div>',
            'item_count' => $itemHtml,
            'payment' => '<div class="inline-flex items-center gap-1.5 rounded-lg bg-skull px-2.5 py-1 text-[12px] font-semibold text-foreground ring-1 ring-mercury">'
                .'<i class="'.$payment['icon'].' text-'.$payment['color'].'"></i>'
                .e($payment['label'])
                .'</div>',
            'amount' => $amountHtml,
            'status' => '<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[12px] font-bold '.$statusMeta['class'].'">'
                .'<i class="'.$statusMeta['icon'].'"></i> '.$statusMeta['label']
                .'</span>',
            'created_at' => '<div class="text-[13px] font-medium text-foreground">'.e($dateLabel).'</div>'
                .($timeLabel ? '<div class="text-[11px] text-onyx">'.e($timeLabel).'</div>' : ''),
            'actions' => $this->renderActions($order),
        ];
    }

    private function statusMeta(string $status, ?string $shippingMethod = null): array
    {
        $shippedLabel = $shippingMethod === 'pickup' ? 'Pesanan Siap Diambil' : 'Pesanan Dikirim';

        return [
            'pending' => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'ri-time-line'],
            'verified' => ['label' => 'Pembayaran Diterima', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'ri-shield-check-line'],
            'paid' => ['label' => 'Pembayaran Lunas', 'class' => 'bg-teal-100 text-teal-700', 'icon' => 'ri-money-dollar-circle-line'],
            'shipped' => ['label' => $shippedLabel, 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'ri-truck-line'],
            'completed' => ['label' => 'Pesanan Selesai', 'class' => 'bg-sky-100 text-sky-700', 'icon' => 'ri-flag-line'],
            'cancelled' => ['label' => 'Pesanan Dibatalkan', 'class' => 'bg-red-100 text-red-700', 'icon' => 'ri-close-circle-line'],
        ][$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'ri-question-line'];
    }

    private function paymentLabel(Order $order): array
    {
        if ($order->payment_method_type === 'bank') {
            $name = $order->payment_data['label'] ?? 'Transfer Bank';

            return ['label' => 'Transfer Bank · '.$name, 'icon' => 'ri-bank-line', 'color' => 'blue-600'];
        }
        if ($order->payment_method_type === 'qris') {
            return ['label' => 'QRIS', 'icon' => 'ri-qr-code-line', 'color' => 'primary'];
        }

        return ['label' => ucfirst($order->payment_method_type ?? '-'), 'icon' => 'ri-wallet-3-line', 'color' => 'onyx'];
    }

    private const MERCH_STOCK_LIMITS = [
        'the-7ourney' => 300,
    ];

    private function findDuplicatePendingOrders(Order $order): Collection
    {
        $email = trim((string) $order->customer_email);
        $phone = preg_replace('/\D+/', '', (string) $order->customer_phone);

        if ($email === '' && $phone === '') {
            return collect();
        }

        return Order::where('id', '!=', $order->id)
            ->where('status', 'pending')
            ->whereNull('payment_proof')
            ->where(function ($q) use ($email, $phone) {
                if ($email !== '') {
                    $q->orWhere('customer_email', $email);
                }
                if ($phone !== '') {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(customer_phone, ' ', ''), '-', ''), '+', '') LIKE ?", ['%'.$phone.'%']);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function countSoldForSlug(string $slug): int
    {
        $sold = 0;
        Order::whereIn('status', ['verified', 'paid', 'shipped', 'completed'])
            ->select(['item'])
            ->chunk(200, function ($orders) use (&$sold, $slug) {
                foreach ($orders as $order) {
                    foreach ($order->item ?? [] as $line) {
                        if (($line['slug'] ?? null) === $slug) {
                            $sold += (int) ($line['qty'] ?? 0);
                        }
                    }
                }
            });

        return $sold;
    }

    private function renderDetail(Order $order): string
    {
        $statusMeta = $this->statusMeta($order->status, $order->shipping_method);
        $payment = $this->paymentLabel($order);
        $rupiah = fn ($n) => 'Rp'.number_format((int) $n, 0, ',', '.');

        $createdAt = $order->created_at;
        $dateText = $createdAt
            ? $createdAt->locale('id')->translatedFormat('d F Y').' · '.$createdAt->format('H:i').' WIB'
            : '-';

        $initials = collect(preg_split('/\s+/', trim($order->customer_name)))
            ->filter()
            ->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode('');
        $initials = $initials ?: 'JP';

        $shipIcon = $order->shipping_method === 'pickup' ? 'ri-store-2-line' : 'ri-truck-line';
        $shipLabel = $order->shipping_method === 'pickup' ? 'Ambil di Tempat' : 'Dikirim';

        $rawPhone = preg_replace('/\D+/', '', (string) $order->customer_phone);
        $waPhone = $rawPhone !== '' ? (str_starts_with($rawPhone, '0') ? '62'.substr($rawPhone, 1) : (str_starts_with($rawPhone, '62') ? $rawPhone : '62'.$rawPhone)) : '';
        $phoneDisplay = $rawPhone !== '' ? '+62'.ltrim(preg_replace('/^62/', '', $rawPhone), '0') : '-';

        // Items
        $itemsHtml = '';
        foreach ($order->item ?? [] as $line) {
            $qty = (int) ($line['qty'] ?? 0);
            $price = (int) ($line['price'] ?? 0);
            $fee = (int) ($line['fee'] ?? 0);
            $lineTotal = ($price + $fee) * $qty;
            $variant = trim(implode(' · ', array_filter([$line['category'] ?? '', $line['sleeve'] ?? '', $line['size'] ?? ''])));

            $itemsHtml .= '<div class="flex items-center gap-3 rounded-xl border border-mercury bg-white px-3 py-2.5">'
                .'<div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft">'
                .'<i class="ri-shirt-line text-xl"></i>'
                .'</div>'
                .'<div class="min-w-0 flex-1">'
                .'<div class="truncate text-[13px] font-bold text-foreground">'.e($line['name'] ?? '-').'</div>'
                .'<div class="mt-0.5 text-[11px] text-onyx">'.e($variant).'</div>'
                .'</div>'
                .'<div class="text-right">'
                .'<div class="text-[11px] text-onyx">'.$qty.'× · '.$rupiah($price + $fee).'</div>'
                .'<div class="text-[13px] font-bold text-foreground">'.$rupiah($lineTotal).'</div>'
                .'</div>'
                .'</div>';
        }

        // Proof chips
        $proofs = [];
        if ($order->payment_proof) {
            $uploadedAt = $order->payment_proof_uploaded_at;
            $uploadedLabel = $uploadedAt
                ? $uploadedAt->locale('id')->translatedFormat('d M Y · H:i').' WIB'
                : null;
            $proofs[] = '<a href="'.e(asset('storage/'.$order->payment_proof)).'" target="_blank" class="detail-chip detail-chip--primary"><i class="ri-image-line"></i> Bukti Pembayaran <i class="ri-external-link-line text-[10px]"></i></a>'
                .($uploadedLabel ? '<span class="inline-flex items-center gap-1 text-[11px] text-onyx"><i class="ri-time-line text-[12px]"></i> '.e($uploadedLabel).'</span>' : '');
        }
        if ($order->payment_type === 'dp' && $order->dp_settlement_proof) {
            $proofs[] = '<a href="'.e(asset('storage/'.$order->dp_settlement_proof)).'" target="_blank" class="detail-chip detail-chip--success"><i class="ri-checkbox-circle-line"></i> Bukti Pelunasan DP <i class="ri-external-link-line text-[10px]"></i></a>';
        }
        if (empty($proofs)) {
            $proofs[] = '<span class="detail-chip detail-chip--muted"><i class="ri-image-add-line"></i> Belum ada bukti</span>';
        }
        $proofHtml = implode('', $proofs);

        // Field row helper
        $field = fn ($label, $value, $icon = null) => '<div class="flex gap-3">'
            .($icon ? '<div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-skull text-onyx ring-1 ring-mercury"><i class="'.e($icon).' text-base"></i></div>' : '')
            .'<div class="min-w-0 flex-1">'
            .'<div class="text-[10px] font-bold uppercase tracking-wider text-onyx">'.e($label).'</div>'
            .'<div class="mt-0.5 text-[13px] font-medium text-foreground break-words">'.$value.'</div>'
            .'</div>'
            .'</div>';

        // Pengiriman block (kirim vs pickup specifics)
        $pickupLocations = [
            'purwokerto' => 'Purwokerto',
            'ajibarang' => 'Ajibarang',
            'jakarta' => 'Jakarta',
        ];

        if ($order->shipping_method === 'kirim') {
            $resiValue = $order->shipping_tracking
                ? '<span class="font-mono font-bold text-foreground">'.e($order->shipping_tracking).'</span>'
                : '<span class="text-amber-700">Belum ada resi</span>';
            $shipAddrHtml = $field('Alamat Pengiriman', nl2br(e($order->customer_address ?? '-')), 'ri-map-pin-2-line')
                .$field('Nomor Resi (JNT)', $resiValue, 'ri-barcode-line');
            $shipNoteHtml = $field('Kurir', 'JNT Express (ongkos kirim ditanggung pembeli)', 'ri-truck-line');
        } else {
            $cityKey = $order->pickup_location ?? '';
            $cityName = $pickupLocations[$cityKey] ?? ucfirst($cityKey ?: '-');
            $shipAddrHtml = $field('Kota Pengambilan', e($cityName), 'ri-map-pin-2-line');
            $shipNoteHtml = $field('Catatan', 'Titik temu & jadwal pengambilan akan dikonfirmasi admin melalui WhatsApp.', 'ri-information-2-line');
        }

        // ===== Build HTML =====
        return '<div class="detail-modal text-left" data-order="'.e($order->order_id).'">'

            // Hero header
            .'<div class="detail-hero">'
            .'<div class="detail-hero__bg"></div>'
            .'<div class="relative flex flex-col gap-4 pr-12">'
            .'<div class="flex flex-wrap items-center gap-2">'
            .'<span class="detail-chip detail-chip--glass">'.e($order->order_id).'</span>'
            .'<span class="detail-chip detail-chip--status '.$statusMeta['class'].'"><i class="'.$statusMeta['icon'].'"></i> '.$statusMeta['label'].'</span>'
            .'</div>'
            .'<div class="flex items-center gap-3">'
            .'<div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-lg font-black text-primary shadow-md">'.e($initials).'</div>'
            .'<div class="min-w-0 flex-1 text-white">'
            .'<div class="truncate text-base font-black leading-tight">'.e($order->customer_name).'</div>'
            .'<div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-[11px] text-white/85">'
            .'<span class="inline-flex items-center gap-1"><i class="ri-calendar-line"></i> '.e($dateText).'</span>'
            .'<span class="inline-flex items-center gap-1"><i class="'.$shipIcon.'"></i> '.e($shipLabel).'</span>'
            .'</div>'
            .'</div>'
            .'</div>'
            .'</div>'
            .'</div>'

            // Duplicate pending orders warning (above customer/shipping)
            .$this->renderDuplicateCard($order)

            // Two-column: contact + shipping
            .'<div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">'
            .'<div class="detail-card">'
            .'<div class="detail-card__title"><i class="ri-user-3-line"></i> Pelanggan</div>'
            .'<div class="space-y-2.5">'
            .$field('Email', '<span class="break-all text-foreground">'.e($order->customer_email).'</span>', 'ri-mail-line')
            .$field('Telepon',
                '<div class="flex flex-wrap items-center gap-2">'
                .'<span>'.e($phoneDisplay).'</span>'
                .($waPhone !== ''
                    ? '<a href="https://wa.me/'.e($waPhone).'" target="_blank" rel="noopener" class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 hover:bg-emerald-100 transition"><i class="ri-whatsapp-line text-[12px]"></i> Follow up</a>'
                    : '')
                .'</div>',
                'ri-phone-line')
            .'</div>'
            .'</div>'

            .'<div class="detail-card">'
            .'<div class="detail-card__title"><i class="'.$shipIcon.'"></i> Pengiriman</div>'
            .'<div class="space-y-2.5">'
            .$shipAddrHtml
            .$shipNoteHtml
            .'</div>'
            .'</div>'
            .'</div>'

            // Items
            .'<div class="detail-card mt-3">'
            .'<div class="detail-card__title"><i class="ri-shopping-bag-3-line"></i> Item Pesanan</div>'
            .'<div class="flex flex-col gap-2">'.$itemsHtml.'</div>'
            .'<div class="mt-3 flex items-center justify-between border-t border-mercury pt-2.5">'
            .'<span class="text-[11px] font-semibold uppercase tracking-wider text-onyx">Subtotal</span>'
            .'<span class="text-base font-black text-foreground">'.$rupiah($order->subtotal).'</span>'
            .'</div>'
            .'</div>'

            // Payment summary
            .'<div class="detail-card mt-3">'
            .'<div class="detail-card__title"><i class="ri-wallet-3-line"></i> Pembayaran</div>'
            .'<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">'
            .'<div class="rounded-xl bg-skull/70 p-3 ring-1 ring-mercury">'
            .'<div class="text-[10px] font-bold uppercase tracking-wider text-onyx">Metode</div>'
            .'<div class="mt-1 inline-flex items-center gap-1.5 text-[13px] font-bold text-foreground"><i class="'.$payment['icon'].' text-'.$payment['color'].'"></i> '.e($payment['label']).'</div>'
            .'</div>'
            .'<div class="rounded-xl bg-skull/70 p-3 ring-1 ring-mercury">'
            .'<div class="text-[10px] font-bold uppercase tracking-wider text-onyx">Tipe</div>'
            .'<div class="mt-1 text-[13px] font-bold text-foreground">'.($order->payment_type === 'dp' ? 'DP (50%)' : 'Bayar Lunas').'</div>'
            .'</div>'
            .'<div class="rounded-xl bg-linear-to-br from-primary via-primary-light to-primary-lighter p-3 text-white shadow-sm">'
            .'<div class="text-[10px] font-bold uppercase tracking-wider text-white/80">Sudah Dibayar</div>'
            .'<div class="mt-1 text-base font-black">'.$rupiah($order->amount_due).'</div>'
            .($order->payment_type === 'dp' ? '<div class="text-[10px] text-white/80">dari total '.$rupiah($order->subtotal).'</div>' : '')
            .'</div>'
            .'</div>'
            .($order->payment_type === 'dp' ? $this->renderSettlementBlock($order, $rupiah) : '')
            .'<div class="mt-3 flex flex-wrap items-center gap-2">'.$proofHtml.'</div>'
            .'</div>'

            .'</div>';
    }

    private function renderDuplicateCard(Order $order): string
    {
        $dupes = $this->findDuplicatePendingOrders($order);
        if ($dupes->isEmpty()) {
            return '';
        }

        $itemsHtml = '';
        foreach ($dupes as $d) {
            $created = $d->created_at
                ? $d->created_at->locale('id')->translatedFormat('d M Y · H:i').' WIB'
                : '-';
            $itemsHtml .= '<div class="flex items-center justify-between gap-2 rounded-lg bg-white px-3 py-2 ring-1 ring-amber-200">'
                .'<div class="min-w-0">'
                .'<div class="font-mono text-[12px] font-bold text-foreground">'.e($d->order_id).'</div>'
                .'<div class="text-[11px] text-onyx">'.e($created).' · Rp'.number_format((int) $d->amount_due, 0, ',', '.').'</div>'
                .'</div>'
                .'<div class="flex items-center gap-1.5">'
                .'<button type="button" data-action="detail" data-order="'.e($d->order_id).'" class="inline-flex items-center gap-1 rounded-md bg-amber-100 px-2 py-1 text-[11px] font-bold text-amber-700 hover:bg-amber-200"><i class="ri-eye-line"></i> Lihat</button>'
                .'<button type="button" data-action="delete" data-order="'.e($d->order_id).'" class="inline-flex items-center gap-1 rounded-md bg-red-100 px-2 py-1 text-[11px] font-bold text-red-700 hover:bg-red-200"><i class="ri-close-circle-line"></i> Batalkan</button>'
                .'</div>'
                .'</div>';
        }

        return '<div class="detail-card mt-3 border-amber-200 bg-amber-50/40">'
            .'<div class="detail-card__title text-amber-800"><i class="ri-error-warning-line"></i> Kemungkinan Pesanan Duplikat ('.$dupes->count().')</div>'
            .'<div class="text-[12px] text-amber-800/80">Pelanggan ini punya pesanan lain yang masih menunggu pembayaran tanpa bukti. Batalkan yang tidak terpakai.</div>'
            .'<div class="mt-2 flex flex-col gap-2">'.$itemsHtml.'</div>'
            .'</div>';
    }

    private function renderSettlementBlock(Order $order, callable $rupiah): string
    {
        $remaining = max(0, (int) $order->subtotal - (int) $order->amount_due);
        $verified = (bool) $order->dp_settlement_verified_at;
        $hasProof = (bool) $order->dp_settlement_proof;

        if ($verified) {
            $label = 'Pelunasan terverifikasi';
            $valueHtml = '<span class="text-[14px] font-black text-emerald-700"><i class="ri-checkbox-circle-fill"></i> Lunas</span>';
            $box = 'border-emerald-300 bg-emerald-50';
            $icon = '<i class="ri-checkbox-circle-line text-base text-emerald-600"></i>';
            $textColor = 'text-emerald-800';
            $action = '';
        } elseif ($hasProof) {
            $label = 'Menunggu verifikasi pelunasan';
            $valueHtml = '<span class="text-[14px] font-black text-amber-700">'.$rupiah($remaining).'</span>';
            $box = 'border-amber-300 bg-amber-50';
            $icon = '<i class="ri-time-line text-base text-amber-600"></i>';
            $textColor = 'text-amber-800';
            $action = '<button type="button" data-action="settlement-verify" data-order="'.e($order->order_id).'" class="mt-2.5 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-[12px] font-bold text-white shadow-sm transition active:scale-95 hover:bg-emerald-700"><i class="ri-shield-check-line"></i> Verifikasi Pelunasan</button>';
        } else {
            $label = 'Menunggu pelunasan pembeli';
            $valueHtml = '<span class="text-[14px] font-black text-amber-700">'.$rupiah($remaining).'</span>';
            $box = 'border-amber-300 bg-amber-50';
            $icon = '<i class="ri-alarm-warning-line text-base text-amber-600"></i>';
            $textColor = 'text-amber-800';
            $action = '';
        }

        return '<div class="mt-3 rounded-xl border-2 border-dashed '.$box.' px-4 py-3">'
            .'<div class="flex items-center justify-between">'
            .'<div class="inline-flex items-center gap-2 '.$textColor.'">'.$icon
            .'<span class="text-[12px] font-semibold">'.$label.'</span>'
            .'</div>'.$valueHtml
            .'</div>'.$action
            .'</div>';
    }

    private function renderActions(Order $order): string
    {
        $orderId = e($order->order_id);

        $items = '<button type="button" role="menuitem" data-action="detail" data-order="'.$orderId.'" class="dropdown-item">'
            .'<i class="ri-eye-line"></i><span>Lihat Detail</span>'
            .'</button>';

        if ($order->status === 'pending') {
            $items .= '<button type="button" role="menuitem" data-action="status" data-status="verified" data-order="'.$orderId.'" class="dropdown-item dropdown-item--success">'
                .'<i class="ri-shield-check-line"></i><span>Pembayaran Diterima</span>'
                .'</button>';
        }

        // Verifikasi bukti pelunasan dari pembeli (self-service).
        if ($order->payment_type === 'dp' && $order->dp_settlement_proof && ! $order->dp_settlement_verified_at
            && $order->status === 'verified') {
            $items .= '<button type="button" role="menuitem" data-action="settlement-verify" data-order="'.$orderId.'" class="dropdown-item dropdown-item--settle">'
                .'<i class="ri-verified-badge-line"></i><span>Verifikasi Pelunasan</span>'
                .'</button>';
        }

        // Tandai lunas manual (DP tanpa bukti dari pembeli).
        if ($order->payment_type === 'dp' && ! $order->dp_settlement_proof && ! $order->dp_settlement_verified_at
            && $order->status === 'verified') {
            $items .= '<button type="button" role="menuitem" data-action="status" data-status="paid" data-order="'.$orderId.'" class="dropdown-item dropdown-item--settle">'
                .'<i class="ri-money-dollar-circle-line"></i><span>Tandai Lunas</span>'
                .'</button>';
        }

        // Koreksi nomor resi setelah pesanan kirim ditandai dikirim.
        if ($order->shipping_method === 'kirim' && $order->status === 'shipped') {
            $items .= '<button type="button" role="menuitem" data-action="shipping" data-mode="edit" data-order="'.$orderId.'" data-tracking="'.e($order->shipping_tracking ?? '').'" class="dropdown-item dropdown-item--resi">'
                .'<i class="ri-barcode-line"></i><span>Ubah Nomor Resi</span>'
                .'</button>';
        }

        if ($order->status === 'paid') {
            if ($order->shipping_method === 'pickup') {
                $items .= '<button type="button" role="menuitem" data-action="status" data-status="shipped" data-order="'.$orderId.'" class="dropdown-item dropdown-item--ship">'
                    .'<i class="ri-truck-line"></i><span>Tandai Siap Diambil</span>'
                    .'</button>';
            } else {
                // Kirim: buka modal resi → simpan resi + tandai dikirim sekaligus.
                $items .= '<button type="button" role="menuitem" data-action="shipping" data-mode="ship" data-order="'.$orderId.'" data-tracking="'.e($order->shipping_tracking ?? '').'" class="dropdown-item dropdown-item--ship">'
                    .'<i class="ri-truck-line"></i><span>Tandai Dikirim</span>'
                    .'</button>';
            }
        }

        if ($order->status === 'shipped') {
            $items .= '<button type="button" role="menuitem" data-action="status" data-status="completed" data-order="'.$orderId.'" class="dropdown-item dropdown-item--complete">'
                .'<i class="ri-flag-line"></i><span>Tandai Selesai</span>'
                .'</button>';
        }

        if ($order->status !== 'cancelled' && $order->status !== 'completed') {
            $items .= '<button type="button" role="menuitem" data-action="sync-payment" data-order="'.$orderId.'" data-subtotal="'.(int) $order->subtotal.'" data-amount-due="'.(int) $order->amount_due.'" class="dropdown-item dropdown-item--warning">'
                .'<i class="ri-refresh-line"></i><span>Sinkronisasi Pembayaran</span>'
                .'</button>';
        }

        if ($order->status !== 'cancelled') {
            $items .= '<button type="button" role="menuitem" data-action="delete" data-order="'.$orderId.'" class="dropdown-item dropdown-item--danger">'
                .'<i class="ri-close-circle-line"></i><span>Batalkan Pesanan</span>'
                .'</button>';
        }

        return '<div class="orders-dropdown" data-dropdown>'
            .'<button type="button" class="dropdown-trigger" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">'
            .'<i class="ri-more-2-fill"></i>'
            .'</button>'
            .'<div class="dropdown-menu" role="menu">'
            .$items
            .'</div>'
            .'</div>';
    }
}
