@extends('layouts.app')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $payment = $order['payment'];
    $isBank = ($payment['type'] ?? null) === 'bank';
    $isQris = ($payment['type'] ?? null) === 'qris';
    $items = $order['items'] ?? [];
    $totalQty = collect($items)->sum('qty');
    $itemsLine = collect($items)->map(fn ($it) => "- {$it['name']} ({$it['category']} · {$it['sleeve']} · {$it['size']}) x{$it['qty']}")->implode('%0A');
    $shipping = $order['shipping'] ?? null;
    $shippingName = $shipping['name'] ?? '';
    if ($shipping && ($shipping['key'] ?? null) === 'pickup' && ! empty($shipping['pickup_location_label'])) {
        $shippingName .= ' - ' . $shipping['pickup_location_label'];
    }
    $shippingLine = $shipping ? "*Pengambilan:* {$shippingName}%0A" : '';
    $addressLine = ($shipping && ($shipping['key'] ?? null) === 'kirim' && ! empty($shipping['address']))
        ? "*Alamat Kirim:*%0A" . rawurlencode($shipping['address']) . "%0A"
        : '';
    $hasProof = ! empty($order['payment_proof']);
    $proofUrl = $order['payment_proof_url'] ?? null;
    $proofExt = $hasProof ? strtolower(pathinfo($order['payment_proof'], PATHINFO_EXTENSION)) : null;
    $proofIsImage = in_array($proofExt, ['jpg', 'jpeg', 'png', 'webp']);
    $waText = "Halo Admin, saya ingin konfirmasi pembayaran pesanan:%0A%0A"
        ."*ID Pesanan:* {$order['id']}%0A"
        ."*Nama:* {$order['customer']['name']}%0A"
        ."*Item:*%0A{$itemsLine}%0A"
        ."{$shippingLine}"
        ."{$addressLine}"
        ."*Jenis Pembayaran:* {$order['payment_type_label']}%0A"
        ."*Metode:* {$payment['label']}%0A"
        ."*Jumlah Dibayar:* {$rupiah($order['amount_due'])}%0A%0A"
        ."Bukti transfer akan saya kirim setelah pesan ini. Terima kasih!";
    $waUrl = 'https://wa.me/' . $order['admin_whatsapp'] . '?text=' . $waText;
    $qrSrc = $isQris && ! empty($payment['image'])
        ? asset('build/' . $payment['image'])
        : null;
@endphp

