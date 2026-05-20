<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h2 class="section-title">Ringkasan Pesanan</h2>
    </div>

    <div class="space-y-2" data-cart-items>
        {{-- diisi via JS dari localStorage --}}
    </div>

    <div class="hidden items-center gap-3 rounded-2xl border border-dashed border-mercury bg-skull p-4 text-center" data-cart-empty>
        <div class="flex w-full flex-col items-center gap-1.5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-onyx ring-1 ring-mercury">
                <i class="ri-shopping-cart-line text-lg"></i>
            </span>
            <p class="text-xs font-bold text-foreground">Keranjang kosong</p>
            <p class="text-[10px] text-onyx">Yuk pilih merchandise dulu sebelum lanjut.</p>
            <a href="{{ route('home') }}" class="badge mt-1 bg-primary text-white px-3 py-1.5 text-[11px]">
                <i class="ri-arrow-left-line"></i>
                Cari Merchandise
            </a>
        </div>
    </div>

    <dl class="mt-3 space-y-1.5 text-[11px]">
        <div class="flex items-center justify-between">
            <dt class="text-onyx">Subtotal (<span data-cart-count>0</span> item)</dt>
            <dd class="font-semibold text-foreground" data-subtotal data-value="0">Rp0</dd>
        </div>
        <div class="my-2 h-px w-full bg-mercury"></div>
        <div class="flex items-center justify-between">
            <dt class="text-xs font-bold text-foreground">Total Tagihan</dt>
            <dd class="text-base font-black text-primary" data-total>Rp0</dd>
        </div>
    </dl>
    <div data-cart-hidden-inputs></div>
</section>
