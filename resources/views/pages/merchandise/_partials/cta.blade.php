@php
    $state = $merch['state'];
    $isBeforeStart = $state['is_before_start'];
@endphp

@unless ($isBeforeStart)
<div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 mx-auto max-w-screen-sm px-3 pb-3">
    <div class="pointer-events-auto mx-auto flex max-w-480 flex-col gap-2">
        <div class="overflow-hidden" data-merch-alert>
            <div class="flex w-full translate-y-[calc(100%+1rem)] items-start gap-2.5 rounded-2xl bg-foreground/95 px-3.5 py-3 text-white shadow-[0_10px_30px_-5px_rgba(0,0,0,0.45)] ring-1 ring-white/10 backdrop-blur-md transition-transform duration-300 ease-out" data-merch-alert-panel>
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-yellow-300 text-primary">
                    <i class="ri-error-warning-fill text-base"></i>
                </span>
                <div class="flex-1">
                    <div class="text-xs font-bold" data-merch-alert-title>Pilih ukuran dulu</div>
                    <p class="mt-0.5 text-[11px] leading-snug text-white/80" data-merch-alert-message>Silakan pilih ukuran jersey terlebih dahulu sebelum melanjutkan.</p>
                </div>
                <button type="button" class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/20" data-merch-alert-close>
                    <i class="ri-close-line text-sm"></i>
                </button>
            </div>
        </div>
        <div class="overflow-hidden" data-merch-selected>
            <div class="flex translate-y-[calc(100%+1rem)] items-center gap-3 rounded-2xl bg-linear-to-r from-foreground via-primary to-primary-lighter px-3.5 py-2.5 text-white shadow-[0_10px_30px_-5px_rgba(0,0,0,0.35)] ring-1 ring-white/15 backdrop-blur-md transition-transform duration-300 ease-out" data-merch-selected-panel>
                <span class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 text-yellow-300 ring-1 ring-white/20">
                    <i class="ri-price-tag-3-fill text-lg"></i>
                    <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-yellow-300 px-1 text-[9px] font-black text-primary" data-merch-selected-qty>1</span>
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-white/70">
                        <i class="ri-checkbox-circle-fill text-yellow-300"></i>
                        <span>Pilihanmu</span>
                    </div>
                    <div class="truncate text-[11px] font-semibold text-white/90" data-merch-selected-variant>Pilih opsi dulu</div>
                </div>
                <div class="flex flex-col items-end leading-tight">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">Total</span>
                    <span class="text-base font-black text-white" data-merch-selected-total>Rp0</span>
                </div>
            </div>
        </div>
        <div class="rounded-2xl bg-white/95 p-2 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.25)] ring-1 ring-mercury backdrop-blur-md">
            <div class="flex items-center gap-2">
                <button type="button" class="relative flex h-12 flex-1 items-center justify-center gap-2 rounded-xl border-2 border-primary bg-white text-sm font-bold text-primary" data-cart-add>
                    <i class="ri-shopping-cart-2-line text-base"></i>
                    Masukkan ke Keranjang
                </button>
                <button type="button" class="relative flex h-12 flex-1 items-center justify-center gap-2 overflow-hidden rounded-xl bg-linear-to-r from-primary via-primary-light to-primary-lighter text-sm font-bold text-white shadow-lg" data-cart-order>
                    <span class="pointer-events-none absolute -left-6 top-0 h-full w-12 -skew-x-12 bg-white/20"></span>
                    <i class="ri-shopping-bag-3-fill text-base"></i>
                    Beli Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endunless
