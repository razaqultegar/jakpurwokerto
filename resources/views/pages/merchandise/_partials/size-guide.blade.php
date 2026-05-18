<div class="pointer-events-none fixed inset-0 z-50" data-size-guide aria-hidden="true">
    <div class="absolute inset-0 bg-black/60 opacity-0 transition-opacity duration-300" data-size-guide-backdrop></div>
    <div class="absolute left-1/2 top-1/2 flex w-[calc(100%-2rem)] max-w-screen-sm -translate-x-1/2 -translate-y-1/2 scale-95 flex-col overflow-hidden rounded-3xl bg-white opacity-0 shadow-2xl transition duration-300 ease-out" data-size-guide-panel role="dialog" aria-modal="true" aria-label="Panduan Ukuran">
        <header class="flex items-center justify-between border-b border-mercury px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft">
                    <i class="ri-ruler-line text-lg"></i>
                </span>
                <div>
                    <h2 class="text-sm font-bold text-foreground">Panduan Ukuran</h2>
                    <p class="text-[10px] text-onyx">Cek sebelum memesan</p>
                </div>
            </div>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-skull text-foreground ring-1 ring-mercury" data-size-guide-close aria-label="Tutup panduan ukuran">
                <i class="ri-close-line text-lg"></i>
            </button>
        </header>
        <div class="p-4">
            <div class="relative">
                <div class="swiper size-guide-swiper" data-size-guide-swiper>
                    <div class="swiper-wrapper">
                        @foreach (['artboard6.jpg', 'artboard7.jpg'] as $i => $img)
                        <div class="swiper-slide">
                            <div class="relative aspect-square w-full overflow-hidden rounded-2xl bg-skull ring-1 ring-mercury">
                                <img src="{{ asset('build/medias/the-7ourney/' . $img) }}" class="h-full w-full object-contain" alt="Panduan ukuran #{{ $i + 1 }}" loading="lazy">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="size-guide-swiper-prev absolute left-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-foreground shadow-lg ring-1 ring-mercury transition disabled:pointer-events-none disabled:opacity-0">
                    <i class="ri-arrow-left-s-line text-xl"></i>
                </button>
                <button type="button" class="size-guide-swiper-next absolute right-2 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-foreground shadow-lg ring-1 ring-mercury transition disabled:pointer-events-none disabled:opacity-0">
                    <i class="ri-arrow-right-s-line text-xl"></i>
                </button>
                <div class="mt-3 flex items-center justify-center gap-1.5 size-guide-swiper-pagination"></div>
            </div>
            <div class="mt-3 flex items-start gap-2 rounded-xl border border-dashed border-primary-soft bg-primary-softer p-2.5">
                <i class="ri-information-2-line text-sm text-primary"></i>
                <p class="text-[10px] leading-relaxed text-onyx">Ukur tubuh dalam posisi rileks. Jika ragu antara dua ukuran, pilih yang lebih besar untuk kenyamanan.</p>
            </div>
        </div>
    </div>
</div>
