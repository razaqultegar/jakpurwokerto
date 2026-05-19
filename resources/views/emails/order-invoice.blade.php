@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $items = $order['items'] ?? [];
    $shipping = $order['shipping'] ?? null;
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
                        <td style="background:#0d3a82;padding:24px;color:#ffffff;">
                            <div style="font-size:12px;letter-spacing:1px;opacity:.85;">INVOICE PESANAN</div>
                            <div style="font-size:22px;font-weight:bold;margin-top:4px;">{{ $order['id'] }}</div>
                            <div style="font-size:12px;opacity:.85;margin-top:6px;">{{ config('app.name') }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px;font-size:14px;">Halo <strong>{{ $order['customer']['name'] }}</strong>,</p>
                            <p style="margin:0 0 16px;font-size:13px;line-height:1.6;">
                                Terima kasih, pesananmu sudah kami terima dan akan segera kami proses. Berikut rincian invoice pesananmu:
                            </p>

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
                                        <td align="right" style="padding:10px 8px;border-bottom:1px solid #f0f0f0;font-size:13px;">{{ $rupiah($it['price'] * $it['qty']) }}</td>
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
                                        <td align="right" style="padding:10px 8px;font-size:15px;font-weight:bold;color:#0d3a82;">{{ $rupiah($order['amount_due']) }}</td>
                                    </tr>
                                    @if ($order['payment_type'] === 'dp')
                                    <tr>
                                        <td colspan="3" style="padding:8px;font-size:11px;color:#777;background:#fafafa;border-radius:6px;">
                                            Sisa pembayaran sebesar {{ $rupiah($order['subtotal'] - $order['amount_due']) }} dibayar saat barang siap kirim.
                                        </td>
                                    </tr>
                                    @endif
                                </tfoot>
                            </table>

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
                                            @if (($shipping['key'] ?? null) === 'pickup' && ! empty($shipping['pickup_location_label']))
                                                ({{ $shipping['pickup_location_label'] }})
                                            @endif
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

                            <p style="margin:24px 0 0;font-size:12px;color:#555;line-height:1.6;">
                                Cek email ini secara berkala untuk informasi pengiriman dan update pesanan kamu. Bila ada pertanyaan, balas email ini atau hubungi admin kami via WhatsApp di +{{ $order['admin_whatsapp'] }}.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px;background:#fafafa;border-top:1px solid #eee;font-size:11px;color:#888;text-align:center;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. Email ini dikirim otomatis, mohon untuk tidak mereply email ini.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
