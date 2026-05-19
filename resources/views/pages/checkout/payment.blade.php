@extends('layouts.app')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $payment = $order['payment'];
    $isBank = ($payment['type'] ?? null) === 'bank';
    $isQris = ($payment['type'] ?? null) === 'qris';
    $items = $order['items'] ?? [];
    $totalQty = collect($items)->sum('qty');
    $hasProof = ! empty($order['payment_proof']);
    $proofUrl = $order['payment_proof_url'] ?? null;
    $proofExt = $hasProof ? strtolower(pathinfo($order['payment_proof'], PATHINFO_EXTENSION)) : null;
    $proofIsImage = in_array($proofExt, ['jpg', 'jpeg', 'png', 'webp']);
    $qrSrc = $isQris && ! empty($payment['image'])
        ? asset('build/' . $payment['image'])
        : null;
@endphp

@section('content')
    <header class="app-header">
        <a href="{{ route('home') }}" class="icon-btn">
            <i class="ri-home-5-line text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="app-header__title">Pembayaran</h1>
            <p class="app-header__subtitle">Selangkah lagi untuk menyelesaikan pesanan</p>
        </div>
        <span class="badge badge-yellow">
            <i class="ri-time-line"></i>
            Menunggu Pembayaran
        </span>
    </header>
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h2 class="section-title">Total Pembayaran</h2>
        </div>
        <div class="card-skull-lg">
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
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h2 class="section-title">Detail Pembayaran</h2>
        </div>
        @if ($isBank)
        <div class="card">
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
                    Upload bukti transfer di bawah ini.
                </li>
            </ol>
        </div>
        @elseif ($isQris)
        <div class="card">
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
                    Upload bukti transfer di bawah ini.
                </li>
            </ol>
        </div>
        @endif
    </section>
    <hr class="section-divider">
    <section class="section" id="proof-section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h2 class="section-title">Bukti Transfer</h2>
        </div>
        @if (session('proof_status') === 'error')
        <div class="mb-3 flex items-start gap-2 rounded-2xl border border-red-200 bg-red-50 p-3 text-[11px] leading-snug text-red-700">
            <i class="ri-error-warning-fill shrink-0 text-base"></i>
            <span>{{ session('proof_message') }}</span>
        </div>
        @endif
        @if ($hasProof)
        <div class="card">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 ring-1 ring-emerald-200">
                    <i class="ri-shield-check-fill text-xl"></i>
                </span>
                <div class="flex-1">
                    <p class="text-xs font-bold text-foreground">Bukti sudah terkirim</p>
                    <p class="text-[10px] text-onyx">Admin akan verifikasi pesananmu segera.</p>
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
                <button type="button" class="icon-btn-xs" data-proof-clear aria-label="Hapus file">
                    <i class="ri-close-line text-sm"></i>
                </button>
            </div>
            @error('proof')
            <p class="mt-2 text-[10px] text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="btn-primary mt-3 w-full disabled:opacity-50" data-proof-submit disabled>
                <i class="ri-upload-2-line text-base"></i>
                <span data-proof-submit-label>Kirim Bukti Transfer</span>
            </button>
        </form>
    </section>
    <div class="cta-floating">
        <div class="cta-floating__inner">
            <div class="overflow-hidden" data-copy-toast>
                <div class="toast-panel" data-copy-toast-panel>
                    <span class="toast-icon-ok">
                        <i class="ri-check-line text-base"></i>
                    </span>
                    <div class="flex-1">
                        <div class="text-xs font-bold">Berhasil disalin</div>
                        <p class="mt-0.5 text-[11px] leading-snug text-white/80" data-copy-toast-message>Tempel ke aplikasi pembayaranmu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkout-success.js')
@endpush
