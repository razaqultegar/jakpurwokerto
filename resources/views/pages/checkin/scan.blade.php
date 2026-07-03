@extends('layouts.checkin-scan')

@push('styles')
<style>
    #qr-video { object-fit: cover; }
</style>
@endpush

@section('content')
<div class="flex flex-1 flex-col bg-background" data-checkin-scan data-lookup-url="{{ route('checkin.lookup') }}" data-confirm-url="{{ url('checkin/__CODE__/confirm') }}" data-undo-url="{{ url('checkin/__CODE__/undo') }}" data-logout-url="{{ route('checkin.logout') }}">
    <header class="flex shrink-0 items-center justify-between border-b border-mercury bg-white px-4 py-4">
        <div>
            <h1 class="text-sm font-black text-foreground">Scan Tiket Venue</h1>
            <p class="text-[11px] text-onyx">Arahkan kamera ke QR code e-tiket</p>
        </div>
        <form action="{{ route('checkin.logout') }}" method="post" onsubmit="return confirm('Keluar dari mode scan?');">
            @csrf
            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full border border-mercury text-onyx hover:bg-skull">
                <i class="ri-logout-box-r-line"></i>
            </button>
        </form>
    </header>
    <div class="relative mx-4 mt-4 overflow-hidden rounded-2xl bg-black shadow-sm" style="aspect-ratio:1/1;">
        <video id="qr-video" class="h-full w-full" playsinline muted></video>
        <div class="pointer-events-none absolute inset-6 rounded-xl border-2 border-primary/80"></div>
        <p data-camera-error hidden class="absolute inset-0 flex items-center justify-center bg-black/80 p-6 text-center text-[12px] text-white/90"></p>
    </div>
    <form data-manual-form class="mx-4 mt-4 flex gap-2">
        <input type="text" name="code" autocomplete="off" placeholder="Atau ketik kode tiket" class="h-11 flex-1 rounded-xl border border-mercury bg-white px-3 text-center text-[13px] font-bold uppercase tracking-widest text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary/20" />
        <button type="submit" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-softer text-primary hover:bg-primary-soft">
            <i class="ri-search-line"></i>
        </button>
    </form>
    <div data-result-empty class="mx-4 mt-4 flex-1 rounded-2xl border border-dashed border-mercury p-6 text-center text-[12px] text-onyx">Belum ada tiket yang di-scan.</div>
    <div data-result-card hidden class="mx-4 mt-4 mb-6 overflow-hidden rounded-2xl border border-mercury bg-white shadow-sm">
        <div class="p-4">
            <p data-result-code class="font-mono text-[11px] font-bold text-onyx"></p>
            <h2 data-result-name class="mt-0.5 text-[15px] font-black leading-tight text-foreground"></h2>
            <p data-result-items class="mt-0.5 text-[12px] text-onyx"></p>
            <div data-result-badge class="mt-3 flex items-center gap-2 rounded-xl px-3 py-2 text-[12px] font-bold">
                <i data-result-icon class="text-base"></i>
                <span data-result-message></span>
            </div>
            <button type="button" data-action-confirm class="mt-3 inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-primary text-[13px] font-bold text-white shadow-sm transition active:scale-95 hover:bg-primary-light">
                <i class="ri-checkbox-circle-line"></i>
                Konfirmasi Check-in
            </button>
            <button type="button" data-action-undo class="mt-3 inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-lg border border-mercury bg-white text-[12px] font-semibold text-foreground transition hover:bg-skull">
                <i class="ri-arrow-go-back-line"></i>
                Batalkan Check-in
            </button>
            <button type="button" data-action-rescan class="mt-2 inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-lg text-[12px] font-semibold text-onyx hover:bg-skull">
                <i class="ri-qr-scan-2-line"></i>
                Scan Tiket Berikutnya
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkin-scan.js')
@endpush
