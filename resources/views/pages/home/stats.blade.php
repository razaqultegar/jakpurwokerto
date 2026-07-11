@extends('layouts.app')

@section('styles')
    @vite('resources/assets/css/app.css')
@endsection

@section('content')
<section class="relative isolate overflow-hidden p-4">
    <div class="absolute inset-x-0 top-0 z-0 h-104 bg-cover bg-center bg-no-repeat" style="background-image: url('https://multimedia.beritajakarta.id/photo/2014_508c75c8507a2ae5223dfd2faeb98122/1efd997748edf3cdb90dd8a0519bb3b2_1716106707858_1716106707858.jpeg');"></div>
    <div class="absolute inset-x-0 top-0 z-0 h-104 bg-linear-to-b from-primary/90 via-primary/80 to-background"></div>

    <div class="relative z-10">
        <div class="relative mb-5 flex items-center gap-3">
            <a href="{{ route('home') }}" class="icon-btn-glass">
                <i class="ri-arrow-left-s-line text-lg"></i>
            </a>
            <div>
                <h1 class="text-sm font-bold text-white">Statistik Keanggotaan</h1>
                <p class="text-[10px] text-white/80">Data keanggotaan Biro 01 Purwokerto</p>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<section class="mb-6 p-4">
    <div id="member-stats-section" class="space-y-4">
        <!-- Summary cards - 4 columns -->
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-gradient-to-br from-primary/10 via-primary-softer to-white p-4 ring-1 ring-primary-soft">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-primary">Total Anggota</p>
                <p class="mt-1 text-2xl font-black text-primary">{{ number_format($memberStats['total']) }}</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-emerald-50 via-white to-white p-4 ring-1 ring-emerald-200">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-emerald-600">Aktif</p>
                <p class="mt-1 text-2xl font-black text-emerald-600">{{ number_format($memberStats['status']['Aktif'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-red-50 via-white to-white p-4 ring-1 ring-red-200">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-red-600">Tidak Aktif</p>
                <p class="mt-1 text-2xl font-black text-red-600">{{ number_format($memberStats['status']['Non-Aktif'] ?? 0) }}</p>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-amber-50 via-white to-white p-4 ring-1 ring-amber-200">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-amber-600">Proses</p>
                <p class="mt-1 text-2xl font-black text-amber-600">{{ number_format($memberStats['status']['Proses'] ?? 0) }}</p>
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

        <!-- Age categories chart -->
        <div class="rounded-2xl bg-white p-4 ring-1 ring-mercury shadow-sm">
            <p class="text-xs font-bold text-foreground">Berdasarkan Kategori Usia</p>
            <div id="chart-age" class="mt-2" data-values="{{ json_encode($memberStats['ageCategories']) }}"></div>
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
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/home.js')
@endpush
