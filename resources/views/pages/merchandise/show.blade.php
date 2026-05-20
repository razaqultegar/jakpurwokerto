@extends('layouts.app')

@section('content')
    <div data-merch-meta data-merch-slug="{{ $merch['slug'] }}" data-merch-image="{{ $merch['gallery'][0] ?? '' }}" hidden></div>
    @include('pages.merchandise._partials.hero')
    @include('pages.merchandise._partials.info')
    <hr class="section-divider">
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
