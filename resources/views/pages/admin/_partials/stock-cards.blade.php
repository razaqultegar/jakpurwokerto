@foreach ($stockCards as $card)
    @php
        $tone = $card['remaining'] === null
            ? ['bar' => 'bg-mercury', 'text' => 'text-foreground']
            : ($card['remaining'] <= 0
                ? ['bar' => 'bg-red-500', 'text' => 'text-red-600']
                : ($card['limit'] > 0 && $card['remaining'] < ($card['limit'] * 0.2)
                    ? ['bar' => 'bg-amber-500', 'text' => 'text-amber-600']
                    : ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-600']));
    @endphp
    <div class="flex flex-wrap items-center gap-4 rounded-xl border border-mercury bg-white p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                <i class="ri-shirt-line text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Stok Terjual</p>
                <p class="truncate text-[13px] font-bold text-foreground">{{ $card['name'] }}</p>
            </div>
        </div>
        <div class="min-w-0 flex-1">
            @if ($card['limit'] > 0)
                <div class="flex items-center justify-between gap-2">
                    <p class="text-[13px] font-black {{ $tone['text'] }}">
                        {{ number_format($card['sold']) }}
                        <span class="text-[12px] font-bold text-onyx">/ {{ number_format($card['limit']) }}</span>
                    </p>
                    <p class="text-[11px] text-onyx">
                        Sisa <span class="font-bold {{ $tone['text'] }}">{{ number_format($card['remaining']) }}</span> · {{ $card['progress'] }}%
                    </p>
                </div>
                <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-skull">
                    <div class="h-full {{ $tone['bar'] }} transition-all" style="width: {{ $card['progress'] }}%"></div>
                </div>
            @else
                <p class="text-[13px] font-black {{ $tone['text'] }}">{{ number_format($card['sold']) }}</p>
                <p class="mt-0.5 text-[11px] text-onyx">Stok tidak diatur</p>
            @endif
        </div>
    </div>
@endforeach
