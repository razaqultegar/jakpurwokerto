@extends('layouts.app')

@section('content')
    @include('pages.merchandise._partials.hero')
    @include('pages.merchandise._partials.info')
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    @include('pages.merchandise._partials.options')
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 py-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h3 class="text-sm font-bold text-foreground">Cerita di Balik Jersey</h3>
        </div>
        <p class="text-xs leading-relaxed text-onyx">{{ $merch['description'] }}</p>
        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
            <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-primary to-primary-lighter p-3 text-white shadow-md">
                <div class="pointer-events-none absolute -right-2 -top-2 h-10 w-10 rounded-full bg-white/15 blur-lg"></div>
                <div class="relative text-2xl font-black leading-none">7</div>
                <div class="relative mt-1 text-[10px] text-white/85">Tahun perjalanan</div>
            </div>
            <div class="rounded-2xl bg-primary-softer p-3 ring-1 ring-primary-soft">
                <div class="text-2xl font-black leading-none text-primary">1</div>
                <div class="mt-1 text-[10px] text-onyx">Jiwa keluarga</div>
            </div>
            <div class="rounded-2xl bg-primary-softer p-3 ring-1 ring-primary-soft">
                <div class="text-2xl font-black leading-none text-primary">∞</div>
                <div class="mt-1 text-[10px] text-onyx">Cinta Macan</div>
            </div>
        </div>
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 pb-6 pt-5">
        <div class="mb-3 flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h3 class="text-sm font-bold text-foreground">Spesifikasi</h3>
        </div>
        <div class="overflow-hidden rounded-2xl ring-1 ring-mercury">
            @foreach ($merch['specs'] as $i => $spec)
            <div class="flex items-center justify-between px-3 py-3 text-xs {{ $i % 2 === 0 ? 'bg-skull' : 'bg-white' }}">
                <span class="text-onyx">{{ $spec['label'] }}</span>
                <span class="font-semibold text-foreground">{{ $spec['value'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-4 flex items-start gap-2.5 rounded-2xl border border-dashed border-primary-soft bg-primary-softer p-3">
            <i class="ri-information-2-line text-base text-primary"></i>
            <p class="text-[11px] leading-relaxed text-onyx">Produk ini adalah <strong class="font-semibold text-foreground">Pre-Order</strong>. Proses produksi dimulai setelah masa PO ditutup. Estimasi pengiriman <strong class="font-semibold text-foreground">{{ $merch['estimated_ship'] }}</strong>.</p>
        </div>
    </section>
    @include('pages.merchandise._partials.cta')
    @include('pages.merchandise._partials.size-guide')
    @include('pages.merchandise._partials.share')
    @include('pages.merchandise._partials.drawer')
@endsection

@push('scripts')
    @vite([
        'resources/assets/plugins/swiper/swiper.css',
        'resources/assets/plugins/swiper/swiper.js',
        'resources/assets/js/pages/merchandise.js',
    ])
@endpush
