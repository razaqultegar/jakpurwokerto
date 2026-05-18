<section class="px-4 py-5">
    <div class="mb-3 flex items-center gap-2">
        <span class="h-5 w-1 rounded-full bg-primary"></span>
        <h2 class="text-sm font-bold text-foreground">Metode Pembayaran</h2>
    </div>

    <div class="space-y-3" data-method-group>
        <div>
            <div class="mb-1.5 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-onyx">
                <i class="ri-bank-line text-xs"></i>
                Bank Transfer
            </div>
            <div class="space-y-2">
                @foreach ($checkout['banks'] as $i => $bank)
                <label class="relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-mercury bg-white p-3 transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
                    <input type="radio" name="payment_method" value="bank:{{ $bank['key'] }}" class="peer sr-only" {{ $i === 0 ? 'checked' : '' }}>
                    <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg {{ $bank['color'] }} text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
                        {{ $bank['logo_text'] }}
                    </span>
                    <span class="flex-1">
                        <span class="block text-xs font-bold text-foreground">{{ $bank['name'] }}</span>
                        <span class="mt-0.5 block text-[10px] text-onyx">Transfer ke rekening JakPurwokerto</span>
                    </span>
                    <span class="hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                        <i class="ri-check-line text-xs"></i>
                    </span>
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <div class="mb-1.5 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-onyx">
                <i class="ri-qr-code-line text-xs"></i>
                QRIS
            </div>
            <div class="space-y-2">
                <label class="relative flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-mercury bg-white p-3 transition has-[input:checked]:border-primary has-[input:checked]:bg-primary-softer has-[input:checked]:shadow-sm">
                    <input type="radio" name="payment_method" value="qris:dana" class="peer sr-only">
                    <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg bg-sky-500 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
                        DANA
                    </span>
                    <span class="flex-1">
                        <span class="block text-xs font-bold text-foreground">QRIS {{ $checkout['qris']['name'] }}</span>
                        <span class="mt-0.5 block text-[10px] text-onyx">Scan via DANA, GoPay, OVO, ShopeePay, mobile banking</span>
                    </span>
                    <span class="hidden h-5 w-5 items-center justify-center rounded-full bg-primary text-white shadow peer-checked:flex">
                        <i class="ri-check-line text-xs"></i>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="mt-3 flex items-start gap-2 rounded-2xl border border-dashed border-primary-soft bg-primary-softer p-3">
        <i class="ri-information-2-line shrink-0 text-base text-primary"></i>
        <p class="text-[11px] leading-relaxed text-onyx">Detail nomor rekening atau kode QRIS akan ditampilkan di halaman konfirmasi setelah checkout.</p>
    </div>
</section>
