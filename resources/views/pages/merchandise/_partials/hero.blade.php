<section class="relative isolate overflow-hidden bg-foreground">
    <div class="relative aspect-square w-full overflow-hidden">
        <div class="swiper hero-swiper h-full w-full" data-hero-swiper>
            <div class="swiper-wrapper">
                @foreach ($merch['gallery'] as $i => $img)
                <div class="swiper-slide relative h-full w-full">
                    <img src="{{ asset('build/' . $img) }}" class="h-full w-full object-cover" alt="{{ $merch['name'] }} #{{ $i + 1 }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                </div>
                @endforeach
            </div>
        </div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-linear-to-b from-black/55 via-black/15 to-transparent"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-linear-to-t from-black/60 via-black/20 to-transparent"></div>
        <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between px-4 pt-4">
            <a href="{{ route('home') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25 text-white shadow ring-1 ring-white/30 backdrop-blur-md">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25 text-white shadow ring-1 ring-white/30 backdrop-blur-md">
                    <i class="ri-share-forward-line text-lg"></i>
                </button>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/25 text-white shadow ring-1 ring-white/30 backdrop-blur-md">
                    <i class="ri-heart-line text-lg"></i>
                </button>
            </div>
        </div>
        <div class="absolute left-4 top-4 z-10 mt-14 flex flex-col gap-1.5">
            <span class="inline-flex w-fit items-center gap-1 rounded-full bg-yellow-300 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-primary shadow-lg">
                <i class="ri-flashlight-fill"></i>
                {{ $merch['badge'] }}
            </span>
            <span class="inline-flex w-fit items-center gap-1 rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-white ring-1 ring-white/20 backdrop-blur-sm">
                <i class="ri-vip-crown-fill text-yellow-300"></i>
                {{ $merch['edition'] }}
            </span>
        </div>
        <div class="absolute right-4 top-4 z-10 mt-14 flex w-fit -rotate-6 flex-col items-center justify-center rounded-2xl bg-yellow-300 px-3 py-2 text-primary shadow-xl ring-2 ring-white/40">
            <span class="text-[9px] font-bold uppercase leading-none tracking-wider">Hemat</span>
            <span class="text-2xl font-black leading-none">{{ $merch['discount_percent'] }}%</span>
        </div>
        <button type="button" class="hero-swiper-prev absolute left-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/25 text-white ring-1 ring-white/30 backdrop-blur-md transition hover:bg-white/40">
            <i class="ri-arrow-left-s-line text-xl"></i>
        </button>
        <button type="button" class="hero-swiper-next absolute right-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/25 text-white ring-1 ring-white/30 backdrop-blur-md transition hover:bg-white/40">
            <i class="ri-arrow-right-s-line text-xl"></i>
        </button>
        <div class="hero-swiper-pagination absolute inset-x-0 bottom-3 z-10 flex justify-center gap-1.5"></div>
        <div class="absolute bottom-4 right-4 z-10 rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-semibold text-white ring-1 ring-white/20 backdrop-blur-sm" data-hero-counter>1 / {{ count($merch['gallery']) }}</div>
    </div>
</section>
