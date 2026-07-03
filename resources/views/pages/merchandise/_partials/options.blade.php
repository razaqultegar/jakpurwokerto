@php
    $state = $merch['state'] ?? [];
    $isAfterEnd = $state['is_after_end'] ?? false;
@endphp

@unless ($isAfterEnd)
<hr class="section-divider">
<section class="section" data-merch-options>
    <div class="section-header-between">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="section-title">Pilih Kategori</h3>
        </div>
        <span class="section-meta">{{ count($merch['categories']) }} pilihan</span>
    </div>
    <div class="grid grid-cols-2 gap-2.5">
        @foreach ($merch['categories'] as $cat)
        <button type="button" class="choice-card" data-category="{{ $cat['key'] }}" aria-pressed="{{ $cat['active'] ? 'true' : 'false' }}" data-category-name="{{ $cat['name'] }}">
            <span class="option-icon">
                <i class="{{ $cat['icon'] }} text-xl"></i>
            </span>
            <span class="flex-1">
                <span class="option-label">{{ $cat['name'] }}</span>
                <span class="block text-[10px] text-onyx">{{ $cat['desc'] }}</span>
            </span>
            <span class="option-check">
                <i class="ri-check-line text-xs"></i>
            </span>
        </button>
        @endforeach
    </div>
    <div class="section-header-between mt-5">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="section-title">Pilih Model Lengan</h3>
        </div>
        <span class="section-meta">{{ count($merch['sleeves']) }} pilihan</span>
    </div>
    <div class="grid grid-cols-2 gap-2.5">
        @foreach ($merch['sleeves'] as $sleeve)
        <button type="button" class="choice-card" data-sleeve="{{ $sleeve['key'] }}" data-sleeve-name="{{ $sleeve['name'] }}" aria-pressed="{{ $sleeve['active'] ? 'true' : 'false' }}" data-sleeve-prices='@json($sleeve['prices'])'>
            <span class="option-icon">
                <i class="{{ $sleeve['icon'] }} text-xl"></i>
            </span>
            <span class="flex-1">
                <span class="option-label">{{ $sleeve['name'] }}</span>
                <span class="block text-[10px] text-onyx">{{ $sleeve['desc'] }}</span>
            </span>
            <span class="option-check">
                <i class="ri-check-line text-xs"></i>
            </span>
        </button>
        @endforeach
    </div>
    <div class="section-header-between mt-5">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="section-title">Pilih Ukuran</h3>
        </div>
        <button type="button" class="inline-flex items-center gap-1 text-[10px] font-semibold text-primary" data-size-guide-open>
            <i class="ri-ruler-line"></i>
            Panduan Ukuran
        </button>
    </div>
    <div class="grid grid-cols-5 gap-2">
        @foreach ($merch['sizes'] as $size)
        <button type="button" class="size-btn" aria-pressed="false" data-size="{{ $size }}" data-size-fee="{{ $size === 'Kustom' ? ($merch['custom_size_fee'] ?? 0) : 0 }}">{{ $size }}</button>
        @endforeach
    </div>
    <div class="mt-2 hidden" data-custom-size-wrap>
        <label class="block text-[10px] font-semibold text-foreground" for="custom-size-input">Tulis ukuran kustom (mis. 2XL, 3XL, 4XL)</label>
        <div class="mt-1.5 flex items-center gap-2 rounded-xl border-2 border-primary bg-white px-3 py-2 ring-1 ring-primary-soft focus-within:ring-2 focus-within:ring-primary">
            <i class="ri-edit-2-line text-base text-primary"></i>
            <input id="custom-size-input" type="text" maxlength="8" placeholder="2XL" class="flex-1 bg-transparent text-xs font-bold uppercase text-foreground placeholder:text-onyx focus:outline-none" data-custom-size-input>
            <span class="rounded-md bg-primary-softer px-1.5 py-0.5 text-[9px] font-bold text-primary">+Rp{{ number_format($merch['custom_size_fee'] ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>
    <p class="mt-2 text-[10px] text-onyx" data-size-helper>
        <i class="ri-information-2-line"></i>
        Silakan pilih ukuran terlebih dahulu.
    </p>
    <div class="mt-5 flex items-center justify-between rounded-2xl bg-skull p-3">
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
@endunless
