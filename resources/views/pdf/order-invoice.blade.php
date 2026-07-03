<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order['id'] }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { vertical-align: top; }
        .brand { font-size: 16px; font-weight: bold; color: #111827; }
        .brand-sub { font-size: 10px; color: #6b7280; margin-top: 2px; }
        .invoice-title { font-size: 20px; font-weight: bold; color: #ea580c; text-align: right; }
        .invoice-meta { text-align: right; font-size: 10px; color: #6b7280; margin-top: 4px; }
        .status-badge { display: inline-block; margin-top: 6px; padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 4px; }
        .info-box { width: 48%; }
        .info-table td { padding: 6px 0; }
        .divider { border-top: 1px solid #e5e7eb; margin: 16px 0; }
        .items-table { margin-top: 6px; }
        .items-table th { background: #1f2937; color: #ffffff; font-size: 9px; text-transform: uppercase; letter-spacing: 0.03em; padding: 8px 6px; text-align: left; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .items-table td { padding: 8px 6px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .items-table .item-name { font-weight: bold; color: #111827; }
        .items-table .item-variant { color: #6b7280; font-size: 9px; }
        .totals-table { width: 260px; margin-left: auto; margin-top: 10px; }
        .totals-table td { padding: 4px 0; font-size: 10.5px; }
        .totals-table .label { color: #6b7280; }
        .totals-table .value { text-align: right; font-weight: bold; color: #111827; }
        .totals-table .grand-row td { border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 13px; }
        .totals-table .grand-row .value { color: #ea580c; }
        .footer-note { margin-top: 24px; padding: 12px; background: #f9fafb; border-radius: 8px; font-size: 9.5px; color: #4b5563; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="brand">the Jakmania Purwokerto</div>
                <div class="brand-sub">Invoice Pesanan</div>
            </td>
            <td>
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    No. {{ $order['id'] }}<br>
                    {{ $order['created_at'] ? $order['created_at']->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                </div>
                <div style="text-align: right;">
                    @php
                        $statusLabels = [
                            'pending' => ['Menunggu Pembayaran', '#fef3c7', '#92400e'],
                            'verified' => ['Pembayaran Diterima', '#d1fae5', '#065f46'],
                            'paid' => ['Pembayaran Lunas', '#ccfbf1', '#115e59'],
                            'shipped' => ['Dikirim / Siap Diambil', '#dbeafe', '#1e40af'],
                            'completed' => ['Selesai', '#e0f2fe', '#075985'],
                            'cancelled' => ['Dibatalkan', '#fee2e2', '#991b1b'],
                        ];
                        [$statusLabel, $statusBg, $statusColor] = $statusLabels[$order['status']] ?? [ucfirst($order['status'] ?? '-'), '#f3f4f6', '#374151'];
                    @endphp
                    <span class="status-badge" style="background: {{ $statusBg }}; color: {{ $statusColor }};">{{ $statusLabel }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="info-box">
                <div class="section-title">Ditagihkan Kepada</div>
                <table class="info-table">
                    <tr><td style="width: 70px; color:#6b7280;">Nama</td><td>: {{ $order['customer']['name'] }}</td></tr>
                    <tr><td style="color:#6b7280;">Email</td><td>: {{ $order['customer']['email'] }}</td></tr>
                    <tr><td style="color:#6b7280;">Telepon</td><td>: {{ $order['customer']['phone'] }}</td></tr>
                </table>
            </td>
            @if ($order['shipping']['key'] !== null && !$isTicketOrder)
            <td class="info-box">
                <div class="section-title">Pengiriman</div>
                <table class="info-table">
                    <tr><td style="width: 70px; color:#6b7280;">Metode</td><td>: {{ $order['shipping']['name'] }}</td></tr>
                    @if ($order['shipping']['key'] === 'kirim')
                    <tr><td style="color:#6b7280;">Alamat</td><td>: {{ $order['shipping']['address'] ?? '-' }}</td></tr>
                    <tr><td style="color:#6b7280;">No. Resi</td><td>: {{ $order['shipping']['tracking'] ?? 'Belum ada' }}</td></tr>
                    @else
                    <tr><td style="color:#6b7280;">Kota</td><td>: {{ $order['shipping']['pickup_location_label'] ?? '-' }}</td></tr>
                    <tr><td style="color:#6b7280;">Titik Temu</td><td>: {{ $order['shipping']['pickup_address'] ?? 'Belum ditentukan' }}</td></tr>
                    @endif
                </table>
            </td>
            @endif
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order['items'] as $line)
                @php
                    $qty = (int) ($line['qty'] ?? 0);
                    $price = (int) ($line['price'] ?? 0) + (int) ($line['fee'] ?? 0);
                    $variantParts = array_filter(
                        [$line['category'] ?? '', $line['sleeve'] ?? '', $line['size'] ?? ''],
                        fn ($v) => $v !== '' && $v !== '-'
                    );
                    $variant = implode(' · ', array_unique($variantParts));
                @endphp
                <tr>
                    <td>
                        <div class="item-name">{{ $line['name'] ?? '-' }}</div>
                        @if ($variant !== '')
                        <div class="item-variant">{{ $variant }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ $qty }}×</td>
                    <td class="text-right">Rp{{ number_format($price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($price * $qty, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">Rp{{ number_format($order['subtotal'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">{{ $order['payment_type_label'] }}</td>
            <td class="value">Rp{{ number_format($order['amount_due'], 0, ',', '.') }}</td>
        </tr>
        @if ($order['remaining'] > 0)
        <tr>
            <td class="label">Sisa Tagihan</td>
            <td class="value">Rp{{ number_format($order['remaining'], 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand-row">
            <td class="label">Sudah Dibayar</td>
            <td class="value">Rp{{ number_format($order['amount_due'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Invoice ini dibuat oleh admin the Jakmania Purwokerto pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB.
        Untuk pertanyaan seputar pesanan, hubungi admin melalui WhatsApp {{ $order['admin_whatsapp'] }}.
    </div>
</body>
</html>
