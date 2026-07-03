@extends('layouts.app')

@section('content')
    <div data-event-meta data-event-slug="{{ $event['slug'] }}" data-event-name="{{ $event['name'] }}" data-event-image="{{ $event['gallery'][0] ?? '' }}" hidden></div>
    @if (session('status'))
    <div class="mx-4 mt-4 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-[12px] text-amber-800">
        <i class="ri-error-warning-line mt-[1px] text-base text-amber-600"></i>
        <span class="leading-relaxed">{{ session('status') }}</span>
    </div>
    @endif
    @include('pages.ticket._partials.hero')
    @include('pages.ticket._partials.info')
    @include('pages.ticket._partials.options')
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h3 class="section-title">Tentang Acara</h3>
        </div>
        <p class="whitespace-pre-line text-xs leading-relaxed text-onyx">{{ $event['description'] }}</p>
    </section>
    @include('pages.ticket._partials.maps')
    @include('pages.ticket._partials.rundown')
    @include('pages.ticket._partials.faq')
    @include('pages._shared.product-cta', [
        'prefix' => 'ticket',
        'waTitle' => 'Tanya seputar tiket',
        'waText' => 'Halo Admin, saya ingin tanya seputar tiket ' . $event['name'] . '.',
        'selectedVariantDefault' => 'Tiket',
        'alertTitle' => 'Tiket belum tersedia',
        'alertMessage' => 'Silakan cek kembali ketersediaan tiket sebelum melanjutkan.',
    ])
    @include('pages._shared.share-sheet', [
        'shareUrl' => url()->current(),
        'shareText' => ($event['name'] ?? 'Tiket') . ' - ' . ($event['subtitle'] ?? ''),
        'shareSubtitle' => 'Ajak teman beli tiket ini',
    ])
    @include('pages._shared.cart-drawer', [
        'emptyMessage' => 'Atur jumlah tiket, lalu tambahkan ke keranjang.',
    ])
@endsection

@push('scripts')
    @vite([
        'resources/assets/plugins/swiper/swiper.css',
        'resources/assets/plugins/swiper/swiper.js',
        'resources/assets/js/pages/ticket.js',
    ])
@endpush
