<section class="section">
    <div class="section-header">
        <span class="section-bar"></span>
        <h2 class="section-title">Metode Pembayaran</h2>
    </div>

    <div class="space-y-3" data-method-group>
        <div>
            <div class="mb-1.5 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-onyx">
                <i class="ri-bank-line text-xs"></i>
                Bank Transfer
            </div>
            <div class="space-y-2">
                @foreach ($checkout['banks'] as $i => $bank)
                <label class="option-card-row">
                    <input type="radio" class="peer sr-only" name="payment_method" value="bank:{{ $bank['key'] }}" {{ $i === 0 ? 'checked' : '' }}>
                    <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg {{ $bank['color'] }} text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
                        {{ $bank['logo_text'] }}
                    </span>
                    <span class="flex-1">
                        <span class="block text-xs font-bold text-foreground">{{ $bank['name'] }}</span>
                        <span class="mt-0.5 block text-[10px] text-onyx">Transfer ke rekening</span>
                    </span>
                    <span class="option-check-inline">
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
                <label class="option-card-row">
                    <input type="radio" class="peer sr-only" name="payment_method" value="qris:{{ $checkout['qris']['key'] }}">
                    <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg bg-foreground text-[10px] font-black uppercase tracking-wider text-white shadow-sm">QRIS</span>
                    <span class="flex-1">
                        <span class="block text-xs font-bold text-foreground">QRIS</span>
                        <span class="mt-0.5 block text-[10px] text-onyx">Scan via DANA, GoPay, OVO, ShopeePay, mobile banking</span>
                    </span>
                    <span class="option-check-inline">
                        <i class="ri-check-line text-xs"></i>
                    </span>
                </label>
            </div>
        </div>
    </div>
</section>
