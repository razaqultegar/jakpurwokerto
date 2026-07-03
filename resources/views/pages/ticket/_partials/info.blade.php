@php
    $minPrice = collect($event['tickets'])->min('price');
@endphp

<section class="px-4 pb-5 pt-5">
    <div class="flex items-start gap-2">
        <div class="flex-1">
            <h1 class="text-xl font-black leading-tight text-foreground" data-event-name>{{ $event['name'] }}</h1>
            <p class="mt-1 text-[11px] italic text-onyx">"{{ $event['subtitle'] }}"</p>
        </div>
    </div>
    <div class="mt-3 flex items-end gap-2">
        <div class="flex flex-col">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-onyx">Harga</span>
            <span class="mt-0.5 text-2xl font-black leading-none text-primary">
                Rp{{ number_format($minPrice, 0, ',', '.') }}
            </span>
        </div>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-2.5">
        <div class="rounded-2xl bg-skull p-3 ring-1 ring-mercury">
            <div class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-wider text-onyx">
                <i class="ri-calendar-line text-primary"></i>
                Tanggal
            </div>
            <div class="mt-1 text-xs font-bold text-foreground">{{ $event['date'] }}</div>
        </div>
        <div class="rounded-2xl bg-skull p-3 ring-1 ring-mercury">
            <div class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-wider text-onyx">
                <i class="ri-map-pin-line text-primary"></i>
                Lokasi
            </div>
            <div class="mt-1 text-xs font-bold text-foreground">{{ $event['venue'] }}</div>
        </div>
    </div>
</section>
