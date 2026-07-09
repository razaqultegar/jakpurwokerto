@extends('layouts.app')

@section('content')
    <header class="app-header">
        <a href="{{ route('home') }}" class="icon-btn">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div class="flex-1">
            <h1 class="app-header__title">Berita</h1>
            <p class="app-header__subtitle">Kabar terbaru seputar Jakmania Biro Purwokerto</p>
        </div>
    </header>

    <section class="section">
        <div class="relative mb-4">
            <span class="field-icon"><i class="ri-search-line text-base"></i></span>
            <input type="text" class="field-control field-control--with-icon" placeholder="Cari berita..." data-article-search>
        </div>

        <div data-article-list>
            @foreach ($articles as $article)
            @if ($article['featured'])
            <a href="{{ route('article.show', $article['slug']) }}" class="mb-3 block overflow-hidden rounded-2xl border border-mercury bg-white transition active:scale-[0.99]" data-article-item data-article-title="{{ Str::lower($article['title']) }}">
                <div class="relative h-36 w-full">
                    <img src="{{ asset('build/' . $article['image']) }}" alt="{{ $article['title'] }}" class="h-full w-full object-cover" loading="lazy">
                    <span class="absolute left-3 top-3 badge bg-yellow-300 text-primary uppercase tracking-wider shadow-lg">
                        <i class="ri-flashlight-fill"></i>
                        Sorotan
                    </span>
                </div>
                <div class="p-3">
                    <span class="text-[10px] text-onyx">{{ \Carbon\Carbon::parse($article['published_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
                    <h4 class="mt-1 text-sm font-bold leading-snug text-foreground">{{ $article['title'] }}</h4>
                    <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-onyx">{{ $article['excerpt'] }}</p>
                </div>
            </a>
            @else
            <a href="{{ route('article.show', $article['slug']) }}" class="mb-3 flex gap-3 rounded-2xl border border-mercury bg-white p-3 transition active:scale-[0.99]" data-article-item data-article-title="{{ Str::lower($article['title']) }}">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl">
                    <img src="{{ asset('build/' . $article['image']) }}" alt="{{ $article['title'] }}" class="h-full w-full object-cover" loading="lazy">
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] text-onyx">{{ \Carbon\Carbon::parse($article['published_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
                    <h4 class="mt-1 line-clamp-2 text-xs font-bold leading-snug text-foreground">{{ $article['title'] }}</h4>
                    <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-onyx">{{ $article['excerpt'] }}</p>
                </div>
            </a>
            @endif
            @endforeach
        </div>

        <div class="hidden flex-col items-center justify-center rounded-2xl border border-dashed border-skull bg-white p-6 text-center" data-article-empty>
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-skull/60 text-foreground/60">
                <i class="ri-file-search-line text-2xl"></i>
            </div>
            <h4 class="text-sm font-semibold text-foreground">Berita tidak ditemukan</h4>
            <p class="mt-1 text-xs text-foreground/60">Coba kata kunci lain.</p>
        </div>
    </section>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/article.js')
@endpush
