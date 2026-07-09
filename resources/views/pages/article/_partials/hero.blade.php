@php
    $heroSlides = [];
    if (!empty($article['image'])) $heroSlides[] = $article['image'];
    foreach ($article['gallery'] ?? [] as $g) {
        if (!in_array($g, $heroSlides, true)) $heroSlides[] = $g;
    }
@endphp
<section class="relative isolate overflow-hidden bg-foreground">
    <div class="relative aspect-square w-full overflow-hidden bg-skull" data-article-hero>
        <div class="swiper hero-swiper h-full w-full" data-hero-swiper>
            <div class="swiper-wrapper">
                @foreach ($heroSlides as $i => $img)
                <div class="swiper-slide relative h-full w-full">
                    <button type="button" class="block h-full w-full cursor-zoom-in" data-hero-zoom="{{ asset('build/' . $img) }}">
                        <img src="{{ asset('build/' . $img) }}" class="h-full w-full object-cover" alt="{{ $article['title'] }} #{{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-linear-to-b from-black/55 via-black/15 to-transparent"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-black/55 via-black/15 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between px-4 pt-4">
            <a href="{{ route('article.index') }}" class="icon-btn-glass">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <button type="button" class="icon-btn-glass" data-share-open>
                <i class="ri-share-forward-line text-lg"></i>
            </button>
        </div>
        @if (count($heroSlides) > 1)
        <button type="button" class="hero-swiper-prev absolute left-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/25 text-white ring-1 ring-white/30 backdrop-blur-md transition hover:bg-white/40">
            <i class="ri-arrow-left-s-line text-xl"></i>
        </button>
        <button type="button" class="hero-swiper-next absolute right-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/25 text-white ring-1 ring-white/30 backdrop-blur-md transition hover:bg-white/40">
            <i class="ri-arrow-right-s-line text-xl"></i>
        </button>
        <div class="hero-swiper-pagination absolute inset-x-0 bottom-3 z-10 flex justify-center gap-1.5"></div>
        <div class="absolute bottom-4 right-4 z-10 rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-semibold text-white ring-1 ring-white/20 backdrop-blur-sm" data-hero-counter>1 / {{ count($heroSlides) }}</div>
        @endif
    </div>
</section>
