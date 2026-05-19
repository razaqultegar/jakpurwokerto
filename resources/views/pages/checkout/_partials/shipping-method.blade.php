<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Metode Pengambilan</h2>
    </div>
    <div class="grid grid-cols-2 gap-2.5" data-shipping-group>
        @foreach ($checkout['shipping_methods'] as $i => $ship)
        <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-mercury bg-white p-3 text-center transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
            <input type="radio" class="peer sr-only" name="shipping_method" value="{{ $ship['key'] }}" data-shipping="{{ $ship['key'] }}" {{ $loop->first ? 'checked' : '' }}>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft peer-checked:bg-primary peer-checked:text-white peer-checked:ring-primary">
                <i class="{{ $ship['icon'] }} text-xl"></i>
            </span>
            <span class="block text-xs font-bold text-foreground peer-checked:text-primary">{{ $ship['name'] }}</span>
            <span class="inline-flex items-center rounded-full bg-primary-softer px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-primary ring-1 ring-primary-soft">{{ $ship['badge'] }}</span>
            <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
        @endforeach
    </div>
    <div class="mt-4 hidden" data-pickup-wrap>
        <label for="checkout-pickup-location" class="mb-1 block text-[11px] font-semibold text-foreground">Kota Pengambilan <span class="text-primary">*</span></label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-onyx">
                <i class="ri-map-pin-2-line text-base"></i>
            </span>
            <select id="checkout-pickup-location" class="w-full appearance-none rounded-xl border-2 border-mercury bg-white py-2.5 pl-9 pr-9 text-xs font-medium text-foreground focus:border-primary focus:outline-none" name="pickup_location" data-field="pickup_location">
                <option value="">-- Pilih kota --</option>
                @foreach ($checkout['pickup_locations'] as $loc)
                <option value="{{ $loc['key'] }}">{{ $loc['name'] }}</option>
                @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-onyx">
                <i class="ri-arrow-down-s-line text-base"></i>
            </span>
        </div>
        <p class="mt-1 hidden text-[10px] text-red-600" data-error="pickup_location"></p>
    </div>
    <div class="mt-4 hidden" data-address-wrap>
        <label for="checkout-address" class="mb-1 block text-[11px] font-semibold text-foreground">Alamat Lengkap <span class="text-primary">*</span></label>
        <textarea id="checkout-address" class="w-full resize-none rounded-xl border-2 border-mercury bg-white py-2.5 px-3 text-xs font-medium text-foreground placeholder:text-onyx/60 focus:border-primary focus:outline-none" name="address" placeholder="Tulis alamat lengkap: nama jalan, RT/RW, kelurahan, kecamatan, kota, kode pos, patokan." rows="4" maxlength="500" data-field="address"></textarea>
        <p class="mt-1 flex items-center justify-end text-[10px] text-onyx"><span data-address-count>0</span>/500</p>
        <p class="mt-1 hidden text-[10px] text-red-600" data-error="address"></p>
    </div>
    <div class="mt-3 flex items-start gap-2 rounded-2xl border border-dashed border-primary-soft bg-primary-softer p-3" data-shipping-detail>
        <i class="ri-information-2-line shrink-0 text-base text-primary"></i>
        <p class="text-[11px] leading-relaxed text-onyx" data-shipping-detail-text>—</p>
    </div>
</section>
