<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Jenis Pembayaran</h2>
    </div>
    <div class="grid grid-cols-2 gap-2.5" data-payment-type-group>
        <label class="group relative flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-mercury bg-white p-3 text-center transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
            <input type="radio" class="peer sr-only" name="payment_type" value="dp" data-payment-type="dp" checked>
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft peer-checked:bg-primary peer-checked:text-white peer-checked:ring-primary">
                <i class="ri-coins-line text-xl"></i>
            </span>
            <span class="block text-xs font-bold text-foreground peer-checked:text-primary">DP 50%</span>
            <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
        <label class="group relative flex cursor-pointer flex-col items-center gap-2 rounded-2xl border-2 border-mercury bg-white p-3 text-center transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
            <input type="radio" class="peer sr-only" name="payment_type" value="full" data-payment-type="full">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary ring-1 ring-primary-soft peer-checked:bg-primary peer-checked:text-white peer-checked:ring-primary">
                <i class="ri-money-dollar-circle-line text-xl"></i>
            </span>
            <span class="block text-xs font-bold text-foreground peer-checked:text-primary">Full Payment</span>
            <span class="absolute right-2 top-2 hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                <i class="ri-check-line text-xs"></i>
            </span>
        </label>
    </div>
</section>
