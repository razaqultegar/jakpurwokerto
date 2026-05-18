<section class="px-4 pb-5 pt-5">
    <div class="flex items-start gap-2">
        <div class="flex-1">
            <h1 class="text-xl font-black leading-tight text-foreground" data-merch-name>{{ $merch['name'] }}</h1>
            <p class="mt-1 text-[11px] italic text-onyx">"{{ $merch['tagline'] }}"</p>
        </div>
    </div>
    <div class="mt-3 flex items-end gap-2">
        <span class="text-3xl font-black text-primary leading-none" data-merch-price data-price="{{ $merch['price'] }}">Rp{{ number_format($merch['price'], 0, ',', '.') }}</span>
        <div class="flex flex-col">
            <span class="rounded bg-primary-soft px-1.5 py-0.5 text-[10px] font-bold text-primary leading-none">-{{ $merch['discount_percent'] }}%</span>
            <span class="mt-1 text-[11px] text-onyx line-through leading-none">Rp{{ number_format($merch['price_original'], 0, ',', '.') }}</span>
        </div>
    </div>
    @php
        $stockLimit = $merch['stock_limit'] ?? 0;
        $sold = $merch['sold'] ?? 0;
        $remaining = max(0, $stockLimit - $sold);
        $progress = $stockLimit > 0 ? min(100, round(($sold / $stockLimit) * 100)) : 0;
    @endphp
    <div class="mt-4 rounded-2xl bg-primary-softer p-3 ring-1 ring-primary-soft">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-primary to-primary-lighter text-white shadow-md">
                <i class="ri-stack-line text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="text-xs font-bold text-foreground">Stok terbatas {{ $stockLimit }} pcs</div>
                <div class="text-[10px] text-onyx">Sudah terjual <strong class="font-semibold text-foreground">{{ $sold }}</strong> · Sisa <strong class="font-semibold text-foreground">{{ $remaining }}</strong></div>
            </div>
            <span class="rounded-full bg-primary px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-white">Tersedia</span>
        </div>
        <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-white/70 ring-1 ring-primary-soft">
            <div class="h-full rounded-full bg-linear-to-r from-primary to-primary-lighter" style="width: {{ $progress }}%"></div>
        </div>
    </div>
    <div class="relative mt-4 overflow-hidden rounded-2xl p-4 text-white shadow-lg" data-countdown data-start="{{ $merch['po_start'] }}" data-end="{{ $merch['po_end'] }}" style="background: linear-gradient(135deg, #1f2937 0%, #111827 100%);">
        <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-yellow-300/15 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-8 -left-4 h-24 w-24 rounded-full bg-primary/30 blur-2xl"></div>
        <div class="relative mb-3 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-white">
                <i class="ri-timer-flash-fill text-base text-yellow-300"></i>
                <span data-countdown-label>Berakhir dalam</span>
            </span>
            <span class="rounded-full bg-yellow-300 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider text-primary" data-countdown-status>Berlangsung</span>
        </div>
        <div class="relative grid grid-cols-4 gap-2 text-center">
            <div class="rounded-xl bg-white/10 py-2.5 ring-1 ring-white/15 backdrop-blur-sm">
                <div class="text-xl font-black leading-none text-white" data-countdown-days>00</div>
                <div class="mt-1 text-[9px] uppercase tracking-wider text-white/60">Hari</div>
            </div>
            <div class="rounded-xl bg-white/10 py-2.5 ring-1 ring-white/15 backdrop-blur-sm">
                <div class="text-xl font-black leading-none text-white" data-countdown-hours>00</div>
                <div class="mt-1 text-[9px] uppercase tracking-wider text-white/60">Jam</div>
            </div>
            <div class="rounded-xl bg-white/10 py-2.5 ring-1 ring-white/15 backdrop-blur-sm">
                <div class="text-xl font-black leading-none text-white" data-countdown-minutes>00</div>
                <div class="mt-1 text-[9px] uppercase tracking-wider text-white/60">Menit</div>
            </div>
            <div class="rounded-xl bg-white/10 py-2.5 ring-1 ring-white/15 backdrop-blur-sm">
                <div class="text-xl font-black leading-none text-white" data-countdown-seconds>00</div>
                <div class="mt-1 text-[9px] uppercase tracking-wider text-white/60">Detik</div>
            </div>
        </div>
        <div class="relative mt-3 flex items-center gap-1.5 text-[10px] text-white/70">
            <i class="ri-truck-line"></i>
            <span>Estimasi pengiriman: <strong class="font-semibold text-white">{{ $merch['estimated_ship'] }}</strong></span>
        </div>
    </div>
</section>
