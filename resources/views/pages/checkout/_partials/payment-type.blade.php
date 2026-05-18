<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Jenis Pembayaran</h2>
    </div>
    <div class="grid grid-cols-1 gap-2.5" data-payment-type-group>
        <label class="group relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-mercury bg-white p-3 transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
            <input type="radio" name="payment_type" value="dp" class="peer sr-only" data-payment-type="dp" checked>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft peer-checked:bg-primary peer-checked:text-white peer-checked:ring-primary">
                <i class="ri-coins-line text-xl"></i>
            </span>
            <span class="flex-1">
                <span class="block text-xs font-bold text-foreground peer-checked:text-primary">Down Payment (DP 50%)</span>
                <span class="mt-0.5 block text-[10px] text-onyx">Bayar setengah dulu, sisanya saat barang siap kirim.</span>
            </span>
            <span class="text-right">
                <span class="block text-[10px] text-onyx">Bayar sekarang</span>
                <span class="block text-sm font-black text-primary" data-payment-amount="dp">-</span>
            </span>
            <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
        <label class="group relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-mercury bg-white p-3 transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
            <input type="radio" name="payment_type" value="full" class="peer sr-only" data-payment-type="full">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft peer-checked:bg-primary peer-checked:text-white peer-checked:ring-primary">
                <i class="ri-money-dollar-circle-line text-xl"></i>
            </span>
            <span class="flex-1">
                <span class="block text-xs font-bold text-foreground peer-checked:text-primary">Full Payment</span>
                <span class="mt-0.5 block text-[10px] text-onyx">Bayar lunas sekarang, tinggal tunggu barangnya datang.</span>
            </span>
            <span class="text-right">
                <span class="block text-[10px] text-onyx">Bayar sekarang</span>
                <span class="block text-sm font-black text-primary" data-payment-amount="full">-</span>
            </span>
            <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
    </div>
</section>