@section('content')
    <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-mercury bg-white/95 px-4 py-3 backdrop-blur-md">
        <a href="{{ route('home') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-skull text-foreground ring-1 ring-mercury">
            <i class="ri-home-5-line text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-sm font-black text-foreground">Terima Kasih!</h1>
            <p class="text-[10px] text-onyx">Pesananmu sudah kami terima</p>
        </div>
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600 ring-1 ring-emerald-200">
            <i class="ri-check-double-line"></i>
            Sukses
        </span>
    </header>
    <section class="px-4 pb-6 pt-5">
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-primary via-primary-light to-primary-lighter p-5 text-white shadow-lg">
            <span class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></span>
            <span class="pointer-events-none absolute -bottom-10 -left-6 h-28 w-28 rounded-full bg-white/10"></span>
            <div class="relative flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 ring-1 ring-white/30">
                    <i class="ri-shopping-bag-3-line text-2xl"></i>
                </span>
                <div class="flex-1">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-white/80">ID Pesanan</p>
                    <div class="mt-0.5 flex items-center gap-2">
                        <span class="text-base font-black tracking-tight" data-order-id>{{ $order['id'] }}</span>
                        <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/15 text-white ring-1 ring-white/25 transition active:scale-95" data-copy="{{ $order['id'] }}" >
                            <i class="ri-file-copy-line text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            <p class="relative mt-3 text-[11px] leading-snug text-white/90">Simpan ID Pesanan ini untuk konfirmasi pembayaran. Segera lakukan pembayaran agar pesananmu diproses.</p>
        </div>
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 py-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h2 class="text-sm font-bold text-foreground">Total Pembayaran</h2>
        </div>
        <div class="rounded-2xl bg-skull p-4 ring-1 ring-mercury">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-onyx">{{ $order['payment_type_label'] }}</p>
                    <p class="text-[10px] text-onyx">{{ count($items) }} produk · {{ $totalQty }} item</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-onyx">Jumlah yang harus dibayar</p>
                    <div class="flex items-center justify-end gap-2">
                        <span class="text-xl font-black text-primary" data-amount>{{ $rupiah($order['amount_due']) }}</span>
                        <button type="button" class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-foreground ring-1 ring-mercury transition active:scale-95" data-copy="{{ $order['amount_due'] }}">
                            <i class="ri-file-copy-line text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            @if ($order['payment_type'] === 'dp')
            <div class="mt-3 flex items-start gap-2 rounded-xl border border-dashed border-primary-soft bg-white p-2.5">
                <i class="ri-information-2-line shrink-0 text-sm text-primary"></i>
                <p class="text-[10px] leading-snug text-onyx">Sisa pembayaran sebesar <span class="font-bold text-foreground">{{ $rupiah($order['subtotal'] - $order['amount_due']) }}</span> dibayar saat barang siap kirim.</p>
            </div>
            @endif
        </div>
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 py-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h2 class="text-sm font-bold text-foreground">Detail Pembayaran</h2>
        </div>
        @if ($isBank)
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg {{ $payment['color'] }} text-[11px] font-black uppercase tracking-wider text-white shadow-sm">{{ $payment['logo_text'] }}</span>
                <div class="flex-1">
                    <p class="text-xs font-bold text-foreground">{{ $payment['label'] }}</p>
                    <p class="text-[10px] text-onyx">a.n. {{ $payment['account_name'] }}</p>
                </div>
            </div>
            <div class="mt-3 rounded-xl bg-skull p-3 ring-1 ring-mercury">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Nomor Rekening</p>
                <div class="mt-1 flex items-center justify-between gap-2">
                    <span class="text-lg font-black tracking-wider text-foreground" data-account>{{ $payment['account_number'] }}</span>
                    <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition active:scale-95" data-copy="{{ $payment['account_number'] }}">
                        <i class="ri-file-copy-line text-xs"></i>
                        Salin
                    </button>
                </div>
            </div>
            <ol class="mt-3 space-y-1.5 text-[11px] text-onyx">
                <li class="flex gap-2">
                    <span class="font-bold text-primary">1.</span>
                    Transfer tepat sesuai nominal di atas.
                </li>
                <li class="flex gap-2">
                    <span class="font-bold text-primary">2.</span>
                    Simpan bukti transfer kamu.
                </li>
                <li class="flex gap-2">
                    <span class="font-bold text-primary">3.</span>
                    Konfirmasi via tombol WhatsApp di bawah.
                </li>
            </ol>
        </div>
        @elseif ($isQris)
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury">
            <div class="flex items-center gap-3">
                <span class="flex h-12 w-16 shrink-0 items-center justify-center rounded-lg bg-sky-500 text-[11px] font-black uppercase tracking-wider text-white shadow-sm">QRIS</span>
                <div class="flex-1">
                    <p class="text-xs font-bold text-foreground">{{ $payment['label'] }}</p>
                    <p class="text-[10px] text-onyx">{{ $payment['merchant'] }}</p>
                </div>
            </div>
            <div class="mt-3 flex flex-col items-center rounded-xl bg-skull p-4 ring-1 ring-mercury">
                <div class="rounded-2xl bg-white p-3 ring-1 ring-mercury">
                    <img src="{{ $qrSrc }}" alt="Kode QR Pembayaran" class="h-48 w-48 object-contain" loading="lazy">
                </div>
                <p class="mt-3 text-center text-[11px] leading-snug text-onyx">Scan kode QR di atas pakai DANA, GoPay, OVO, ShopeePay, atau mobile banking.</p>
            </div>
            <ol class="mt-3 space-y-1.5 text-[11px] text-onyx">
                <li class="flex gap-2">
                    <span class="font-bold text-primary">1.</span>
                    Pastikan nominal sesuai dengan total tagihan.
                </li>
                <li class="flex gap-2">
                    <span class="font-bold text-primary">2.</span>
                    Simpan struk pembayaran.
                </li>
                <li class="flex gap-2">
                    <span class="font-bold text-primary">3.</span>
                    Konfirmasi via tombol WhatsApp di bawah.
                </li>
            </ol>
        </div>
        @endif
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 py-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h2 class="text-sm font-bold text-foreground">Bukti Transfer</h2>
        </div>
        @if (session('proof_status') === 'success')
        <div class="mb-3 flex items-start gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-[11px] leading-snug text-emerald-700">
            <i class="ri-checkbox-circle-fill shrink-0 text-base"></i>
            <span>{{ session('proof_message') }}</span>
        </div>
        @elseif (session('proof_status') === 'error')
        <div class="mb-3 flex items-start gap-2 rounded-2xl border border-red-200 bg-red-50 p-3 text-[11px] leading-snug text-red-700">
            <i class="ri-error-warning-fill shrink-0 text-base"></i>
            <span>{{ session('proof_message') }}</span>
        </div>
        @endif
        @if ($hasProof)
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 ring-1 ring-emerald-200">
                    <i class="ri-shield-check-fill text-xl"></i>
                </span>
                <div class="flex-1">
                    <p class="text-xs font-bold text-foreground">Bukti sudah terkirim</p>
                    <p class="text-[10px] text-onyx">Admin akan verifikasi & konfirmasi via WhatsApp.</p>
                </div>
            </div>
            @if ($proofIsImage)
            <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="mt-3 block overflow-hidden rounded-xl ring-1 ring-mercury">
                <img src="{{ $proofUrl }}" class="h-auto w-full object-contain" alt="Bukti Transfer" loading="lazy">
            </a>
            @else
            <a href="{{ $proofUrl }}" class="mt-3 flex items-center gap-2 rounded-xl bg-skull p-3 ring-1 ring-mercury" target="_blank" rel="noopener">
                <i class="ri-file-pdf-2-line text-2xl text-red-500"></i>
                <span class="flex-1 truncate text-[11px] font-semibold text-foreground">Lihat bukti (PDF)</span>
                <i class="ri-external-link-line text-base text-onyx"></i>
            </a>
            @endif
            <p class="mt-3 text-[10px] text-onyx">Mau ganti file? Upload ulang di bawah ini.</p>
        </div>
        @endif
        <form action="{{ route('checkout.proof', ['orderId' => strtolower($order['id'])]) }}" method="post" enctype="multipart/form-data" class="{{ $hasProof ? 'mt-3' : '' }}" data-proof-form>
            @csrf
            <label for="proof-file" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-primary-soft bg-primary-softer p-5 text-center transition hover:bg-primary-softer/80" data-proof-dropzone>
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-primary ring-1 ring-primary-soft">
                    <i class="ri-upload-cloud-2-line text-2xl"></i>
                </span>
                <span class="text-xs font-bold text-foreground" data-proof-label>{{ $hasProof ? 'Ganti bukti transfer' : 'Pilih file bukti transfer' }}</span>
                <span class="text-[10px] text-onyx">JPG/PNG/WEBP/PDF · maks 5MB</span>
                <input type="file" id="proof-file" class="sr-only" name="proof" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf" data-proof-input required>
            </label>
            <div class="mt-3 hidden items-center gap-3 rounded-xl bg-white p-3 ring-1 ring-mercury" data-proof-preview>
                <span class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-skull text-primary ring-1 ring-mercury">
                    <i class="ri-file-line text-xl" data-proof-preview-icon></i>
                    <img class="hidden h-full w-full object-cover" alt="" data-proof-preview-image>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-foreground" data-proof-preview-name>-</p>
                    <p class="text-[10px] text-onyx" data-proof-preview-size>-</p>
                </div>
                <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-skull text-foreground ring-1 ring-mercury" data-proof-clear aria-label="Hapus file">
                    <i class="ri-close-line text-sm"></i>
                </button>
            </div>
            @error('proof')
            <p class="mt-2 text-[10px] text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 text-xs font-bold text-white shadow-sm transition active:scale-95 disabled:opacity-50" data-proof-submit disabled>
                <i class="ri-upload-2-line text-base"></i>
                <span data-proof-submit-label>Kirim Bukti Transfer</span>
            </button>
        </form>
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 py-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h2 class="text-sm font-bold text-foreground">Ringkasan Pesanan</h2>
        </div>
        <div class="space-y-2">
            @foreach ($items as $it)
            <div class="flex items-center gap-3 rounded-2xl bg-skull p-3 ring-1 ring-mercury">
                @if (! empty($it['image']))
                <img src="{{ asset('build/' . $it['image']) }}" alt="{{ $it['name'] }}" class="h-16 w-16 shrink-0 rounded-xl object-cover ring-1 ring-mercury">
                @else
                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-white text-primary ring-1 ring-mercury">
                    <i class="ri-shirt-fill text-2xl"></i>
                </span>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="truncate text-xs font-bold text-foreground">{{ $it['name'] }}</div>
                    <div class="mt-0.5 text-[10px] text-onyx">{{ $it['category'] }} · {{ $it['sleeve'] }} · Ukuran {{ $it['size'] }}</div>
                    <div class="mt-1 inline-flex items-center gap-2">
                        <span class="rounded-md bg-white px-1.5 py-0.5 text-[10px] font-semibold text-foreground ring-1 ring-mercury">x{{ $it['qty'] }}</span>
                        <span class="text-[11px] font-bold text-primary">{{ $rupiah($it['price']) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <dl class="mt-3 space-y-1.5 text-[11px]">
            <div class="flex items-center justify-between">
                <dt class="text-onyx">Nama Pemesan</dt>
                <dd class="font-semibold text-foreground">{{ $order['customer']['name'] }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-onyx">No. WhatsApp</dt>
                <dd class="font-semibold text-foreground">+62{{ $order['customer']['phone'] }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-onyx">Email</dt>
                <dd class="font-semibold text-foreground">{{ $order['customer']['email'] }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-onyx">Metode Pengambilan</dt>
                <dd class="font-semibold text-foreground">{{ $shipping['name'] ?? '-' }}</dd>
            </div>
            @if (($shipping['key'] ?? null) === 'pickup' && ! empty($shipping['pickup_location_label']))
            <div class="flex items-center justify-between">
                <dt class="text-onyx">Kota Pengambilan</dt>
                <dd class="font-semibold text-foreground">{{ $shipping['pickup_location_label'] }}</dd>
            </div>
            @endif
            @if (($shipping['key'] ?? null) === 'kirim' && ! empty($shipping['address']))
            <div class="flex items-start justify-between gap-3">
                <dt class="shrink-0 text-onyx">Alamat</dt>
                <dd class="whitespace-pre-line text-right font-semibold text-foreground">{{ $shipping['address'] }}</dd>
            </div>
            @endif
        </dl>
    </section>
    <div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 mx-auto max-w-screen-sm px-3 pb-3">
        <div class="pointer-events-auto mx-auto flex max-w-480 flex-col gap-2">
            <div class="overflow-hidden" data-copy-toast>
                <div class="flex w-full translate-y-[calc(100%+1rem)] items-start gap-2.5 rounded-2xl bg-foreground/95 px-3.5 py-3 text-white shadow-[0_10px_30px_-5px_rgba(0,0,0,0.45)] ring-1 ring-white/10 backdrop-blur-md transition-transform duration-300 ease-out" data-copy-toast-panel>
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-400 text-foreground">
                        <i class="ri-check-line text-base"></i>
                    </span>
                    <div class="flex-1">
                        <div class="text-xs font-bold">Berhasil disalin</div>
                        <p class="mt-0.5 text-[11px] leading-snug text-white/80" data-copy-toast-message>Tempel ke aplikasi pembayaranmu.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-white/95 p-2 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.25)] ring-1 ring-mercury backdrop-blur-md">
                <a href="{{ $waUrl }}" class="relative flex h-12 w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-linear-to-r from-emerald-500 via-emerald-500 to-emerald-400 px-5 text-sm font-bold text-white shadow-lg" target="_blank" rel="noopener">
                    <span class="pointer-events-none absolute -left-6 top-0 h-full w-12 -skew-x-12 bg-white/20"></span>
                    <i class="ri-whatsapp-line text-lg"></i>
                    Konfirmasi via WhatsApp
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkout-success.js')
@endpush
