@extends('layouts.app')

@section('content')
    <div data-merch-meta data-merch-slug="{{ $merch['slug'] }}" data-merch-image="{{ $merch['gallery'][0] ?? '' }}" hidden></div>
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
        <p class="whitespace-pre-line text-xs leading-relaxed text-onyx">{{ $merch['description'] }}</p>
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="px-4 mb-6 pt-5">
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
