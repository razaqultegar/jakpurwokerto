<div class="mb-3 flex items-end justify-between">
    <div>
        <span class="inline-flex items-center gap-1 rounded-full bg-primary-tint px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary">
            <i class="ri-flashlight-fill"></i>
            Open Pre-Order
        </span>
        <h3 class="mt-1 text-base font-bold text-foreground">Jersey #7 Tahun JakPurwokerto</h3>
    </div>
    <a href="{{ route('merchandise.show', 'the-7ourney') }}" class="text-xs font-medium text-primary">Lihat Detail</a>
</div>
<div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-primary via-primary-light to-primary-lighter p-4 text-white shadow-lg">
    <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-12 -left-8 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
    <div class="pointer-events-none absolute right-4 top-2 text-[110px] font-black leading-none text-white/10">7</div>
    <div class="relative flex gap-4">
        <a href="{{ route('merchandise.show', 'the-7ourney') }}" class="relative h-32 w-28 shrink-0 overflow-hidden rounded-xl bg-white/15 ring-1 ring-white/20 backdrop-blur-sm">
            <img src="{{ asset('build/medias/the-7ourney/artboard1.jpg') }}" alt="Jersey the 7ourney" class="h-full w-full object-cover" loading="lazy">
            <span class="pointer-events-none absolute inset-x-0 bottom-0 h-10 bg-linear-to-t from-black/50 to-transparent"></span>
            <span class="absolute bottom-1 left-1 rounded-full bg-white/85 px-1.5 py-0.5 text-[9px] font-bold text-primary backdrop-blur-sm">#7</span>
        </a>
        <div class="flex flex-1 flex-col">
            <span class="text-[10px] font-medium uppercase tracking-wider text-white/80">Edisi Spesial</span>
            <h4 class="text-sm font-bold leading-tight">the 7ourney</h4>
            <p class="mt-1 text-[11px] text-white/85">Bersama jadi keluarga, satu jiwa untuk Macan Kemayoran.</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-lg font-bold">+Rp135.000</span>
                <span class="text-[11px] text-white/70 line-through">Rp185.000</span>
            </div>
            <span class="mt-1 w-fit rounded bg-yellow-300 px-1.5 py-0.5 text-[9px] font-bold text-primary">HEMAT 22%</span>
        </div>
    </div>
    <div class="relative mt-3" data-countdown data-start="2026-05-20T00:00:00+07:00" data-end="2026-06-20T23:59:59+07:00">
        <div class="mb-1.5 flex items-center justify-between text-[10px] font-medium text-white/85">
            <span data-countdown-label>Berakhir dalam</span>
            <span class="rounded bg-white/20 px-1.5 py-0.5 text-[9px] uppercase tracking-wide" data-countdown-status>Berlangsung</span>
        </div>
        <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
            <div class="rounded-lg bg-white/15 py-2 ring-1 ring-white/20">
                <div class="text-base font-bold leading-none" data-countdown-days>00</div>
                <div class="mt-1 text-[10px] text-white/80">Hari</div>
            </div>
            <div class="rounded-lg bg-white/15 py-2 ring-1 ring-white/20">
                <div class="text-base font-bold leading-none" data-countdown-hours>00</div>
                <div class="mt-1 text-[10px] text-white/80">Jam</div>
            </div>
            <div class="rounded-lg bg-white/15 py-2 ring-1 ring-white/20">
                <div class="text-base font-bold leading-none" data-countdown-minutes>00</div>
                <div class="mt-1 text-[10px] text-white/80">Menit</div>
            </div>
            <div class="rounded-lg bg-white/15 py-2 ring-1 ring-white/20">
                <div class="text-base font-bold leading-none" data-countdown-seconds>00</div>
                <div class="mt-1 text-[10px] text-white/80">Detik</div>
            </div>
        </div>
    </div>
    <a href="{{ route('merchandise.show', 'the-7ourney') }}" class="relative mt-4 flex items-center justify-center gap-2 rounded-full bg-white py-3 text-sm font-bold text-primary shadow-md">
        Selengkapnya...
        <i class="ri-arrow-right-line"></i>
    </a>
</div>
