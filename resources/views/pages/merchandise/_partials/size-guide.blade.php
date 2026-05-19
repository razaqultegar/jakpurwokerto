<div class="overlay" aria-hidden="true" data-size-guide>
    <div class="overlay-backdrop--dark" data-size-guide-backdrop></div>
    <div class="absolute left-1/2 top-1/2 flex w-[calc(100%-2rem)] max-w-screen-sm -translate-x-1/2 -translate-y-1/2 scale-95 flex-col overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition duration-300 ease-out" aria-modal="true" data-size-guide-panel>
        <header class="flex items-center justify-between border-b border-mercury px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft">
                    <i class="ri-ruler-line text-lg"></i>
                </span>
                <div>
                    <h2 class="section-title">Panduan Ukuran</h2>
                    <p class="text-[10px] text-onyx">Cek sebelum memesan</p>
                </div>
            </div>
            <button type="button" class="icon-btn-sm" data-size-guide-close>
                <i class="ri-close-line text-lg"></i>
            </button>
        </header>
        <div class="p-4">
            <div class="relative">
                <div class="swiper size-guide-swiper" data-size-guide-swiper>
                    <div class="swiper-wrapper">
                        @foreach (['artboard5.jpg', 'artboard6.jpg'] as $i => $img)
                        <div class="swiper-slide">
                            <div class="relative aspect-square w-full overflow-hidden rounded-2xl bg-skull ring-1 ring-mercury">
                                <img src="{{ asset('build/medias/the-7ourney/' . $img) }}" class="h-full w-full object-contain" alt="Panduan ukuran #{{ $i + 1 }}" loading="lazy">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="icon-btn-nav left-2 size-guide-swiper-prev">
                    <i class="ri-arrow-left-s-line text-xl"></i>
                </button>
                <button type="button" class="icon-btn-nav right-2 size-guide-swiper-next">
                    <i class="ri-arrow-right-s-line text-xl"></i>
                </button>
                <div class="mt-3 flex items-center justify-center gap-1.5 size-guide-swiper-pagination"></div>
            </div>
            <div class="alert-soft mt-3">
                <i class="ri-information-2-line text-sm text-primary"></i>
                <p class="text-[10px] leading-relaxed text-onyx">Ukur tubuh dalam posisi rileks. Jika ragu antara dua ukuran, pilih yang lebih besar untuk kenyamanan.</p>
            </div>
        </div>
    </div>
</div>
