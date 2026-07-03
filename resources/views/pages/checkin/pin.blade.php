@extends('layouts.checkin-scan')

@section('content')
    <main class="relative flex flex-1 flex-col items-center justify-center overflow-hidden px-6 py-12">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,var(--color-primary-soft)_0%,transparent_45%),radial-gradient(circle_at_bottom_right,var(--color-primary-softer)_0%,transparent_50%)]"></div>
        <div class="relative z-10 w-full rounded-2xl border border-mercury bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-softer text-primary">
                <i class="ri-shield-keyhole-line text-3xl"></i>
            </div>
            <h1 class="mt-4 text-base font-black text-foreground">Akses Petugas Check-in</h1>
            <p class="mt-1 text-xs text-onyx">Masukkan PIN petugas untuk membuka halaman scan tiket venue.</p>
            <form action="{{ route('checkin.pin.verify') }}" method="post" class="mt-5 flex flex-col gap-3">
                @csrf
                <input type="tel" inputmode="numeric" name="pin" required autofocus autocomplete="off" placeholder="PIN" class="h-14 w-full rounded-xl border border-mercury bg-white px-4 text-center text-2xl font-black tracking-[0.4em] text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                @error('pin')
                <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="inline-flex h-12 w-full items-center justify-center gap-1.5 rounded-lg bg-primary text-[14px] font-bold text-white shadow-sm transition active:scale-95 hover:bg-primary-light">
                    <i class="ri-login-box-line"></i>
                    Masuk
                </button>
            </form>
        </div>
        <p class="relative z-10 mt-4 text-center text-[11px] text-onyx">PIN diberikan oleh koordinator acara. Perangkat ini akan diingat selama 30 hari setelah verifikasi.</p>
    </main>
@endsection
