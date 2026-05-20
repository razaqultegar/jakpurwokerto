<div class="overlay" aria-hidden="true" data-cart-drawer>
    <div class="overlay-backdrop--dark" data-cart-backdrop></div>
    <aside class="absolute right-0 top-0 flex h-full w-[88%] max-w-sm translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300 ease-out" aria-modal="true" data-cart-panel>
        <header class="flex items-center justify-between border-b border-mercury px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft">
                    <i class="ri-shopping-cart-2-line text-lg"></i>
                </span>
                <div>
                    <h2 class="section-title">Keranjang</h2>
                    <p class="text-[10px] text-onyx"><span data-cart-summary>0</span> item siap dipesan</p>
                </div>
            </div>
            <button type="button" class="icon-btn-sm" data-cart-close>
                <i class="ri-close-line text-lg"></i>
            </button>
        </header>
        <div class="flex-1 overflow-y-auto px-4 py-4">
            <div class="flex h-full flex-col items-center justify-center gap-3 text-center" data-cart-empty>
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-softer text-primary ring-1 ring-primary-soft">
                    <i class="ri-shopping-bag-3-line text-3xl"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-foreground">Keranjang masih kosong</div>
                    <p class="mt-1 text-[11px] leading-relaxed text-onyx">Pilih kategori dan ukuran jersey, lalu tambahkan ke keranjang.</p>
                </div>
            </div>
            <ul class="hidden flex-col gap-2.5" data-cart-list></ul>
        </div>
        <footer class="border-t border-mercury bg-white px-4 py-3">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-[11px] text-onyx">Total</span>
                <span class="text-base font-black text-foreground" data-cart-total>Rp0</span>
            </div>
            <button type="button" class="btn-primary-gradient w-full disabled:cursor-not-allowed disabled:opacity-60" data-cart-checkout disabled>
                <i class="ri-secure-payment-line text-base"></i>
                Selesaikan Pesanan
            </button>
        </footer>
    </aside>
</div>
