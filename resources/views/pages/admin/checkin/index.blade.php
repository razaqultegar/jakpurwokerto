@extends('layouts.admin')

@section('heading', $title ?? 'Check-in Tiket')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-xl border border-mercury bg-white p-6 text-center shadow-sm">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-softer text-primary">
                <i class="ri-qr-scan-2-line text-3xl"></i>
            </div>
            <h2 class="mt-4 text-base font-bold text-foreground">Check-in Tiket Venue</h2>
            <p class="mt-1 text-xs text-onyx">Scan QR code pada e-tiket pengunjung menggunakan kamera HP, atau masukkan kode tiket secara manual di bawah ini.</p>

            <form action="{{ route('admin.checkin.lookup') }}" method="post" class="mt-5 flex flex-col gap-3">
                @csrf
                <input type="text" name="code" required autofocus autocomplete="off"
                    placeholder="Cth. A1B2C3D4E5"
                    class="h-12 w-full rounded-xl border border-mercury bg-white px-4 text-center text-[15px] font-black uppercase tracking-widest text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                @error('code')
                    <p class="text-[11px] font-semibold text-red-600">{{ $message }}</p>
                @enderror
                <button type="submit"
                    class="inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-primary text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-light">
                    <i class="ri-search-line"></i>
                    Cari Tiket
                </button>
            </form>
        </div>

        <p class="mt-4 text-center text-[11px] text-onyx">
            Kode tiket didapat dari QR code yang tercetak di invoice PDF pesanan tiket yang sudah dikonfirmasi admin.
        </p>
    </div>
@endsection
