<div class="cta-floating">
    <div class="cta-floating__inner">
        <div class="cta-collapse" data-{{ $prefix }}-selected>
            <div class="cta-collapse__inner">
                <div class="flex translate-y-[calc(100%+1rem)] items-center gap-3 rounded-2xl bg-linear-to-r from-foreground via-primary to-primary-lighter px-3.5 py-2.5 text-white ring-1 ring-white/15 backdrop-blur-md transition-transform duration-300 ease-out" data-{{ $prefix }}-selected-panel>
                    <span class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 text-yellow-300 ring-1 ring-white/20">
                        <i class="ri-price-tag-3-fill text-lg"></i>
                        <span class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-yellow-300 px-1 text-[9px] font-black text-primary" data-{{ $prefix }}-selected-qty>1</span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wider text-white/70">
                            <i class="ri-checkbox-circle-fill text-yellow-300"></i>
                            <span>Pilihanmu</span>
                        </div>
                        <div class="truncate text-[11px] font-semibold text-white/90" data-{{ $prefix }}-selected-variant>{{ $selectedVariantDefault }}</div>
                    </div>
                    <div class="flex flex-col items-end leading-tight">
                        <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">Total</span>
                        <span class="text-base font-black text-white" data-{{ $prefix }}-selected-total>Rp0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="cta-collapse" data-{{ $prefix }}-alert>
            <div class="cta-collapse__inner">
                <div class="toast-panel" data-{{ $prefix }}-alert-panel>
                    <span class="toast-icon-warn">
                        <i class="ri-error-warning-fill text-base"></i>
                    </span>
                    <div class="flex-1">
                        <div class="text-xs font-bold" data-{{ $prefix }}-alert-title>{{ $alertTitle }}</div>
                        <p class="mt-0.5 text-[11px] leading-snug text-white/80" data-{{ $prefix }}-alert-message>{{ $alertMessage }}</p>
                    </div>
                    <button type="button" class="toast-close" data-{{ $prefix }}-alert-close>
                        <i class="ri-close-line text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="cta-card">
            <div class="flex items-center gap-2">
                <a href="https://wa.me/628975851952?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener" class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-foreground ring-2 ring-mercury shadow-sm transition active:scale-95" aria-label="Tanya admin via WhatsApp" title="{{ $waTitle }}">
                    <i class="ri-customer-service-2-fill text-lg"></i>
                </a>
                <button type="button" class="btn-outline-primary flex-1" data-cart-add>
                    <i class="ri-shopping-cart-2-line text-base"></i>
                    Keranjang
                </button>
                <button type="button" class="btn-primary-gradient flex-1" data-cart-order>
                    <i class="ri-shopping-bag-3-fill text-base"></i>
                    Beli Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
