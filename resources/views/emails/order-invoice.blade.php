@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $items = $order['items'] ?? [];
    $shipping = $order['shipping'] ?? null;
    $mode = $mode ?? 'invoice';
    $isDp = ($order['payment_type'] ?? null) === 'dp';
    $remaining = $order['remaining'] ?? max(0, (int) ($order['subtotal'] ?? 0) - (int) ($order['amount_due'] ?? 0));
    $settlementUrl = route('checkout.settlement', ['orderId' => strtolower($order['id'])]);
    $reminder = $order['reminder'] ?? null;
    $deadlineLabel = $reminder['deadline_label'] ?? null;
    $reminderIntro = $reminder
        ? (($reminder['is_due'] ?? false)
            ? 'Hari ini adalah batas akhir pelunasan pesananmu' . ($deadlineLabel ? ' (' . $deadlineLabel . ')' : '') . '. Mohon segera lunasi sisa pembayaran agar pesanan dapat kami proses.'
            : 'Pengingat: batas pelunasan pesananmu' . ($deadlineLabel ? ' jatuh pada ' . $deadlineLabel : '') . '. Yuk lunasi sisa pembayaran sebelum melewati tenggat.')
        : 'Pengingat untuk melunasi sisa pembayaran pesananmu.';
    $headings = [
        'invoice' => ['eyebrow' => 'INVOICE PESANAN', 'intro' => 'Pembayaran kamu sudah kami verifikasi. Berikut invoice resmi pesananmu:'],
        'dp-verified' => ['eyebrow' => 'DP DITERIMA', 'intro' => 'Pembayaran DP kamu sudah kami verifikasi. Silakan lunasi sisa pembayaran agar pesananmu dapat segera diproses dan dikirim.'],
        'settlement-received' => ['eyebrow' => 'BUKTI PELUNASAN DITERIMA', 'intro' => 'Kami sudah menerima bukti pelunasanmu. Tim kami akan segera memverifikasi pembayaran ini.'],
        'settlement-verified' => ['eyebrow' => 'PEMBAYARAN LUNAS', 'intro' => 'Pelunasan kamu sudah kami verifikasi. Pesananmu kini berstatus LUNAS dan akan segera kami proses. Terima kasih!'],
        'reminder' => ['eyebrow' => 'PENGINGAT PELUNASAN', 'intro' => $reminderIntro],
        'shipped' => ['eyebrow' => 'PESANAN DIKIRIM', 'intro' => 'Kabar baik! Pesananmu sudah kami serahkan ke kurir dan sedang dalam perjalanan. Berikut nomor resi untuk melacak pengiriman:'],
        'pickup-ready' => ['eyebrow' => 'PESANAN SIAP DIAMBIL', 'intro' => 'Pesananmu sudah siap diambil. Silakan datang ke titik pengambilan berikut dan hubungi pengurus kami terlebih dahulu:'],
    ];
    $head = $headings[$mode] ?? $headings['invoice'];
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Invoice {{ $order['id'] }}</title>
    </head>
    <body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
            <tr>
                <td align="center">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e5e5;">
                        <tr>
                            <td style="background:#d84315;background-image:linear-gradient(135deg,#d84315 0%,#f57c00 60%,#ff7043 100%);padding:28px 24px;color:#ffffff;border-bottom:3px solid #ffb380;">
                                <div style="font-size:12px;letter-spacing:1px;opacity:.85;">{{ $head['eyebrow'] }}</div>
                                <div style="font-size:22px;font-weight:bold;margin-top:4px;">{{ $order['id'] }}</div>
                                <div style="font-size:12px;opacity:.85;margin-top:6px;">{{ config('app.name') }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 12px;font-size:14px;">Halo <strong>{{ $order['customer']['name'] }}</strong>,</p>
                                <p style="margin:0 0 16px;font-size:13px;line-height:1.6;">{{ $head['intro'] }}</p>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-top:8px;">
                                    <thead>
                                        <tr>
                                            <th align="left" style="padding:8px;border-bottom:2px solid #e5e5e5;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#666;">Produk</th>
                                            <th align="center" style="padding:8px;border-bottom:2px solid #e5e5e5;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#666;">Qty</th>
                                            <th align="right" style="padding:8px;border-bottom:2px solid #e5e5e5;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#666;">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $it)
                                        <tr>
                                            <td style="padding:10px 8px;border-bottom:1px solid #f0f0f0;font-size:13px;">
                                                <div style="font-weight:bold;">{{ $it['name'] }}</div>
                                                <div style="font-size:11px;color:#777;margin-top:2px;">{{ $it['category'] }} · {{ $it['sleeve'] }} · Ukuran {{ $it['size'] }}</div>
                                            </td>
                                            <td align="center" style="padding:10px 8px;border-bottom:1px solid #f0f0f0;font-size:13px;">{{ $it['qty'] }}</td>
                                            <td align="right" style="padding:10px 8px;border-bottom:1px solid #f0f0f0;font-size:13px;">{{ $rupiah(($it['price'] + ($it['fee'] ?? 0)) * $it['qty']) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" align="right" style="padding:10px 8px;font-size:12px;color:#666;">Subtotal</td>
                                            <td align="right" style="padding:10px 8px;font-size:13px;">{{ $rupiah($order['subtotal']) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="right" style="padding:10px 8px;font-size:12px;color:#666;">{{ $order['payment_type_label'] }}</td>
                                            <td align="right" style="padding:10px 8px;font-size:15px;font-weight:bold;color:#d84315;">{{ $rupiah($order['amount_due']) }}</td>
                                        </tr>
                                        @if ($isDp && in_array($mode, ['dp-verified', 'reminder'], true))
                                        <tr>
                                            <td colspan="2" align="right" style="padding:10px 8px;font-size:12px;color:#666;">Sisa Pelunasan</td>
                                            <td align="right" style="padding:10px 8px;font-size:13px;font-weight:bold;color:#b45309;">{{ $rupiah($remaining) }}</td>
                                        </tr>
                                        @elseif ($isDp && $mode === 'settlement-verified')
                                        <tr>
                                            <td colspan="2" align="right" style="padding:10px 8px;font-size:12px;color:#666;">Status Pembayaran</td>
                                            <td align="right" style="padding:10px 8px;font-size:13px;font-weight:bold;color:#047857;">LUNAS</td>
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>
                                @if ($isDp && $remaining > 0 && in_array($mode, ['dp-verified', 'reminder'], true))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                                    <tr>
                                        <td style="padding:16px;background:{{ ($reminder['is_due'] ?? false) ? '#fef2f2' : '#fff7ed' }};border:1px solid {{ ($reminder['is_due'] ?? false) ? '#fecaca' : '#fed7aa' }};border-radius:8px;">
                                            <div style="font-size:13px;font-weight:bold;color:#b45309;">Lunasi Sisa Pembayaran</div>
                                            <div style="font-size:12px;color:#92400e;line-height:1.6;margin-top:4px;">
                                                @if ($mode === 'reminder')
                                                    @if ($reminder['is_due'] ?? false)
                                                        <strong>Hari ini batas akhir pelunasan{{ $deadlineLabel ? ' (' . $deadlineLabel . ')' : '' }}.</strong> Lunasi sisa <strong>{{ $rupiah($remaining) }}</strong> sekarang melalui tombol di bawah ini.
                                                    @else
                                                        Batas pelunasan{{ $deadlineLabel ? ' jatuh pada ' . $deadlineLabel : '' }}. Lunasi sisa <strong>{{ $rupiah($remaining) }}</strong> melalui tombol di bawah ini sebelum tenggat.
                                                    @endif
                                                @else
                                                    DP kamu sudah diverifikasi. Lunasi sisa <strong>{{ $rupiah($remaining) }}</strong> melalui tombol di bawah ini.
                                                @endif
                                            </div>
                                            <a href="{{ $settlementUrl }}" style="display:inline-block;margin-top:12px;padding:10px 20px;background:#d84315;color:#ffffff;font-size:13px;font-weight:bold;text-decoration:none;border-radius:6px;">Lunasi Sekarang</a>
                                            <div style="font-size:11px;color:#a16207;margin-top:8px;word-break:break-all;">{{ $settlementUrl }}</div>
                                        </td>
                                    </tr>
                                </table>
                                @elseif ($mode === 'settlement-received')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                                    <tr>
                                        <td style="padding:16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;font-size:12px;color:#047857;line-height:1.6;">
                                            Bukti pelunasanmu sudah kami terima dan sedang diverifikasi. Kamu akan kami hubungi setelah pembayaran dikonfirmasi.
                                        </td>
                                    </tr>
                                </table>
                                @elseif ($mode === 'settlement-verified')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                                    <tr>
                                        <td style="padding:16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;">
                                            <div style="font-size:14px;font-weight:bold;color:#047857;">✓ Pembayaran Lunas</div>
                                            <div style="font-size:12px;color:#047857;line-height:1.6;margin-top:4px;">Total <strong>{{ $rupiah($order['subtotal']) }}</strong> telah kami terima penuh. Pesananmu akan segera diproses{{ ($shipping['key'] ?? null) === 'kirim' ? ' dan dikirim' : '' }}.</div>
                                        </td>
                                    </tr>
                                </table>
                                @elseif ($mode === 'shipped')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                                    <tr>
                                        <td style="padding:16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;">
                                            <div style="font-size:13px;font-weight:bold;color:#1d4ed8;">Nomor Resi Pengiriman</div>
                                            <div style="font-size:20px;font-weight:bold;color:#1e3a8a;letter-spacing:1px;margin-top:6px;font-family:'Courier New',monospace;">{{ $shipping['tracking'] ?? '-' }}</div>
                                            <div style="font-size:12px;color:#1e40af;line-height:1.6;margin-top:8px;">Kurir: <strong>JNT Express</strong>. Gunakan nomor resi di atas untuk melacak status pengiriman pesananmu.</div>
                                            @if (! empty($shipping['address']))
                                            <div style="font-size:12px;color:#1e40af;line-height:1.6;margin-top:8px;">Dikirim ke:<br><span style="white-space:pre-line;">{{ $shipping['address'] }}</span></div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                                @elseif ($mode === 'pickup-ready')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                                    <tr>
                                        <td style="padding:16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;">
                                            <div style="font-size:14px;font-weight:bold;color:#047857;">Pesanan Siap Diambil</div>
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;color:#065f46;margin-top:10px;">
                                                @if (! empty($shipping['pickup_location_label']))
                                                <tr>
                                                    <td style="padding:3px 0;color:#047857;width:130px;vertical-align:top;">Lokasi</td>
                                                    <td style="padding:3px 0;font-weight:bold;">{{ $shipping['pickup_location_label'] }}</td>
                                                </tr>
                                                @endif
                                                @if (! empty($shipping['pickup_address']))
                                                <tr>
                                                    <td style="padding:3px 0;color:#047857;vertical-align:top;">Alamat</td>
                                                    <td style="padding:3px 0;white-space:pre-line;">{{ $shipping['pickup_address'] }}</td>
                                                </tr>
                                                @endif
                                                @if (! empty($shipping['pickup_contact_name']))
                                                <tr>
                                                    <td style="padding:3px 0;color:#047857;vertical-align:top;">Pengurus</td>
                                                    <td style="padding:3px 0;">{{ $shipping['pickup_contact_name'] }}</td>
                                                </tr>
                                                @endif
                                                @if (! empty($shipping['pickup_contact_phone']))
                                                <tr>
                                                    <td style="padding:3px 0;color:#047857;vertical-align:top;">Kontak</td>
                                                    <td style="padding:3px 0;font-weight:bold;">
                                                        <a href="https://wa.me/{{ $shipping['pickup_contact_phone'] }}" style="color:#047857;text-decoration:underline;">+{{ $shipping['pickup_contact_phone'] }}</a>
                                                    </td>
                                                </tr>
                                                @endif
                                            </table>
                                            <div style="font-size:12px;color:#047857;line-height:1.6;margin-top:10px;">Mohon hubungi pengurus terlebih dahulu sebelum datang untuk memastikan pesananmu siap diserahkan.</div>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                                <div style="margin-top:24px;padding:16px;background:#fafafa;border-radius:8px;">
                                    <div style="font-size:12px;color:#666;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Detail Pemesan</div>
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                                        <tr>
                                            <td style="padding:3px 0;color:#666;width:140px;">Nama</td>
                                            <td style="padding:3px 0;">{{ $order['customer']['name'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;color:#666;">Email</td>
                                            <td style="padding:3px 0;">{{ $order['customer']['email'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;color:#666;">WhatsApp</td>
                                            <td style="padding:3px 0;">+62{{ $order['customer']['phone'] }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;color:#666;">Metode Pembayaran</td>
                                            <td style="padding:3px 0;">{{ $order['payment']['label'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:3px 0;color:#666;">Pengambilan</td>
                                            <td style="padding:3px 0;">
                                                {{ $shipping['name'] ?? '-' }}
                                                @if (($shipping['key'] ?? null) === 'pickup' && ! empty($shipping['pickup_location_label'])) ({{ $shipping['pickup_location_label'] }}) @endif
                                            </td>
                                        </tr>
                                        @if (($shipping['key'] ?? null) === 'kirim' && ! empty($shipping['address']))
                                        <tr>
                                            <td style="padding:3px 0;color:#666;vertical-align:top;">Alamat Kirim</td>
                                            <td style="padding:3px 0;white-space:pre-line;">{{ $shipping['address'] }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <p style="margin:24px 0 0;font-size:12px;color:#555;line-height:1.6;">Cek email ini secara berkala untuk informasi pengiriman dan update pesanan kamu. Bila ada pertanyaan, silahkan hubungi admin kami via WhatsApp di +{{ $order['admin_whatsapp'] }}.</p>
                                <p style="margin:16px 0 0;font-size:12px;color:#555;line-height:1.6;">Email ini dikirim otomatis, mohon untuk tidak mereply email ini.</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:16px 24px;background:#fafafa;border-top:1px solid #eee;font-size:11px;color:#888;text-align:center;">&copy; {{ date('Y') }} {{ config('app.name') }}.</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
