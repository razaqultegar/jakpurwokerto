<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h2 class="section-title">Metode Pengambilan</h2>
    </div>
    <div class="grid grid-cols-2 gap-2.5" data-shipping-group>
        @foreach ($checkout['shipping_methods'] as $i => $ship)
        <label class="option-card">
            <input type="radio" class="peer sr-only" name="shipping_method" value="{{ $ship['key'] }}" data-shipping="{{ $ship['key'] }}" {{ $loop->first ? 'checked' : '' }}>
            <span class="option-icon">
                <i class="{{ $ship['icon'] }} text-xl"></i>
            </span>
            <span class="option-label">{{ $ship['name'] }}</span>
            <span class="inline-flex items-center rounded-full bg-primary-softer px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-primary ring-1 ring-primary-soft">{{ $ship['badge'] }}</span>
            <span class="option-check">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
        @endforeach
    </div>
    <div class="mt-4 hidden" data-pickup-wrap>
        <label for="checkout-pickup-location" class="field-label">Kota Pengambilan <span class="text-primary">*</span></label>
        <div class="relative">
            <span class="field-icon">
                <i class="ri-map-pin-2-line text-base"></i>
            </span>
            <select id="checkout-pickup-location" class="field-control field-control--with-icon appearance-none pr-9" name="pickup_location" data-field="pickup_location">
                <option value="">-- Pilih kota --</option>
                @foreach ($checkout['pickup_locations'] as $loc)
                <option value="{{ $loc['key'] }}">{{ $loc['name'] }}</option>
                @endforeach
            </select>
            <span class="field-icon-right">
                <i class="ri-arrow-down-s-line text-base"></i>
            </span>
        </div>
        <p class="field-error" data-error="pickup_location"></p>
    </div>
    <div class="mt-4 hidden" data-address-wrap>
        <label for="checkout-address" class="field-label">Alamat Lengkap <span class="text-primary">*</span></label>
        <textarea id="checkout-address" class="field-control resize-none" name="address" placeholder="Tulis alamat lengkap: nama jalan, RT/RW, kelurahan, kecamatan, kota, kode pos, patokan." rows="4" maxlength="500" data-field="address"></textarea>
        <p class="mt-1 flex items-center justify-end text-[10px] text-onyx"><span data-address-count>0</span>/500</p>
        <p class="field-error" data-error="address"></p>
    </div>
    <div class="alert-soft mt-3" data-shipping-detail>
        <i class="ri-information-2-line shrink-0 text-base text-primary"></i>
        <p class="text-[11px] leading-relaxed text-onyx" data-shipping-detail-text>—</p>
    </div>
</section>
