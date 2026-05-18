<section class="px-4 pb-5 pt-5">
    <div class="flex items-start gap-2">
        <div class="flex-1">
            <h1 class="text-xl font-black leading-tight text-foreground" data-merch-name>{{ $merch['name'] }}</h1>
            <p class="mt-1 text-[11px] italic text-onyx">"{{ $merch['tagline'] }}"</p>
        </div>
    </div>
    <div class="mt-3 flex items-end gap-2">
        <div class="flex flex-col">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-onyx">Mulai dari</span>
            <span class="mt-0.5 text-2xl font-black leading-none text-primary">
                Rp{{ number_format($merch['price'], 0, ',', '.') }}
            </span>
        </div>
        <div class="ml-auto flex flex-col items-end">
            <span class="rounded bg-primary-soft px-1.5 py-0.5 text-[10px] font-bold text-primary leading-none">-{{ $merch['discount_percent'] }}%</span>
            <span class="mt-1 text-[11px] text-onyx line-through leading-none">Rp{{ number_format($merch['price_original'], 0, ',', '.') }}</span>
        </div>
    </div>
    @php
        $state = $merch['state'];
        $isSoldOut = $state['is_sold_out'];
        $isBeforeStart = $state['is_before_start'];
        $isAfterEnd = $state['is_after_end'];

        if ($isSoldOut) {
            $countdownLabel = 'Stok telah habis';
            $countdownStatus = 'Habis';
        } elseif ($isAfterEnd) {
            $countdownLabel = 'Pre-Order telah ditutup';
            $countdownStatus = 'Ditutup';
        } elseif ($isBeforeStart) {
            $countdownLabel = 'PO dibuka dalam';
            $countdownStatus = 'Segera';
        } else {
            $countdownLabel = 'PO berakhir dalam';
            $countdownStatus = 'Berlangsung';
        }

        $countdownGradient = $isSoldOut || $isAfterEnd
            ? 'linear-gradient(135deg, #374151 0%, #1f2937 100%)'
            : 'linear-gradient(135deg, #1f2937 0%, #111827 100%)';
    @endphp
    @unless ($isBeforeStart)
    <div class="mt-4 rounded-2xl {{ $isSoldOut || $isAfterEnd ? 'bg-skull ring-mercury' : 'bg-primary-softer ring-primary-soft' }} p-3 ring-1">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isSoldOut || $isAfterEnd ? 'bg-onyx' : 'bg-linear-to-br from-primary to-primary-lighter' }} text-white shadow-md">
                <i class="{{ $isSoldOut ? 'ri-shopping-bag-line' : 'ri-stack-line' }} text-xl"></i>
            </div>
            <div class="flex-1">
                <div class="text-xs font-bold text-foreground">@if ($isSoldOut) Stok habis terjual @else Stok terbatas {{ $merch['stock_limit'] }} pcs @endif</div>
                <div class="text-[10px] text-onyx">Sudah terjual <strong class="font-semibold text-foreground">{{ $merch['sold'] }}</strong> · Sisa <strong class="font-semibold text-foreground">{{ $state['stock_remaining'] }}</strong></div>
            </div>
        </div>
        <div class="mt-2.5 h-1.5 w-full overflow-hidden rounded-full bg-white/70 ring-1 {{ $isSoldOut || $isAfterEnd ? 'ring-mercury' : 'ring-primary-soft' }}">
            <div class="h-full rounded-full {{ $isSoldOut || $isAfterEnd ? 'bg-onyx' : 'bg-linear-to-r from-primary to-primary-lighter' }}" style="width: {{ $isSoldOut ? 100 : $state['stock_progress'] }}%"></div>
        </div>
    </div>
    @endunless
    <div class="relative mt-4 overflow-hidden rounded-2xl p-4 text-white shadow-lg" data-countdown data-start="{{ $merch['po_start'] }}" data-end="{{ $merch['po_end'] }}" style="background: {{ $countdownGradient }};">
        <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full {{ $isSoldOut || $isAfterEnd ? 'bg-white/10' : 'bg-yellow-300/15' }} blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-8 -left-4 h-24 w-24 rounded-full {{ $isSoldOut || $isAfterEnd ? 'bg-white/10' : 'bg-primary/30' }} blur-2xl"></div>
        <div class="relative mb-3 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-white">
                <i class="{{ $isSoldOut ? 'ri-shopping-bag-line' : ($isAfterEnd ? 'ri-time-line' : 'ri-timer-flash-fill') }} text-base {{ $isSoldOut || $isAfterEnd ? 'text-white/70' : 'text-yellow-300' }}"></i>
                <span data-countdown-label>{{ $countdownLabel }}</span>
            </span>
            <span class="rounded-full {{ $isSoldOut || $isAfterEnd ? 'bg-white/20 text-white' : 'bg-yellow-300 text-primary' }} px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider" data-countdown-status>{{ $countdownStatus }}</span>
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
