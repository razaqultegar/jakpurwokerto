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
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="mb-6 p-4">
        <h3 class="text-base font-bold text-foreground mb-3">Merchandise</h3>
        @include('pages.home._partials.merchandise')
    </section>
    <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
    <section class="mb-6 p-4">
        <h3 class="text-base font-bold text-foreground mb-3">Berita</h3>
        @include('pages.home._partials.berita')
    </section>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/home.js')
@endpush
