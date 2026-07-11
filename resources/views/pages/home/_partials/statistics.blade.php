@if (!empty($memberStats) && $memberStats['total'] > 0)
<section class="mb-6 p-4">
    <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="section-bar"></span>
            <h3 class="text-base font-bold text-foreground">Statistik Keanggotaan</h3>
        </div>
        <a href="{{ route('home.stats') }}" class="text-[11px] font-semibold text-primary">Lihat detail</a>
    </div>

    <div id="member-stats-section" class="space-y-4">
        <!-- Summary cards -->
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-gradient-to-br from-primary/10 via-primary-softer to-white p-4 ring-1 ring-primary-soft">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-primary">Total Anggota</p>
                <p class="mt-1 text-2xl font-black text-primary">{{ number_format($memberStats['total']) }}</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-white to-white p-4 ring-1 ring-emerald-200">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600">Aktif</p>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($memberStats['status']['Aktif'] ?? 0) }}</p>
            </div>
        </div>

        <!-- Gender chart -->
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury shadow-sm">
            <p class="text-xs font-bold text-foreground">Berdasarkan Jenis Kelamin</p>
            <div id="chart-gender" class="mt-2" data-values="{{ json_encode($memberStats['gender']) }}"></div>
        </div>

        <!-- Status chart -->
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury shadow-sm">
            <p class="text-xs font-bold text-foreground">Berdasarkan Status</p>
            <div id="chart-status" class="mt-2" data-values="{{ json_encode($memberStats['status']) }}"></div>
        </div>

        <!-- Monthly chart -->
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury shadow-sm">
            <p class="text-xs font-bold text-foreground">Pendaftaran 6 Bulan Terakhir</p>
            <div id="chart-monthly" class="mt-2" data-values="{{ json_encode($memberStats['monthly']) }}"></div>
        </div>

        <!-- Sector chart -->
        @if (!empty($memberStats['sector']))
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury shadow-sm">
            <p class="text-xs font-bold text-foreground">Berdasarkan Sektor</p>
            <div id="chart-sector" class="mt-2" data-values="{{ json_encode($memberStats['sector']) }}"></div>
        </div>
        @endif
    </div>
</section>
@endif
