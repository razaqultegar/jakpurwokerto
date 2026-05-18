<section class="px-4 py-5" data-merch-options>
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h3 class="text-sm font-bold text-foreground">Pilih Kategori</h3>
        </div>
        <span class="text-[10px] text-onyx">{{ count($merch['categories']) }} pilihan</span>
    </div>
    <div class="grid grid-cols-2 gap-2.5">
        @foreach ($merch['categories'] as $cat)
        <button type="button" class="group relative flex items-center gap-3 rounded-2xl border-2 p-3 text-left transition aria-pressed:border-primary aria-pressed:bg-primary-softer aria-pressed:shadow-sm border-mercury bg-white" data-category="{{ $cat['key'] }}" aria-pressed="{{ $cat['active'] ? 'true' : 'false' }}">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft group-aria-pressed:bg-primary group-aria-pressed:text-white group-aria-pressed:ring-primary">
                <i class="{{ $cat['icon'] }} text-xl"></i>
            </span>
            <span class="flex-1">
                <span class="block text-xs font-bold text-foreground group-aria-pressed:text-primary">{{ $cat['name'] }}</span>
                <span class="block text-[10px] text-onyx">{{ $cat['desc'] }}</span>
            </span>
            <span class="hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow group-aria-pressed:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </button>
        @endforeach
    </div>
    <div class="mb-3 mt-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="h-5 w-1 rounded-full bg-primary"></span>
            <h3 class="text-sm font-bold text-foreground">Pilih Ukuran</h3>
        </div>
        <a href="#" class="inline-flex items-center gap-1 text-[10px] font-semibold text-primary">
            <i class="ri-ruler-line"></i>
            Panduan Ukuran
        </a>
    </div>
    <div class="grid grid-cols-6 gap-2">
        @foreach ($merch['sizes'] as $size)
        <button type="button" class="rounded-xl border-2 border-mercury bg-white py-2.5 text-xs font-bold text-foreground transition aria-pressed:border-primary aria-pressed:bg-primary aria-pressed:text-white aria-pressed:shadow-md" data-size="{{ $size }}" aria-pressed="false">{{ $size }}</button>
        @endforeach
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
