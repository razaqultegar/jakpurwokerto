@php
    $defaultTicket = collect($event['tickets'])->firstWhere('featured', true) ?? ($event['tickets'][0] ?? null);
@endphp

@if ($defaultTicket)
<hr class="section-divider">
<section class="section" data-ticket-options data-default-ticket data-ticket-key="{{ $defaultTicket['key'] }}" data-ticket-name="{{ $defaultTicket['name'] }}" data-ticket-price="{{ $defaultTicket['price'] }}" data-ticket-desc="{{ $defaultTicket['desc'] }}" data-ticket-note="{{ $defaultTicket['note'] }}">
    <div class="section-header-between mb-3">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="section-title">Jumlah Tiket</h3>
        </div>
    </div>
    <div class="flex items-center justify-between rounded-2xl bg-skull p-3">
        <div>
            <div class="text-xs font-semibold text-foreground">Jumlah</div>
            <div class="text-[10px] text-onyx">Pesan sesuai kebutuhanmu</div>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-foreground shadow-sm ring-1 ring-mercury transition disabled:cursor-not-allowed disabled:opacity-40" data-qty-decrement>
                <i class="ri-subtract-line"></i>
            </button>
            <span class="w-6 text-center text-base font-black text-foreground" data-qty-value>1</span>
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-foreground shadow-sm ring-1 ring-mercury transition" data-qty-increment>
                <i class="ri-add-line"></i>
            </button>
        </div>
    </div>
</section>
@endif
