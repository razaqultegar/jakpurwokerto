@php
    $galleryImages = ['gallery1.jpg', 'gallery2.jpg', 'gallery3.jpg', 'gallery4.jpg', 'gallery5.jpg', 'gallery6.jpg'];
@endphp

<section class="py-5">
    <div class="section-header-between gap-2 px-4">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="section-title">Galeri</h3>
        </div>
        <span class="text-[11px] font-semibold text-onyx">{{ count($galleryImages) }} foto</span>
    </div>
    <div class="relative">
        <div class="swiper gallery-swiper px-4!" data-gallery-swiper>
            <div class="swiper-wrapper">
                @foreach ($galleryImages as $i => $img)
                <div class="swiper-slide h-auto! w-36!">
                    <a href="{{ asset('build/medias/the-7ourney/' . $img) }}" class="group relative block aspect-3/4 overflow-hidden rounded-2xl bg-skull shadow-md ring-1 ring-mercury" target="_blank" rel="noopener">
                        <img src="{{ asset('build/medias/the-7ourney/' . $img) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="Galeri the 7ourney #{{ $i + 1 }}" loading="lazy">
                        <span class="pointer-events-none absolute inset-x-0 bottom-0 h-16 bg-linear-to-t from-foreground/60 to-transparent"></span>
                        <span class="pointer-events-none absolute bottom-2 left-2 inline-flex items-center rounded-md bg-white/95 px-1.5 py-0.5 text-[10px] font-black tracking-wider text-foreground shadow-sm">#{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        <button type="button" class="icon-btn-nav right-2 gallery-swiper-next">
            <i class="ri-arrow-right-s-line text-lg"></i>
        </button>
        <button type="button" class="icon-btn-nav left-2 gallery-swiper-prev">
            <i class="ri-arrow-left-s-line text-lg"></i>
        </button>
    </div>
</section>
