<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Order;
use App\Models\OrderTicket;
use App\Models\PickupLocation;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Controller extends BaseController
{
    protected const MERCH_STOCK_LIMITS = [
        'the-7ourney' => 300,
    ];

    protected const CHECKIN_ELIGIBLE_STATUSES = ['verified', 'paid', 'shipped', 'completed'];

    protected function isTicketOrder(Order $order): bool
    {
        $items = collect($order->item ?? []);

        return $items->isNotEmpty() && $items->every(fn ($line) => ($line['category'] ?? null) === 'Tiket');
    }

    protected function ticketUnitCount(Order $order): int
    {
        return collect($order->item ?? [])
            ->filter(fn ($line) => ($line['category'] ?? null) === 'Tiket')
            ->sum(fn ($line) => (int) ($line['qty'] ?? 0));
    }

    protected function ensureCheckinCodes(Order $order): void
    {
        if (! $this->isTicketOrder($order)) {
            return;
        }

        if (! in_array($order->status, self::CHECKIN_ELIGIBLE_STATUSES, true)) {
            return;
        }

        $needed = $this->ticketUnitCount($order);
        $existing = $order->tickets()->count();

        for ($unitIndex = $existing + 1; $unitIndex <= $needed; $unitIndex++) {
            do {
                $code = strtoupper(Str::random(10));
            } while (OrderTicket::where('code', $code)->exists());

            $order->tickets()->create([
                'code' => $code,
                'unit_index' => $unitIndex,
            ]);
        }
    }

    protected function generateQrDataUri(string $data, int $size = 220): string
    {
        $qrCode = new QrCode(data: $data, size: $size, margin: 8);

        return (new PngWriter)->write($qrCode)->getDataUri();
    }

    protected function stockCards(): array
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

    protected function stockCardsHtml(): string
    {
        return view('pages.admin._partials.stock-cards', ['stockCards' => $this->stockCards()])->render();
    }

    protected function orderStats(): array
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

    protected function ticketStats(): array
    {
        $base = Order::whereRaw("JSON_SEARCH(item, 'one', 'Tiket', NULL, '$[*].category') IS NOT NULL");
        $confirmed = ['verified', 'paid', 'shipped', 'completed'];

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'verified' => (clone $base)->where('status', 'verified')->count(),
            'paid' => (clone $base)->where('status', 'paid')->count(),
            'shipped' => (clone $base)->where('status', 'shipped')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'confirmed' => (clone $base)->whereIn('status', $confirmed)->count(),
            'settled' => (clone $base)->whereIn('status', ['paid', 'shipped', 'completed'])->count(),
            'revenue' => (clone $base)->whereIn('status', $confirmed)->sum('amount_due'),
        ];
    }

    protected function merchandiseStats(): array
    {
        $base = Order::whereRaw("JSON_SEARCH(item, 'one', 'Tiket', NULL, '$[*].category') IS NULL");
        $confirmed = ['verified', 'paid', 'shipped', 'completed'];

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'verified' => (clone $base)->where('status', 'verified')->count(),
            'paid' => (clone $base)->where('status', 'paid')->count(),
            'shipped' => (clone $base)->where('status', 'shipped')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
            'confirmed' => (clone $base)->whereIn('status', $confirmed)->count(),
            'settled' => (clone $base)->whereIn('status', ['paid', 'shipped', 'completed'])->count(),
            'revenue' => (clone $base)->whereIn('status', $confirmed)->sum('amount_due'),
        ];
    }

    protected function statsFor(?string $category): array
    {
        return match ($category) {
            'Tiket' => $this->ticketStats(),
            'Merchandise' => $this->merchandiseStats(),
            default => $this->orderStats(),
        };
    }

    protected function applyOrderFilters($query, Request $request)
    {
        $paymentType = $request->input('filter_payment_type');
        if (in_array($paymentType, ['dp', 'full'], true)) {
            $query->where('payment_type', $paymentType);
        }

        $statusFilter = $request->input('filter_status');
        if (in_array($statusFilter, ['pending', 'verified', 'paid', 'shipped', 'completed', 'cancelled'], true)) {
            $query->where('status', $statusFilter);
        }

        $shippingFilter = $request->input('filter_shipping_method');
        if (in_array($shippingFilter, ['kirim', 'pickup'], true)) {
            $query->where('shipping_method', $shippingFilter);
        }

        $categoryFilter = $request->input('filter_category');
        if ($categoryFilter === 'Tiket') {
            $query->whereRaw("JSON_SEARCH(item, 'one', 'Tiket', NULL, '$[*].category') IS NOT NULL");
        } elseif ($categoryFilter === 'Merchandise') {
            $query->whereRaw("JSON_SEARCH(item, 'one', 'Tiket', NULL, '$[*].category') IS NULL");
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

    protected function serializeOrder(Order $order): array
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

    protected function statusMeta(string $status, ?string $shippingMethod = null): array
    {
        $shippedLabel = 'Pesanan Dikirim / Siap Diambil';

        return [
            'pending' => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'ri-time-line'],
            'verified' => ['label' => 'Pembayaran Diterima', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'ri-shield-check-line'],
            'paid' => ['label' => 'Pembayaran Lunas', 'class' => 'bg-teal-100 text-teal-700', 'icon' => 'ri-money-dollar-circle-line'],
            'shipped' => ['label' => $shippedLabel, 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'ri-truck-line'],
            'completed' => ['label' => 'Pesanan Selesai', 'class' => 'bg-sky-100 text-sky-700', 'icon' => 'ri-flag-line'],
            'cancelled' => ['label' => 'Pesanan Dibatalkan', 'class' => 'bg-red-100 text-red-700', 'icon' => 'ri-close-circle-line'],
        ][$status] ?? ['label' => ucfirst($status), 'class' => 'bg-gray-100 text-gray-700', 'icon' => 'ri-question-line'];
    }

    protected function paymentLabel(Order $order): array
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

    protected function findDuplicatePendingOrders(Order $order): Collection
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

    protected function countSoldForSlug(string $slug): int
    {
        $sold = 0;
        Order::whereIn('status', ['verified', 'paid', 'shipped', 'completed'])
            ->select(['item'])
            ->chunk(200, function ($orders) use (&$sold, $slug) {
                foreach ($orders as $order) {
                    foreach ($order->item ?? [] as $line) {
                        if (($line['slug'] ?? null) === $slug && ($line['category'] ?? null) !== 'Tiket') {
                            $sold += (int) ($line['qty'] ?? 0);
                        }
                    }
                }
            });

        return $sold;
    }

    protected function renderDetail(Order $order): string
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

        $isTicketOrder = $this->isTicketOrder($order);
        $tickets = $isTicketOrder ? $order->tickets : collect();
        $ticketsCheckedIn = $tickets->filter(fn ($t) => $t->checked_in_at)->count();
        $ticketsAllCheckedIn = $tickets->isNotEmpty() && $ticketsCheckedIn === $tickets->count();

        $rawPhone = preg_replace('/\D+/', '', (string) $order->customer_phone);
        $waPhone = $rawPhone !== '' ? (str_starts_with($rawPhone, '0') ? '62'.substr($rawPhone, 1) : (str_starts_with($rawPhone, '62') ? $rawPhone : '62'.$rawPhone)) : '';
        $phoneDisplay = $rawPhone !== '' ? '+62'.ltrim(preg_replace('/^62/', '', $rawPhone), '0') : '-';
        $itemsHtml = '';

        foreach ($order->item ?? [] as $line) {
            $qty = (int) ($line['qty'] ?? 0);
            $price = (int) ($line['price'] ?? 0);
            $fee = (int) ($line['fee'] ?? 0);
            $lineTotal = ($price + $fee) * $qty;
            $variantParts = array_filter(
                [$line['category'] ?? '', $line['sleeve'] ?? '', $line['size'] ?? ''],
                fn ($v) => $v !== '' && $v !== '-'
            );
            $variant = implode(' · ', array_unique($variantParts));

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

        $proofRows = $this->renderProofRows($order);

        $field = fn ($label, $value, $icon = null) => '<div class="flex gap-3">'
            .($icon ? '<div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-skull text-onyx ring-1 ring-mercury"><i class="'.e($icon).' text-base"></i></div>' : '')
            .'<div class="min-w-0 flex-1">'
            .'<div class="text-[10px] font-bold uppercase tracking-wider text-onyx">'.e($label).'</div>'
            .'<div class="mt-0.5 text-[13px] font-medium text-foreground break-words">'.$value.'</div>'
            .'</div>'
            .'</div>';

        if ($order->shipping_method === 'kirim') {
            $resiValue = $order->shipping_tracking
                ? '<span class="font-mono font-bold text-foreground">'.e($order->shipping_tracking).'</span>'
                : '<span class="text-amber-700">Belum ada resi</span>';
            $shipAddrHtml = $field('Alamat Pengiriman', nl2br(e($order->customer_address ?? '-')), 'ri-map-pin-2-line')
                .$field('Nomor Resi (JNT)', $resiValue, 'ri-barcode-line');
            $shipNoteHtml = $field('Kurir', 'JNT Express (ongkos kirim ditanggung pembeli)', 'ri-truck-line');
        } else {
            $city = PickupLocation::findByKey($order->pickup_location);
            $cityName = $city?->name ?? ucfirst($order->pickup_location ?: '-');

            $addr = (string) ($order->pickup_address ?? '');
            $contactName = (string) ($order->pickup_contact_name ?? '');
            $contactPhone = (string) ($order->pickup_contact_phone ?? '');

            $addrValue = $addr !== ''
                ? nl2br(e($addr))
                : '<span class="text-amber-700">Belum ditentukan</span>';
            $contactValue = $contactPhone !== ''
                ? e(($contactName !== '' ? $contactName.' · ' : '').'+'.$contactPhone)
                : '<span class="text-amber-700">Belum ditentukan</span>';

            $shipAddrHtml = $field('Kota Pengambilan', e($cityName), 'ri-map-pin-2-line')
                .$field('Alamat / Titik Temu', $addrValue, 'ri-navigation-line')
                .$field('Kontak Pengurus', $contactValue, 'ri-user-location-line');
            $shipNoteHtml = $field('Catatan', 'Alamat & kontak pengambilan diatur per pesanan dan dikirim ke pembeli saat ditandai siap diambil.', 'ri-information-2-line');
        }

        return '<div class="detail-modal text-left" data-order="'.e($order->order_id).'">'
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
            .'<div class="mt-3 grid gap-2'.($tickets->isNotEmpty() ? ' grid-cols-2' : ' grid-cols-1').'">'
            .'<a href="'.e(url('admin/orders/'.$order->order_id.'/invoice')).'" target="_blank" class="detail-action">'
            .'<i class="ri-file-pdf-2-line text-lg text-primary"></i>'
            .'<span>Unduh Invoice</span>'
            .'</a>'
            .($tickets->isNotEmpty()
                ? '<div class="detail-action cursor-default hover:border-mercury hover:bg-white hover:text-foreground">'
                    .'<i class="ri-qr-scan-2-line text-lg '.($ticketsAllCheckedIn ? 'text-emerald-600' : 'text-primary').'"></i>'
                    .'<span>'.$ticketsCheckedIn.'/'.$tickets->count().' Check-in</span>'
                    .'</div>'
                : '')
            .'</div>'
            .$this->renderDuplicateCard($order)
            .'<div class="mt-3 grid grid-cols-1 gap-3'.($isTicketOrder ? '' : ' sm:grid-cols-2').'">'
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
            .($isTicketOrder ? '' :
            '<div class="detail-card">'
            .'<div class="detail-card__title"><i class="'.$shipIcon.'"></i> Pengiriman</div>'
            .'<div class="space-y-2.5">'
            .$shipAddrHtml
            .$shipNoteHtml
            .'</div>'
            .'</div>')
            .'</div>'
            .'<div class="detail-card mt-3">'
            .'<div class="detail-card__title"><i class="ri-shopping-bag-3-line"></i> Item Pesanan</div>'
            .'<div class="flex flex-col gap-2">'.$itemsHtml.'</div>'
            .'<div class="mt-3 flex items-center justify-between border-t border-mercury pt-2.5">'
            .'<span class="text-[11px] font-semibold uppercase tracking-wider text-onyx">Subtotal</span>'
            .'<span class="text-base font-black text-foreground">'.$rupiah($order->subtotal).'</span>'
            .'</div>'
            .'</div>'
            .($tickets->isNotEmpty() ? $this->renderTicketCheckinCard($tickets, $ticketsCheckedIn, $ticketsAllCheckedIn) : '')
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
            .$proofRows
            .'</div>'
            .'</div>';
    }

    protected function renderTicketCheckinCard(Collection $tickets, int $checkedIn, bool $allCheckedIn): string
    {
        $total = $tickets->count();
        $percent = $total > 0 ? (int) round(($checkedIn / $total) * 100) : 0;

        $rows = '';
        foreach ($tickets as $ticket) {
            $isChecked = (bool) $ticket->checked_in_at;
            $rows .= '<a href="'.e(route('checkin.index', ['code' => $ticket->code])).'" target="_blank" class="flex items-center justify-between gap-2 rounded-lg bg-skull/40 px-3 py-2 ring-1 ring-mercury transition hover:ring-primary/40">'
                .'<span class="font-mono text-[12px] font-bold text-foreground">Tiket '.$ticket->unit_index.' · '.e($ticket->code).'</span>'
                .'<span class="inline-flex items-center gap-1 text-[11px] font-semibold '.($isChecked ? 'text-emerald-700' : 'text-onyx').'">'
                .'<i class="'.($isChecked ? 'ri-checkbox-circle-fill' : 'ri-time-line').'"></i> '.($isChecked ? 'Sudah Check-in' : 'Belum Check-in')
                .'</span>'
                .'</a>';
        }

        return '<div class="detail-card mt-3">'
            .'<div class="detail-card__title"><i class="ri-qr-scan-2-line"></i> E-Tiket &amp; Check-in</div>'
            .'<div class="flex items-center justify-between gap-3">'
            .'<span class="text-[12px] font-semibold text-foreground">'.$checkedIn.' dari '.$total.' tiket sudah check-in</span>'
            .'<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-bold '.($allCheckedIn ? 'bg-emerald-100 text-emerald-700' : 'bg-skull text-onyx').'">'.$percent.'%</span>'
            .'</div>'
            .'<div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-skull">'
            .'<div class="h-full rounded-full '.($allCheckedIn ? 'bg-emerald-500' : 'bg-primary').'" style="width:'.$percent.'%"></div>'
            .'</div>'
            .'<div class="mt-3 flex flex-col gap-1.5">'
            .$rows
            .'</div>'
            .'</div>';
    }

    protected function renderDuplicateCard(Order $order): string
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

    protected function renderProofRows(Order $order): string
    {
        $row = function (string $type, string $label, ?string $path, $uploadedAt) use ($order): string {
            $has = (bool) $path;

            $uploadedLabel = $uploadedAt
                ? 'Diunggah '.$uploadedAt->locale('id')->translatedFormat('d M Y · H:i').' WIB'
                : 'Sudah diunggah';

            if ($has) {
                $iconWrap = 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200';
                $icon = 'ri-image-2-line';
                $statusHtml = '<div class="mt-0.5 inline-flex items-center gap-1 text-[11px] text-onyx"><i class="ri-checkbox-circle-fill text-[12px] text-emerald-500"></i> '.e($uploadedLabel).'</div>';
            } else {
                $iconWrap = 'bg-amber-50 text-amber-600 ring-1 ring-amber-200';
                $icon = 'ri-image-add-line';
                $statusHtml = '<div class="mt-0.5 inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600"><i class="ri-error-warning-line text-[12px]"></i> Belum ada bukti</div>';
            }

            $viewBtn = $has
                ? '<a href="'.e(asset('storage/'.$path)).'" target="_blank" rel="noopener" class="inline-flex h-8 items-center gap-1 rounded-lg border border-mercury bg-white px-2.5 text-[11px] font-bold text-foreground transition hover:border-primary hover:text-primary"><i class="ri-eye-line text-[13px]"></i> Lihat</a>'
                : '';
            $actionBtn = $has
                ? '<button type="button" data-action="payment-proof" data-type="'.$type.'" data-order="'.e($order->order_id).'" data-has-proof="1" class="inline-flex h-8 items-center gap-1 rounded-lg border border-primary-soft bg-primary-softer px-2.5 text-[11px] font-bold text-primary transition hover:bg-primary-soft"><i class="ri-image-edit-line text-[13px]"></i> Ganti</button>'
                : '<button type="button" data-action="payment-proof" data-type="'.$type.'" data-order="'.e($order->order_id).'" data-has-proof="0" class="inline-flex h-8 items-center gap-1 rounded-lg bg-primary px-2.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-primary-light"><i class="ri-upload-2-line text-[13px]"></i> Tambah</button>';

            return '<div class="flex items-center justify-between gap-3 rounded-xl border border-mercury bg-skull/40 px-3 py-2.5">'
                .'<div class="flex min-w-0 items-center gap-2.5">'
                .'<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg '.$iconWrap.'"><i class="'.$icon.' text-base"></i></div>'
                .'<div class="min-w-0">'
                .'<div class="truncate text-[12px] font-bold text-foreground">'.e($label).'</div>'
                .$statusHtml
                .'</div>'
                .'</div>'
                .'<div class="flex shrink-0 items-center gap-1.5">'.$viewBtn.$actionBtn.'</div>'
                .'</div>';
        };

        $rows = $row(
            'payment',
            $order->payment_type === 'dp' ? 'Bukti Transfer DP' : 'Bukti Transfer',
            $order->payment_proof,
            $order->payment_proof_uploaded_at,
        );

        if ($order->payment_type === 'dp') {
            $rows .= $row(
                'settlement',
                'Bukti Pelunasan DP',
                $order->dp_settlement_proof,
                $order->dp_settlement_uploaded_at,
            );
        }

        return '<div class="mt-3">'
            .'<div class="mb-2 text-[10px] font-bold uppercase tracking-wider text-onyx">Bukti Transfer</div>'
            .'<div class="flex flex-col gap-2">'.$rows.'</div>'
            .'</div>';
    }

    protected function renderSettlementBlock(Order $order, callable $rupiah): string
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

    protected function renderActions(Order $order): string
    {
        $orderId = e($order->order_id);

        $items = '<button type="button" role="menuitem" data-action="detail" data-order="'.$orderId.'" class="dropdown-item">'
            .'<i class="ri-eye-line"></i><span>Lihat Detail</span>'
            .'</button>'
            .'<a href="'.e(url('admin/orders/'.$order->order_id.'/invoice')).'" target="_blank" role="menuitem" class="dropdown-item">'
            .'<i class="ri-file-pdf-2-line"></i><span>Unduh Invoice (PDF)</span>'
            .'</a>';

        if ($order->status === 'pending') {
            $items .= '<button type="button" role="menuitem" data-action="status" data-status="verified" data-order="'.$orderId.'" class="dropdown-item dropdown-item--success">'
                .'<i class="ri-shield-check-line"></i><span>Pembayaran Diterima</span>'
                .'</button>';
        }

        if ($order->payment_type === 'dp' && $order->dp_settlement_proof && ! $order->dp_settlement_verified_at
            && $order->status === 'verified') {
            $items .= '<button type="button" role="menuitem" data-action="settlement-verify" data-order="'.$orderId.'" class="dropdown-item dropdown-item--settle">'
                .'<i class="ri-verified-badge-line"></i><span>Verifikasi Pelunasan</span>'
                .'</button>';
        }

        if ($order->payment_type === 'dp' && ! $order->dp_settlement_proof && ! $order->dp_settlement_verified_at
            && $order->status === 'verified') {
            $items .= '<button type="button" role="menuitem" data-action="status" data-status="paid" data-order="'.$orderId.'" class="dropdown-item dropdown-item--settle">'
                .'<i class="ri-money-dollar-circle-line"></i><span>Tandai Lunas</span>'
                .'</button>';
        }

        $pickupAttrs = '';
        if ($order->shipping_method === 'pickup') {
            $pickupAttrs = ' data-pickup-address="'.e($order->pickup_address ?? '').'"'
                .' data-pickup-contact-name="'.e($order->pickup_contact_name ?? '').'"'
                .' data-pickup-contact-phone="'.e($order->pickup_contact_phone ?? '').'"';
        }

        if ($order->shipping_method === 'kirim' && $order->status === 'shipped') {
            $items .= '<button type="button" role="menuitem" data-action="shipping" data-mode="edit" data-order="'.$orderId.'" data-tracking="'.e($order->shipping_tracking ?? '').'" class="dropdown-item dropdown-item--resi">'
                .'<i class="ri-barcode-line"></i><span>Ubah Nomor Resi</span>'
                .'</button>';
        }

        if ($order->shipping_method === 'pickup' && $order->status === 'shipped') {
            $items .= '<button type="button" role="menuitem" data-action="pickup" data-mode="edit" data-order="'.$orderId.'"'.$pickupAttrs.' class="dropdown-item dropdown-item--resi">'
                .'<i class="ri-map-pin-2-line"></i><span>Ubah Info Pengambilan</span>'
                .'</button>';
        }

        if ($order->status === 'paid') {
            if ($order->shipping_method === 'pickup' && ! $this->isTicketOrder($order)) {
                $items .= '<button type="button" role="menuitem" data-action="pickup" data-mode="ship" data-order="'.$orderId.'"'.$pickupAttrs.' class="dropdown-item dropdown-item--ship">'
                    .'<i class="ri-store-2-line"></i><span>Tandai Siap Diambil</span>'
                    .'</button>';
            } elseif ($order->shipping_method !== 'pickup') {
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
