<section class="py-5">
    <div class="mb-3 flex items-center justify-between px-4">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h3 class="text-sm font-bold text-foreground">Galeri</h3>
        </div>
        <span class="text-[10px] text-onyx">{{ count($merch['gallery']) }} foto</span>
    </div>
    <div class="relative">
        <div class="swiper gallery-swiper px-4!" data-gallery-swiper>
            <div class="swiper-wrapper">
                @foreach (array_slice($merch['gallery'], 1) as $i => $img)
                <div class="swiper-slide w-32!">
                    <div class="relative aspect-3/4 w-full overflow-hidden rounded-2xl ring-1 ring-mercury">
                        <img src="{{ asset('build/' . $img) }}" class="h-full w-full object-cover" alt="Galeri #{{ $i + 2 }}" loading="lazy">
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-12 bg-linear-to-t from-black/50 to-transparent"></div>
                        <span class="absolute bottom-1.5 left-1.5 rounded-full bg-white/85 px-1.5 py-0.5 text-[9px] font-bold text-foreground backdrop-blur-sm">#{{ str_pad($i + 2, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <button type="button" class="gallery-swiper-prev absolute left-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-foreground shadow-lg ring-1 ring-mercury transition disabled:pointer-events-none disabled:opacity-0">
            <i class="ri-arrow-left-s-line text-xl"></i>
        </button>
        <button type="button" class="gallery-swiper-next absolute right-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-foreground shadow-lg ring-1 ring-mercury transition disabled:pointer-events-none disabled:opacity-0">
            <i class="ri-arrow-right-s-line text-xl"></i>
        </button>
    </div>
</section>
