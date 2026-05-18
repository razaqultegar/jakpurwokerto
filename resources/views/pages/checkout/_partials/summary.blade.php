<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Ringkasan Pesanan</h2>
    </div>
    <div class="flex items-center gap-3 rounded-2xl bg-skull p-3 ring-1 ring-mercury">
        <img src="{{ asset('build/' . $checkout['item']['image']) }}" alt="{{ $checkout['item']['name'] }}" class="h-16 w-16 shrink-0 rounded-xl object-cover ring-1 ring-mercury">
        <div class="flex-1">
            <div class="text-xs font-bold text-foreground">{{ $checkout['item']['name'] }}</div>
            <div class="mt-0.5 text-[10px] text-onyx">{{ $checkout['item']['category'] }} · Ukuran {{ $checkout['item']['size'] }}</div>
            <div class="mt-1 inline-flex items-center gap-2">
                <span class="rounded-md bg-white px-1.5 py-0.5 text-[10px] font-semibold text-foreground ring-1 ring-mercury">x{{ $checkout['item']['qty'] }}</span>
                <span class="text-[11px] font-bold text-primary">Rp{{ number_format($checkout['item']['price'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <dl class="mt-3 space-y-1.5 text-[11px]">
        <div class="flex items-center justify-between">
            <dt class="text-onyx">Subtotal</dt>
            <dd class="font-semibold text-foreground" data-subtotal data-value="{{ $checkout['item']['price'] * $checkout['item']['qty'] }}">Rp{{ number_format($checkout['item']['price'] * $checkout['item']['qty'], 0, ',', '.') }}</dd>
        </div>
        <div class="my-2 h-px w-full bg-mercury"></div>
        <div class="flex items-center justify-between">
            <dt class="text-xs font-bold text-foreground">Total Tagihan</dt>
            <dd class="text-base font-black text-primary" data-total>Rp{{ number_format($checkout['item']['price'] * $checkout['item']['qty'], 0, ',', '.') }}</dd>
        </div>
    </dl>
    <p class="mt-2 flex items-center gap-1 text-[10px] text-onyx">
        <i class="ri-truck-line"></i>
        Ongkos kirim akan dihitung & dikonfirmasi terpisah via WhatsApp.
    </p>
</section>
