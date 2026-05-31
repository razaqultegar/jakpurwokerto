<?php

namespace App\Support;

use App\Models\Order;

class OrderPresenter
{
    private const ADMIN_WHATSAPP = '6282298001051';

    private const SHIPPING_LABELS = [
        'pickup' => 'Ambil di Tempat',
        'kirim' => 'Kirim (Kurir)',
    ];

    /**
     * Susun array pesanan yang dikonsumsi template email (emails.order-invoice).
     * Dibangun mandiri dari model Order agar bisa dipakai di luar CheckoutController.
     */
    public static function mailData(Order $order): array
    {
        $pickup = $order->pickup_location
            ? config('pickup.locations.'.$order->pickup_location)
            : null;

        return [
            'id' => $order->order_id,
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address' => $order->customer_address,
            ],
            'shipping' => [
                'key' => $order->shipping_method,
                'name' => self::SHIPPING_LABELS[$order->shipping_method] ?? $order->shipping_method,
                'address' => $order->customer_address,
                'tracking' => $order->shipping_tracking,
                'pickup_location' => $order->pickup_location,
                'pickup_location_label' => $pickup['name']
                    ?? ($order->pickup_location ? ucfirst($order->pickup_location) : null),
                'pickup_address' => $pickup['address'] ?? null,
                'pickup_contact_name' => $pickup['contact_name'] ?? null,
                'pickup_contact_phone' => $pickup['contact_phone'] ?? null,
            ],
            'items' => $order->item ?? [],
            'subtotal' => (int) $order->subtotal,
            'amount_due' => (int) $order->amount_due,
            'remaining' => max(0, (int) $order->subtotal - (int) $order->amount_due),
            'payment_type' => $order->payment_type,
            'payment_type_label' => $order->payment_type === 'dp'
                ? 'Down Payment (DP 50%)'
                : 'Full Payment',
            'payment' => array_merge(
                ['type' => $order->payment_method_type, 'key' => $order->payment_method_key],
                $order->payment_data ?? []
            ),
            'admin_whatsapp' => self::ADMIN_WHATSAPP,
        ];
    }
}
