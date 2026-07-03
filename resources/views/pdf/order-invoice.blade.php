<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order['id'] }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #4a4a4a; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .page { padding: 26px 34px 34px; }

        /* ===== Header band ===== */
        .header-band { background: linear-gradient(120deg, #d84315 0%, #f57c00 100%); background-color: #d84315; padding: 22px 34px; }
        .header-band td { vertical-align: middle; }
        .brand-mark { display: inline-block; width: 34px; height: 34px; background: rgba(255,255,255,0.18); border-radius: 9px; text-align: center; line-height: 34px; color: #ffffff; font-size: 15px; font-weight: bold; }
        .brand-name { font-size: 15px; font-weight: bold; color: #ffffff; }
        .brand-sub { font-size: 9px; color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: 0.08em; margin-top: 1px; }
        .invoice-title { font-size: 22px; font-weight: bold; color: #ffffff; text-align: right; letter-spacing: 0.04em; }
        .invoice-no { text-align: right; font-size: 10px; color: rgba(255,255,255,0.9); margin-top: 3px; font-family: 'Courier New', monospace; letter-spacing: 0.03em; }

        /* ===== Meta row ===== */
        .meta-row { padding: 14px 34px 0; }
        .meta-row td { vertical-align: top; }
        .meta-date { font-size: 10px; color: #666666; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; }

        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; color: #d84315; margin-bottom: 6px; }
        .info-card { width: 47%; background: #f8f8f8; border-radius: 8px; padding: 12px 14px; }
        .info-table td { padding: 3px 0; font-size: 10px; }
        .info-table .info-label { width: 62px; color: #999999; }
        .info-table .info-value { color: #4a4a4a; font-weight: bold; }

        .divider-space { height: 18px; }

        /* ===== Items ===== */
        .items-table { border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden; }
        .items-table th { background: #d84315; color: #ffffff; font-size: 9px; text-transform: uppercase; letter-spacing: 0.04em; padding: 10px 12px; text-align: left; font-weight: bold; }
        .items-table th.text-right, .items-table td.text-right { text-align: right; }
        .items-table td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        .items-table tr:nth-child(even) td { background: #fff5ec; }
        .items-table .item-name { font-weight: bold; color: #4a4a4a; }
        .items-table .item-variant { color: #999999; font-size: 9px; margin-top: 1px; }

        /* ===== Totals ===== */
        .totals-wrap { margin-top: 14px; }
        .totals-table { width: 260px; margin-left: auto; background: #f8f8f8; border-radius: 8px; padding: 4px 16px; }
        .totals-table td { padding: 6px 0; font-size: 10.5px; }
        .totals-table .label { color: #666666; }
        .totals-table .value { text-align: right; font-weight: bold; color: #4a4a4a; }
        .totals-table .grand-row td { border-top: 1px dashed #d1c4bb; padding-top: 10px; font-size: 14px; }
        .totals-table .grand-row .label { color: #4a4a4a; font-weight: bold; }
        .totals-table .grand-row .value { color: #d84315; font-size: 16px; }

        /* ===== E-Ticket (boarding-pass style) ===== */
        .eticket-wrap { margin-top: 22px; }
        .eticket-flag { display: inline-block; background: #d84315; color: #ffffff; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; padding: 5px 12px; border-radius: 6px 6px 0 0; }
        .eticket { border: 2px solid #d84315; border-radius: 0 10px 10px 10px; overflow: hidden; }
        .eticket-main { background: linear-gradient(135deg, #d84315 0%, #f57c00 100%); background-color: #d84315; padding: 18px 20px; }
        .eticket-main .eticket-event { font-size: 13px; font-weight: bold; color: #ffffff; }
        .eticket-main .eticket-sub { font-size: 9px; color: rgba(255,255,255,0.85); margin-top: 2px; }
        .eticket-main .eticket-holder { margin-top: 12px; font-size: 9px; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 0.06em; }
        .eticket-main .eticket-holder-name { font-size: 12px; color: #ffffff; font-weight: bold; margin-top: 1px; }
        .eticket-code-strip { margin-top: 14px; background: rgba(255,255,255,0.15); border: 1px dashed rgba(255,255,255,0.6); border-radius: 6px; padding: 8px 12px; display: inline-block; }
        .eticket-code-label { font-size: 8px; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 0.08em; }
        .eticket-code-value { font-family: 'Courier New', monospace; font-size: 16px; font-weight: bold; letter-spacing: 4px; color: #ffffff; margin-top: 2px; }
        .eticket-stub { background: #ffffff; width: 150px; text-align: center; padding: 16px 12px; border-left: 2px dashed #d84315; }
        .eticket-stub img { width: 108px; height: 108px; }
        .eticket-stub .eticket-scan-hint { font-size: 8px; color: #999999; margin-top: 6px; text-transform: uppercase; letter-spacing: 0.05em; }
        .eticket-checked { margin-top: 10px; display: inline-block; background: rgba(6,95,70,0.9); color: #ffffff; font-size: 9px; font-weight: bold; padding: 5px 10px; border-radius: 5px; }
        .eticket-notice { margin-top: 8px; font-size: 8.5px; color: rgba(255,255,255,0.8); line-height: 1.5; }

        /* ===== Footer ===== */
        .footer-note { margin-top: 22px; padding: 12px 16px; background: #f8f8f8; border-left: 3px solid #d84315; border-radius: 0 6px 6px 0; font-size: 9px; color: #666666; line-height: 1.6; }
        .footer-brand { margin-top: 14px; text-align: center; font-size: 8.5px; color: #b0b0b0; }
    </style>
</head>
<body>
    <table class="header-band">
        <tr>
            <td style="width: 60%;">
                <table>
                    <tr>
                        <td style="width: 40px;"><span class="brand-mark">JP</span></td>
                        <td>
                            <div class="brand-name">the Jakmania Purwokerto</div>
                            <div class="brand-sub">Invoice Resmi Pesanan</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%;">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-no">No. {{ $order['id'] }}</div>
            </td>
        </tr>
    </table>

    <div class="page" style="padding-top: 0;">
        <table class="meta-row">
            <tr>
                <td>
                    <div class="meta-date">
                        <i>{{ $order['created_at'] ? $order['created_at']->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</i>
                    </div>
                </td>
                <td style="text-align: right;">
                    @php
                        $statusLabels = [
                            'pending' => ['Menunggu Pembayaran', '#ffe4d1', '#92400e'],
                            'verified' => ['Pembayaran Diterima', '#d1fae5', '#065f46'],
                            'paid' => ['Pembayaran Lunas', '#ccfbf1', '#115e59'],
                            'shipped' => ['Dikirim / Siap Diambil', '#dbeafe', '#1e40af'],
                            'completed' => ['Selesai', '#e0f2fe', '#075985'],
                            'cancelled' => ['Dibatalkan', '#fee2e2', '#991b1b'],
                        ];
                        [$statusLabel, $statusBg, $statusColor] = $statusLabels[$order['status']] ?? [ucfirst($order['status'] ?? '-'), '#f3f4f6', '#374151'];
                    @endphp
                    <span class="status-badge" style="background: {{ $statusBg }}; color: {{ $statusColor }};">{{ $statusLabel }}</span>
                </td>
            </tr>
        </table>

        <div class="divider-space"></div>

        <table>
            <tr>
                <td class="info-card">
                    <div class="section-title">Ditagihkan Kepada</div>
                    <table class="info-table">
                        <tr><td class="info-label">Nama</td><td class="info-value">{{ $order['customer']['name'] }}</td></tr>
                        <tr><td class="info-label">Email</td><td class="info-value">{{ $order['customer']['email'] }}</td></tr>
                        <tr><td class="info-label">Telepon</td><td class="info-value">{{ $order['customer']['phone'] }}</td></tr>
                    </table>
                </td>
                <td style="width: 6%;"></td>
                @if ($order['shipping']['key'] !== null && !$isTicketOrder)
                <td class="info-card">
                    <div class="section-title">Pengiriman</div>
                    <table class="info-table">
                        <tr><td class="info-label">Metode</td><td class="info-value">{{ $order['shipping']['name'] }}</td></tr>
                        @if ($order['shipping']['key'] === 'kirim')
                        <tr><td class="info-label">Alamat</td><td class="info-value">{{ $order['shipping']['address'] ?? '-' }}</td></tr>
                        <tr><td class="info-label">No. Resi</td><td class="info-value">{{ $order['shipping']['tracking'] ?? 'Belum ada' }}</td></tr>
                        @else
                        <tr><td class="info-label">Kota</td><td class="info-value">{{ $order['shipping']['pickup_location_label'] ?? '-' }}</td></tr>
                        <tr><td class="info-label">Titik Temu</td><td class="info-value">{{ $order['shipping']['pickup_address'] ?? 'Belum ditentukan' }}</td></tr>
                        @endif
                    </table>
                </td>
                @else
                <td class="info-card">
                    <div class="section-title">Kategori Pesanan</div>
                    <table class="info-table">
                        <tr><td class="info-label">Tipe</td><td class="info-value">{{ $isTicketOrder ? 'Tiket Event' : 'Merchandise' }}</td></tr>
                        <tr><td class="info-label">Jumlah Item</td><td class="info-value">{{ collect($order['items'])->sum('qty') }} pcs</td></tr>
                    </table>
                </td>
                @endif
            </tr>
        </table>

        <div class="divider-space"></div>

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

        <div class="totals-wrap">
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
        </div>

        @if ($isTicketOrder && $qrDataUri)
        <div class="eticket-wrap">
            <span class="eticket-flag">&#9733; E-Ticket &mdash; Tiket Masuk Elektronik</span>
            <table class="eticket">
                <tr>
                    <td class="eticket-main">
                        <div class="eticket-event">the Jakmania Purwokerto</div>
                        <div class="eticket-sub">Tunjukkan QR ini di pintu masuk venue untuk check-in.</div>

                        <div class="eticket-holder">Nama Pemegang Tiket</div>
                        <div class="eticket-holder-name">{{ $order['customer']['name'] }}</div>

                        <div class="eticket-code-strip">
                            <div class="eticket-code-label">Kode Tiket</div>
                            <div class="eticket-code-value">{{ $order['checkin_code'] }}</div>
                        </div>

                        @if ($order['checked_in_at'])
                        <div>
                            <span class="eticket-checked">&#10003; Sudah Check-in &middot; {{ $order['checked_in_at']->locale('id')->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        @else
                        <div class="eticket-notice">Satu kode hanya berlaku untuk satu kali check-in. Simpan invoice ini hingga hari acara.</div>
                        @endif
                    </td>
                    <td class="eticket-stub">
                        <img src="{{ $qrDataUri }}" alt="QR Check-in">
                        <div class="eticket-scan-hint">Scan di venue</div>
                    </td>
                </tr>
            </table>
        </div>
        @endif

        <div class="footer-note">
            Invoice ini dibuat oleh admin the Jakmania Purwokerto pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB.
            Untuk pertanyaan seputar pesanan, hubungi admin melalui WhatsApp {{ $order['admin_whatsapp'] }}.
        </div>
        <div class="footer-brand">the Jakmania Purwokerto &mdash; Dokumen ini sah tanpa tanda tangan basah.</div>
    </div>
</body>
</html>
