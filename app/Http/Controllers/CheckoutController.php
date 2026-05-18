<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private function checkoutData()
    {
        return [
            'banks' => [
                [
                    'key' => 'bca',
                    'name' => 'Bank BCA',
                    'logo_text' => 'BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'JakPurwokerto Raya',
                    'color' => 'bg-sky-600',
                ],
                [
                    'key' => 'mandiri',
                    'name' => 'Bank Mandiri',
                    'logo_text' => 'MDR',
                    'account_number' => '0987654321',
                    'account_name' => 'JakPurwokerto Raya',
                    'color' => 'bg-yellow-500',
                ],
            ],
            'qris' => [
                'key' => 'dana',
                'name' => 'DANA',
                'merchant' => 'JakPurwokerto Raya',
                'image' => 'medias/qris-dana.png',
            ],
            'admin_whatsapp' => '6281234567890',
        ];
    }

    public function index()
    {
        return view('pages.checkout.index', [
            'title' => 'Checkout',
            'checkout' => $this->checkoutData(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{8,15}$/'],
            'note' => ['nullable', 'string', 'max:240'],
            'payment_type' => ['required', 'in:dp,full'],
            'payment_method' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.slug' => ['nullable', 'string', 'max:120'],
            'items.*.name' => ['required', 'string', 'max:160'],
            'items.*.image' => ['nullable', 'string', 'max:255'],
            'items.*.category' => ['required', 'string', 'max:60'],
            'items.*.sleeve' => ['required', 'string', 'max:60'],
            'items.*.size' => ['required', 'string', 'max:60'],
            'items.*.qty' => ['required', 'integer', 'min:1', 'max:99'],
            'items.*.price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'items.*.fee' => ['nullable', 'integer', 'min:0', 'max:10000000'],
        ]);

        $items = array_map(fn ($it) => [
            'slug' => $it['slug'] ?? null,
            'name' => $it['name'],
            'image' => $it['image'] ?? null,
            'category' => $it['category'],
            'sleeve' => $it['sleeve'],
            'size' => $it['size'],
            'qty' => (int) $it['qty'],
            'price' => (int) $it['price'],
            'fee' => (int) ($it['fee'] ?? 0),
        ], $validated['items']);

        $subtotal = array_sum(array_map(fn ($it) => $it['price'] * $it['qty'], $items));
        $amount = $validated['payment_type'] === 'dp' ? (int) round($subtotal * 0.5) : $subtotal;

        $data = $this->checkoutData();

        [$methodType, $methodKey] = array_pad(explode(':', $validated['payment_method'], 2), 2, null);

        $paymentData = [];

        if ($methodType === 'bank') {
            $bank = collect($data['banks'])->firstWhere('key', $methodKey);
            if (! $bank) {
                return back()->withErrors(['payment_method' => 'Metode pembayaran tidak valid.'])
                    ->withInput();
            }

            $paymentData = [
                'label' => $bank['name'],
                'logo_text' => $bank['logo_text'],
                'color' => $bank['color'],
                'account_number' => $bank['account_number'],
                'account_name' => $bank['account_name'],
            ];
        } elseif ($methodType === 'qris') {
            $paymentData = [
                'label' => 'QRIS '.$data['qris']['name'],
                'merchant' => $data['qris']['merchant'],
                'image' => $data['qris']['image'],
            ];
        } else {
            return back()->withErrors(['payment_method' => 'Metode pembayaran tidak valid.'])
                ->withInput();
        }

        $orderId = 'JPW-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));

        $order = Order::create([
            'order_id' => $orderId,
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'customer_note' => $validated['note'] ?? null,
            'item' => $items,
            'subtotal' => $subtotal,
            'amount_due' => $amount,
            'payment_type' => $validated['payment_type'],
            'payment_method_type' => $methodType,
            'payment_method_key' => $methodKey,
            'payment_data' => $paymentData,
            'status' => 'pending',
        ]);

        return redirect()->route('checkout.success', ['orderId' => $order->order_id]);
    }

    public function success(string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return redirect()->route('checkout')
                ->with('status', 'Pesanan tidak ditemukan.');
        }

        $view = [
            'id' => $order->order_id,
            'created_at' => $order->created_at?->toIso8601String(),
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'note' => $order->customer_note,
            ],
            'items' => $order->item ?? [],
            'subtotal' => $order->subtotal,
            'amount_due' => $order->amount_due,
            'payment_type' => $order->payment_type,
            'payment_type_label' => $order->payment_type === 'dp' ? 'Down Payment (DP 50%)' : 'Full Payment',
            'payment' => array_merge(
                ['type' => $order->payment_method_type, 'key' => $order->payment_method_key],
                $order->payment_data ?? []
            ),
            'status' => $order->status,
            'admin_whatsapp' => $this->checkoutData()['admin_whatsapp'],
        ];

        return view('pages.checkout.success', [
            'title' => 'Terima Kasih',
            'order' => $view,
        ]);
    }
}
