<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Data Pemesan</h2>
    </div>
    <div class="space-y-3">
        <div>
            <label for="checkout-name" class="mb-1 block text-[11px] font-semibold text-foreground">Nama Lengkap <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-onyx">
                    <i class="ri-user-3-line text-base"></i>
                </span>
                <input type="text" id="checkout-name" class="w-full rounded-xl border-2 border-mercury bg-white py-2.5 pl-9 pr-3 text-xs font-medium text-foreground placeholder:text-onyx/60 focus:border-primary focus:outline-none" name="name" placeholder="Contoh: Bayu Pratama" autocomplete="name" data-field="name" required>
            </div>
            <p class="mt-1 hidden text-[10px] text-red-600" data-error="name"></p>
        </div>
        <div>
            <label for="checkout-email" class="mb-1 block text-[11px] font-semibold text-foreground">Alamat Email <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-onyx">
                    <i class="ri-mail-line text-base"></i>
                </span>
                <input type="email" id="checkout-email" class="w-full rounded-xl border-2 border-mercury bg-white py-2.5 pl-9 pr-3 text-xs font-medium text-foreground placeholder:text-onyx/60 focus:border-primary focus:outline-none" name="email" placeholder="nama@email.com" autocomplete="email" data-field="email" required>
            </div>
            <p class="mt-1 hidden text-[10px] text-red-600" data-error="email"></p>
        </div>
        <div>
            <label for="checkout-phone" class="mb-1 block text-[11px] font-semibold text-foreground">No. Telepon / WhatsApp <span class="text-primary">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center gap-1 text-onyx">
                    <i class="ri-whatsapp-line text-base"></i>
                    <span class="text-[11px] font-semibold">+62</span>
                </span>
                <input type="tel" id="checkout-phone" class="w-full rounded-xl border-2 border-mercury bg-white py-2.5 pl-16 pr-3 text-xs font-medium text-foreground placeholder:text-onyx/60 focus:border-primary focus:outline-none" name="phone" placeholder="81234567890" autocomplete="tel" inputmode="numeric" data-field="phone" required>
            </div>
            <p class="mt-1 hidden text-[10px] text-red-600" data-error="phone"></p>
        </div>
        <div>
            <label for="checkout-note" class="mb-1 block text-[11px] font-semibold text-foreground">Catatan Tambahan <span class="font-normal text-onyx">(opsional)</span></label>
            <textarea id="checkout-note" class="w-full resize-none rounded-xl border-2 border-mercury bg-white px-3 py-2.5 text-xs font-medium text-foreground placeholder:text-onyx/60 focus:border-primary focus:outline-none" name="note" placeholder="Misal: tolong dibungkus rapi, atau request khusus lain..." rows="3" maxlength="240" data-field="note"></textarea>
            <p class="mt-1 flex items-center justify-between text-[10px] text-onyx">
                <span><i class="ri-edit-2-line"></i> Maks 240 karakter</span>
                <span><span data-note-count>0</span>/240</span>
            </p>
        </div>
    </div>
</section>
