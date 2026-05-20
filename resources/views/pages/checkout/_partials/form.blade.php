<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h2 class="section-title">Data Pemesan</h2>
    </div>
    <div class="space-y-3">
        <div>
            <label for="checkout-name" class="field-label">Nama Lengkap <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="field-icon">
                    <i class="ri-user-3-line text-base"></i>
                </span>
                <input type="text" id="checkout-name" class="field-control field-control--with-icon" name="name" placeholder="Contoh: Bayu Pratama" autocomplete="name" data-field="name" required>
            </div>
            <p class="field-error" data-error="name"></p>
        </div>
        <div>
            <label for="checkout-email" class="field-label">Alamat Email <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="field-icon">
                    <i class="ri-mail-line text-base"></i>
                </span>
                <input type="email" id="checkout-email" class="field-control field-control--with-icon" name="email" placeholder="nama@email.com" autocomplete="email" data-field="email" required>
            </div>
            <p class="field-error" data-error="email"></p>
        </div>
        <div>
            <label for="checkout-phone" class="field-label">No. Telepon / WhatsApp <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="field-icon gap-1">
                    <i class="ri-whatsapp-line text-base"></i>
                    <span class="text-[11px] font-semibold">+62</span>
                </span>
                <input type="tel" id="checkout-phone" class="field-control pl-16 pr-3" name="phone" placeholder="81234567890" autocomplete="tel" inputmode="numeric" data-field="phone" required>
            </div>
            <p class="field-error" data-error="phone"></p>
        </div>
    </div>
</section>
