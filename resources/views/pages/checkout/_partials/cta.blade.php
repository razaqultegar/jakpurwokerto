<div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 mx-auto max-w-screen-sm px-3 pb-3">
    <div class="pointer-events-auto mx-auto flex max-w-480 flex-col gap-2">
        <div class="overflow-hidden" data-checkout-alert aria-live="polite">
            <div class="flex w-full translate-y-[calc(100%+1rem)] items-start gap-2.5 rounded-2xl bg-foreground/95 px-3.5 py-3 text-white shadow-[0_10px_30px_-5px_rgba(0,0,0,0.45)] ring-1 ring-white/10 backdrop-blur-md transition-transform duration-300 ease-out" data-checkout-alert-panel role="status">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-yellow-300 text-primary">
                    <i class="ri-error-warning-fill text-base"></i>
                </span>
                <div class="flex-1">
                    <div class="text-xs font-bold" data-checkout-alert-title>Data belum lengkap</div>
                    <p class="mt-0.5 text-[11px] leading-snug text-white/80" data-checkout-alert-message>Mohon lengkapi data pemesan sebelum melanjutkan.</p>
                </div>
                <button type="button" class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-white ring-1 ring-white/20" aria-label="Tutup pemberitahuan" data-checkout-alert-close>
                    <i class="ri-close-line text-sm"></i>
                </button>
            </div>
        </div>
        <div class="rounded-2xl bg-white/95 p-2 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.25)] ring-1 ring-mercury backdrop-blur-md">
            <div class="flex items-center gap-3 px-2 py-1.5">
                <div class="flex-1">
                    <div class="text-[10px] text-onyx" data-pay-label>Bayar sekarang (DP 50%)</div>
                    <div class="text-base font-black text-primary" data-pay-amount>-</div>
                </div>
                <button type="submit" class="relative flex h-12 items-center justify-center gap-2 overflow-hidden rounded-xl bg-linear-to-r from-primary via-primary-light to-primary-lighter px-5 text-sm font-bold text-white shadow-lg">
                    <span class="pointer-events-none absolute -left-6 top-0 h-full w-12 -skew-x-12 bg-white/20"></span>
                    <i class="ri-shield-check-fill text-base"></i>
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
