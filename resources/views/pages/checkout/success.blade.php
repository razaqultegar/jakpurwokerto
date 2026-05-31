@extends('layouts.app')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $items = $order['items'] ?? [];
    $totalQty = collect($items)->sum('qty');
    $shipping = $order['shipping'] ?? null;
@endphp

@section('content')
    <header class="app-header">
        <a href="{{ route('home') }}" class="icon-btn">
            <i class="ri-home-5-line text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="app-header__title">Terima Kasih</h1>
            <p class="app-header__subtitle">Pesanan kamu sudah kami terima</p>
        </div>
        <span class="badge badge-emerald">
            <i class="ri-checkbox-circle-fill"></i>
            Selesai
        </span>
    </header>
    <section class="px-4 pb-6 pt-5">
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-emerald-500 via-emerald-500 to-emerald-400 p-5 text-white shadow-lg">
            <span class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10"></span>
            <span class="pointer-events-none absolute -bottom-10 -left-6 h-28 w-28 rounded-full bg-white/10"></span>
            <div class="relative flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 ring-1 ring-white/30">
                    <i class="ri-shield-check-fill text-2xl"></i>
                </span>
                <div class="flex-1">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-white/80">Pesanan Berhasil</p>
                    <p class="mt-0.5 text-sm font-black leading-snug">Terima kasih, pesananmu sudah masuk dan akan segera kami proses.</p>
                </div>
            </div>
            <p class="relative mt-3 text-[11px] leading-snug text-white/90">
                Invoice telah dikirim ke <span class="font-bold">{{ $order['customer']['email'] }}</span>. Silakan cek email kamu secara berkala untuk informasi terbaru terkait pesanan.
            </p>
        </div>
    </section>
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h2 class="section-title">ID Pesanan</h2>
        </div>
        <div class="card-skull-lg">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Nomor Pesanan</p>
            <div class="mt-1 flex items-center justify-between gap-2">
                <span class="text-lg font-black tracking-wider text-foreground">{{ $order['id'] }}</span>
                <span class="badge bg-emerald-100 text-emerald-600 ring-1 ring-emerald-200">
                    <i class="ri-checkbox-circle-fill"></i>
                    Diproses
                </span>
            </div>
            <p class="mt-2 text-[10px] leading-snug text-onyx">Simpan ID Pesanan ini sebagai referensi. Kami akan menghubungi kamu via email untuk update pesanan.</p>
        </div>
    </section>
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h2 class="section-title">Ringkasan Pesanan</h2>
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
            <div class="flex items-center justify-between border-t border-mercury pt-2">
                <dt class="text-onyx">{{ $order['payment_type_label'] }}</dt>
                <dd class="font-black text-primary">{{ $rupiah($order['amount_due']) }}</dd>
            </div>
        </dl>
        @if ($order['payment_type'] === 'dp' && ($order['remaining'] ?? 0) > 0)
        <div class="mt-3 rounded-2xl border border-dashed border-amber-300 bg-amber-50 p-4">
            <div class="flex items-start gap-2">
                <i class="ri-wallet-3-line shrink-0 text-base text-amber-600"></i>
                <div class="flex-1">
                    <p class="text-[11px] font-bold text-amber-800">Sisa pelunasan {{ $rupiah($order['remaining']) }}</p>
                    <p class="mt-0.5 text-[10px] leading-snug text-amber-700">Setelah DP diverifikasi admin, kamu bisa melunasi sisa pembayaran lewat halaman pelunasan. Link juga kami kirim ke emailmu.</p>
                </div>
            </div>
            <a href="{{ route('checkout.settlement', ['orderId' => strtolower($order['id'])]) }}" class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-[11px] font-bold text-white shadow-sm transition active:scale-95 hover:bg-amber-600">
                <i class="ri-arrow-right-circle-line text-sm"></i>
                Buka Halaman Pelunasan
            </a>
        </div>
        @endif
    </section>
    <div class="cta-floating">
        <div class="pointer-events-auto mx-auto max-w-480 cta-card">
            <a href="{{ route('home') }}" class="btn-primary-gradient w-full">
                <i class="ri-home-5-line text-lg"></i>
                Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection
