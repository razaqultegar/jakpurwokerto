<?php

namespace App\Http\Controllers;

use App\Mail\OrderInvoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    private function shippingMethods()
    {
        return [
            'pickup' => [
                'key' => 'pickup',
                'name' => 'Ambil di Tempat',
                'desc' => 'Ketemuan langsung, titik temu diatur admin.',
                'icon' => 'ri-map-pin-2-line',
                'badge' => 'GRATIS',
                'detail' => 'Pilih kota terdekat. Titik temu & jadwal ditentukan admin via WhatsApp.',
            ],
            'kirim' => [
                'key' => 'kirim',
                'name' => 'Kirim (Kurir)',
                'desc' => 'Dikirim ke alamatmu via ekspedisi.',
                'icon' => 'ri-truck-line',
                'badge' => 'ONGKIR',
                'detail' => 'Pengiriman menggunakan JNT Express dan untuk biaya ongkos kirim sepenuhnya di tanggung oleh pembeli.',
            ],
        ];
    }

    private function pickupLocations()
    {
        $locations = [];
        foreach (config('pickup.locations', []) as $key => $loc) {
            $locations[$key] = ['key' => $key, 'name' => $loc['name']];
        }

        return $locations;
    }

    private function checkoutData()
    {
        return [
            'shipping_methods' => $this->shippingMethods(),
            'pickup_locations' => $this->pickupLocations(),
            'banks' => [
                [
                    'key' => 'seabank',
                    'name' => 'Seabank',
                    'logo_text' => 'SEA',
                    'account_number' => '901962260446',
                    'account_name' => 'a.n. Tsani Imaniyah',
                    'color' => 'bg-indigo-600',
                ],
            ],
            'qris' => [
                'key' => 'qris',
                'name' => 'QRIS',
                'merchant' => 'a.n. Tsani Imaniyah',
                'image' => 'medias/payments/qris.jpeg',
            ],
            'admin_whatsapp' => '6282298001051',
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
            'shipping_method' => ['required', 'in:pickup,kirim'],
            'pickup_location' => ['nullable', 'in:purwokerto,ajibarang,jakarta', 'required_if:shipping_method,pickup'],
            'address' => ['nullable', 'string', 'max:500', 'required_if:shipping_method,kirim'],
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

        $subtotal = array_sum(array_map(fn ($it) => ($it['price'] + $it['fee']) * $it['qty'], $items));
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
                'label' => $data['qris']['name'],
                'merchant' => $data['qris']['merchant'],
                'image' => $data['qris']['image'],
            ];
        } else {
            return back()->withErrors(['payment_method' => 'Metode pembayaran tidak valid.'])
                ->withInput();
        }

        $orderId = 'JPW-'.now()->format('ymd').'-'.strtoupper(Str::random(5));

        $order = Order::create([
            'order_id' => $orderId,
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'shipping_method' => $validated['shipping_method'],
            'pickup_location' => $validated['shipping_method'] === 'pickup'
                ? ($validated['pickup_location'] ?? null)
                : null,
            'customer_address' => $validated['shipping_method'] === 'kirim'
                ? ($validated['address'] ?? null)
                : null,
            'item' => $items,
            'subtotal' => $subtotal,
            'amount_due' => $amount,
            'payment_type' => $validated['payment_type'],
            'payment_method_type' => $methodType,
            'payment_method_key' => $methodKey,
            'payment_data' => $paymentData,
            'status' => 'pending',
        ]);

        return redirect()->route('checkout.payment', ['orderId' => strtolower($order->order_id)]);
    }

    /**
     * Tentukan tahap pelunasan DP sebuah pesanan.
     *
     * not_dp    : bukan pesanan DP, tidak ada pelunasan.
     * cancelled : pesanan dibatalkan.
     * done      : sudah lunas (pelunasan terverifikasi / pesanan selesai / tidak ada sisa).
     * review    : bukti pelunasan sudah diunggah, menunggu verifikasi admin.
     * locked    : DP belum diverifikasi admin, pelunasan belum bisa dibayar.
     * open      : siap dilunasi (DP terverifikasi, masih ada sisa).
     */
    private function settlementState(Order $order): string
    {
        if ($order->payment_type !== 'dp') {
            return 'not_dp';
        }
        if ($order->status === 'cancelled') {
            return 'cancelled';
        }
        if ($order->dp_settlement_verified_at
            || in_array($order->status, ['paid', 'shipped', 'completed'], true)
            || (int) $order->subtotal - (int) $order->amount_due <= 0) {
            return 'done';
        }
        if ($order->dp_settlement_proof) {
            return 'review';
        }
        if ($order->status !== 'verified') {
            return 'locked';
        }

        return 'open';
    }

    public function settlement(string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return redirect()->route('checkout')
                ->with('status', 'Pesanan tidak ditemukan.');
        }

        if ($order->payment_type !== 'dp') {
            return redirect()->route('checkout.payment', ['orderId' => strtolower($order->order_id)]);
        }

        if ($order->status === 'cancelled') {
            return $this->redirectClosedOrder($order);
        }

        return view('pages.checkout.settlement', [
            'title' => 'Pelunasan',
            'order' => $this->buildOrderView($order),
        ]);
    }

    public function uploadSettlement(Request $request, string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return back()->with('proof_status', 'error')
                ->with('proof_message', 'Pesanan tidak ditemukan.');
        }

        $state = $this->settlementState($order);

        if (! in_array($state, ['open', 'review'], true)) {
            $messages = [
                'not_dp' => 'Pesanan ini bukan tipe DP.',
                'cancelled' => 'Pesanan ini telah dibatalkan.',
                'done' => 'Pesanan ini sudah lunas.',
                'locked' => 'DP kamu masih menunggu verifikasi admin. Pelunasan dapat dilakukan setelah DP diterima.',
            ];

            return redirect()->route('checkout.settlement', ['orderId' => strtolower($order->order_id)])
                ->with('proof_status', 'error')
                ->with('proof_message', $messages[$state] ?? 'Pelunasan tidak dapat diproses.');
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'proof.required' => 'File bukti pelunasan wajib dipilih.',
            'proof.mimes' => 'Format file harus jpg, jpeg, png, webp, atau pdf.',
            'proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($order->dp_settlement_proof) {
            Storage::disk('public')->delete($order->dp_settlement_proof);
        }

        $path = $validated['proof']->store('proofs/settlements', 'public');

        $order->update([
            'dp_settlement_proof' => $path,
            'dp_settlement_uploaded_at' => now(),
            'dp_settlement_verified_at' => null,
        ]);

        try {
            Mail::to($order->customer_email)
                ->send(new OrderInvoice($this->buildOrderView($order), 'settlement-received'));
        } catch (\Throwable $e) {
            Log::error('Failed to send settlement email', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('checkout.settlement', ['orderId' => strtolower($order->order_id)])
            ->with('proof_status', 'success')
            ->with('proof_message', 'Bukti pelunasan berhasil diunggah. Admin akan memverifikasi pembayaranmu.');
    }

    private function buildOrderView(Order $order): array
    {
        return [
            'id' => $order->order_id,
            'created_at' => $order->created_at?->toIso8601String(),
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address' => $order->customer_address,
            ],
            'shipping' => array_merge(
                $this->shippingMethods()[$order->shipping_method]
                    ?? ['key' => $order->shipping_method, 'name' => $order->shipping_method],
                [
                    'address' => $order->customer_address,
                    'pickup_location' => $order->pickup_location,
                    'pickup_location_label' => $order->pickup_location
                        ? ($this->pickupLocations()[$order->pickup_location]['name']
                            ?? ucfirst($order->pickup_location))
                        : null,
                ]
            ),
            'payment_proof' => $order->payment_proof,
            'payment_proof_url' => $order->payment_proof
                ? asset('storage/'.$order->payment_proof)
                : null,
            'items' => $order->item ?? [],
            'subtotal' => $order->subtotal,
            'amount_due' => $order->amount_due,
            'remaining' => max(0, (int) $order->subtotal - (int) $order->amount_due),
            'payment_type' => $order->payment_type,
            'payment_type_label' => $order->payment_type === 'dp'
                ? 'Down Payment (DP 50%)'
                : 'Full Payment',
            'settlement' => [
                'state' => $this->settlementState($order),
                'proof' => $order->dp_settlement_proof,
                'proof_url' => $order->dp_settlement_proof
                    ? asset('storage/'.$order->dp_settlement_proof)
                    : null,
                'uploaded_at' => $order->dp_settlement_uploaded_at?->toIso8601String(),
                'verified_at' => $order->dp_settlement_verified_at?->toIso8601String(),
            ],
            'payment' => array_merge(
                ['type' => $order->payment_method_type, 'key' => $order->payment_method_key],
                $order->payment_data ?? []
            ),
            'status' => $order->status,
            'admin_whatsapp' => $this->checkoutData()['admin_whatsapp'],
        ];
    }

    public function payment(string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return redirect()->route('checkout')
                ->with('status', 'Pesanan tidak ditemukan.');
        }

        if (in_array($order->status, ['verified', 'paid', 'shipped', 'completed', 'cancelled'], true)) {
            return $this->redirectClosedOrder($order);
        }

        if ($order->payment_proof) {
            return redirect()->route('checkout.success', ['orderId' => strtolower($order->order_id)]);
        }

        return view('pages.checkout.payment', [
            'title' => 'Pembayaran',
            'order' => $this->buildOrderView($order),
        ]);
    }

    private function redirectClosedOrder(Order $order)
    {
        $slug = collect($order->item ?? [])->pluck('slug')->filter()->first();
        $message = $order->status === 'cancelled'
            ? 'Pesanan ini telah dibatalkan. Silakan buat pesanan baru.'
            : 'Pembayaran pesanan ini sudah diterima. Halaman pembayaran tidak dapat diakses lagi.';

        $target = $slug
            ? redirect()->route('merchandise.show', ['slug' => $slug])
            : redirect()->route('home');

        return $target->with('status', $message);
    }

    public function success(string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return redirect()->route('checkout')
                ->with('status', 'Pesanan tidak ditemukan.');
        }

        if (! $order->payment_proof) {
            return redirect()->route('checkout.payment', ['orderId' => strtolower($order->order_id)]);
        }

        return view('pages.checkout.success', [
            'title' => 'Terima Kasih',
            'order' => $this->buildOrderView($order),
        ]);
    }

    public function uploadProof(Request $request, string $orderId)
    {
        $order = Order::where('order_id', $orderId)->first();
        if (! $order) {
            return back()->with('proof_status', 'error')
                ->with('proof_message', 'Pesanan tidak ditemukan.');
        }

        if (in_array($order->status, ['verified', 'paid', 'shipped', 'completed', 'cancelled'], true)) {
            return $this->redirectClosedOrder($order);
        }

        $validated = $request->validate([
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'proof.required' => 'File bukti transfer wajib dipilih.',
            'proof.mimes' => 'Format file harus jpg, jpeg, png, webp, atau pdf.',
            'proof.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $validated['proof']->store('proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_proof_uploaded_at' => now(),
        ]);

        // Email invoice/pembayaran diterima dikirim saat admin memverifikasi pembayaran,
        // bukan saat bukti diunggah. Lihat AdminController::updateStatus.

        return redirect()->route('checkout.success', ['orderId' => strtolower($order->order_id)])
            ->with('proof_status', 'success')
            ->with('proof_message', 'Bukti transfer berhasil diunggah. Admin akan memverifikasi pembayaranmu dan invoice dikirim ke email.');
    }
}
