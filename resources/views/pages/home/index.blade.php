@extends('layouts.app')

@section('content')
    <section class="relative isolate overflow-hidden p-4">
        <div class="absolute inset-x-0 top-0 z-0 h-104 bg-cover bg-center bg-no-repeat" style="background-image: url('https://multimedia.beritajakarta.id/photo/2014_508c75c8507a2ae5223dfd2faeb98122/1efd997748edf3cdb90dd8dae037b5c9.jpg')"></div>
        <div class="absolute inset-x-0 top-0 z-0 h-104 bg-linear-to-b from-primary/90 via-primary/75 to-primary/40"></div>
        @include('pages.home._partials.match')
        <svg class="absolute inset-x-0 top-88 z-0 h-20 w-full text-white" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor"><path d="M0,60 C200,120 400,0 720,60 C1040,120 1240,0 1440,60 L1440,120 L0,120 Z"/></svg>
    </section>
    <section class="mb-6 px-4">
        <h3 class="mb-6 text-base font-semibold">Mau ikut kegiatan apa hari ini?</h3>
        <div class="grid grid-cols-4 gap-y-6">
            @include('pages.home._partials.menu')
        </div>
    </section>
    <hr class="section-divider">
    <section class="mb-6 p-4">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-base font-bold text-foreground">Tiket</h3>
            <a href="{{ route('ticket.show', 'the-7ourney') }}" class="text-[11px] font-semibold text-primary">Lihat detail</a>
        </div>
        <div class="overflow-hidden rounded-[24px] border border-mercury bg-gradient-to-br from-primary/10 via-white to-purple-50 p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-primary">THE 7OURNEY</p>
                    <h4 class="mt-1 text-base font-black text-foreground">Beli tiket untuk merayakan perjalanan 7 tahun bersama.</h4>
                    <p class="mt-2 text-[12px] leading-relaxed text-onyx">Nikmati pengalaman acara penuh hiburan, momen kebersamaan, dan akses spesial yang disusun untuk keluarga besar The Jakmania Biro Purwokerto.</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/15 text-primary">
                    <i class="ri-ticket-2-line text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('ticket.show', 'the-7ourney') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-[11px] font-semibold text-white">
                <i class="ri-shopping-bag-line"></i>
                Beli Tiket
            </a>
        </div>
    </section>
    <hr class="section-divider">
    <section class="mb-6 p-4">
        <h3 class="text-base font-bold text-foreground mb-3">Merchandise</h3>
        @include('pages.home._partials.merchandise')
    </section>
    <hr class="section-divider">
    <section class="mb-6 p-4">
        <h3 class="text-base font-bold text-foreground mb-3">Berita</h3>
        @include('pages.home._partials.berita')
    </section>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/home.js')
@endpush
