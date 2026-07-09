@extends('layouts.app')

@section('content')
    @include('pages.article._partials.hero')
    @include('pages.article._partials.info')
    <section class="section">
        <article class="prose-article text-xs leading-relaxed text-onyx">
            @markdown($article['body'])
        </article>
    </section>

    @if (!empty($article['attachments']))
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h3 class="section-title">Lampiran Berkas</h3>
        </div>
        <div class="flex flex-col gap-2.5">
            @foreach ($article['attachments'] as $file)
            <a href="{{ $file['url'] }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl border border-mercury bg-white p-3 transition active:scale-[0.99] hover:border-primary-soft hover:bg-primary-softer/40">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600">
                    <i class="ri-file-pdf-2-fill text-xl"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold leading-snug text-foreground">{{ $file['label'] }}</p>
                    @if (!empty($file['description']))
                    <p class="mt-0.5 line-clamp-2 text-[11px] leading-snug text-onyx">{{ $file['description'] }}</p>
                    @endif
                </div>
                <i class="ri-external-link-line shrink-0 text-lg text-onyx"></i>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    @if (count($related))
    <hr class="section-divider">
    <section class="section">
        <div class="section-header">
            <span class="section-bar"></span>
            <h3 class="section-title">Berita Lainnya</h3>
        </div>
        <div class="flex flex-col gap-2.5">
            @foreach ($related as $item)
            <a href="{{ route('article.show', $item['slug']) }}" class="flex items-center gap-3 rounded-2xl border border-mercury bg-white p-3 transition active:scale-[0.99] hover:border-primary-soft hover:bg-primary-softer/40">
                <div class="h-14 w-14 shrink-0 overflow-hidden rounded-xl">
                    <img src="{{ asset('build/' . $item['image']) }}" alt="{{ $item['title'] }}" class="h-full w-full object-cover" loading="lazy">
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] text-onyx">{{ \Carbon\Carbon::parse($item['published_at'])->translatedFormat('d M Y, H:i') }} WIB</span>
                    <p class="mt-1 line-clamp-2 text-xs font-bold leading-snug text-foreground">{{ $item['title'] }}</p>
                </div>
                <i class="ri-arrow-right-s-line shrink-0 text-lg text-onyx"></i>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    @include('pages._shared.share-sheet', [
        'shareUrl' => url()->current(),
        'shareText' => $article['title'],
        'shareSubtitle' => 'Ajak teman baca berita ini',
    ])
@endsection

@push('scripts')
    @vite([
        'resources/assets/plugins/swiper/swiper.css',
        'resources/assets/plugins/swiper/swiper.js',
        'resources/assets/js/pages/article.js',
    ])
@endpush
