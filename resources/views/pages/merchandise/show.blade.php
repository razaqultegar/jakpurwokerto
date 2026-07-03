@extends('layouts.app')

@section('content')
    <div data-merch-meta data-merch-slug="{{ $merch['slug'] }}" data-merch-image="{{ $merch['gallery'][0] ?? '' }}" hidden></div>
    @if (session('status'))
        <div class="mx-4 mt-4 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-[12px] text-amber-800">
            <i class="ri-error-warning-line mt-[1px] text-base text-amber-600"></i>
            <span class="leading-relaxed">{{ session('status') }}</span>
        </div>
    @endif
    @include('pages.merchandise._partials.hero')
    @include('pages.merchandise._partials.info')
    @include('pages.merchandise._partials.options')
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h3 class="section-title">Deskripsi</h3>
        </div>
        <p class="whitespace-pre-line text-xs leading-relaxed text-onyx">{{ $merch['description'] }}</p>
    </section>
    <hr class="section-divider">
    @include('pages.merchandise._partials.gallery')
    <hr class="section-divider">
    <section class="px-4 mb-6 pt-5">
        <div class="section-header">
            <span class="section-bar"></span>
            <h3 class="section-title">Spesifikasi</h3>
        </div>
        <div class="overflow-hidden rounded-2xl ring-1 ring-mercury">
            @foreach ($merch['specs'] as $i => $spec)
            <div class="flex items-center justify-between px-3 py-3 text-xs {{ $i % 2 === 0 ? 'bg-skull' : 'bg-white' }}">
                <span class="text-onyx">{{ $spec['label'] }}</span>
                <span class="font-semibold text-foreground">{{ $spec['value'] }}</span>
            </div>
            @endforeach
        </div>
    </section>
    @unless ($merch['state']['is_before_start'] || $merch['state']['is_after_end'])
    @include('pages._shared.product-cta', [
        'prefix' => 'merch',
        'waTitle' => 'Tanya seputar produk',
        'waText' => 'Halo Admin, saya ingin tanya seputar produk merchandise ' . $merch['name'] . '.',
        'selectedVariantDefault' => 'Pilih opsi dulu',
        'alertTitle' => 'Pilih ukuran dulu',
        'alertMessage' => 'Silakan pilih ukuran jersey terlebih dahulu sebelum melanjutkan.',
    ])
    @endunless
    @include('pages.merchandise._partials.size-guide')
    @include('pages._shared.share-sheet', [
        'shareUrl' => url()->current(),
        'shareText' => ($merch['name'] ?? 'Merchandise') . ' - ' . ($merch['tagline'] ?? ''),
        'shareSubtitle' => 'Ajak teman lihat merchandise ini',
    ])
    @unless ($merch['state']['is_after_end'] ?? false)
    @include('pages._shared.cart-drawer', [
        'emptyMessage' => 'Pilih kategori dan ukuran jersey, lalu tambahkan ke keranjang.',
    ])
    @endunless
@endsection

@push('scripts')
    @vite([
        'resources/assets/plugins/swiper/swiper.css',
        'resources/assets/plugins/swiper/swiper.js',
        'resources/assets/js/pages/merchandise.js',
    ])
@endpush
