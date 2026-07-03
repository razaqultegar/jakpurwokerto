@extends('layouts.admin')

@section('heading', $title ?? 'Check-in Tiket')

@php
    $flashStatus = session('checkin_status');
    $flashMessage = session('checkin_message');
    $flashStyles = [
        'success' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'ri-checkbox-circle-fill'],
        'already' => ['bg-amber-50 border-amber-200 text-amber-800', 'ri-information-fill'],
        'undone' => ['bg-sky-50 border-sky-200 text-sky-800', 'ri-arrow-go-back-line'],
        'error' => ['bg-red-50 border-red-200 text-red-800', 'ri-error-warning-fill'],
    ];
    [$flashClass, $flashIcon] = $flashStyles[$flashStatus] ?? ['bg-gray-50 border-gray-200 text-gray-800', 'ri-information-line'];
@endphp

@section('content')
    <div class="mx-auto max-w-md">
        <a href="{{ route('admin.checkin.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-[12px] font-semibold text-onyx hover:text-primary">
            <i class="ri-arrow-left-line"></i> Cari kode lain
        </a>

        @if ($flashMessage)
        <div class="mb-4 flex items-center gap-2 rounded-xl border {{ $flashClass }} px-4 py-3 text-[12px] font-semibold">
            <i class="{{ $flashIcon }} text-base"></i>
            {{ $flashMessage }}
        </div>
        @endif

        @if (! $order)
        <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
            <i class="ri-close-circle-line text-4xl text-red-500"></i>
            <h2 class="mt-3 text-sm font-bold text-red-800">Kode Tiket Tidak Ditemukan</h2>
            <p class="mt-1 text-[12px] text-red-700">Kode <span class="font-mono font-bold">{{ $code }}</span> tidak terdaftar sebagai e-tiket yang valid.</p>
        </div>
        @else
            @php
                $itemNames = collect($order->item ?? [])->pluck('name')->filter()->implode(', ');
                $itemQty = collect($order->item ?? [])->sum('qty');
                $isCancelled = $order->status === 'cancelled';
                $isCheckedIn = (bool) $order->checked_in_at;
            @endphp
            <div class="overflow-hidden rounded-2xl border border-mercury bg-white shadow-sm">
                <div class="detail-hero">
                    <div class="detail-hero__bg"></div>
                    <div class="relative flex flex-col gap-2">
                        <span class="detail-chip detail-chip--glass w-fit font-mono">{{ $order->order_id }}</span>
                        <h2 class="text-base font-black leading-tight text-white">{{ $order->customer_name }}</h2>
                        <p class="text-[12px] text-white/85">{{ $itemNames }} &middot; {{ $itemQty }} tiket</p>
                    </div>
                </div>

                <div class="p-5">
                    @if ($isCancelled)
                        <div class="flex items-center gap-2 rounded-xl bg-red-50 px-4 py-3 text-[12px] font-bold text-red-700 ring-1 ring-red-200">
                            <i class="ri-close-circle-fill text-base"></i>
                            Pesanan dibatalkan &mdash; tiket tidak berlaku.
                        </div>
                    @elseif ($isCheckedIn)
                        <div class="rounded-xl bg-amber-50 px-4 py-3 ring-1 ring-amber-200">
                            <div class="flex items-center gap-2 text-[12px] font-bold text-amber-800">
                                <i class="ri-checkbox-circle-fill text-base"></i>
                                Sudah check-in
                            </div>
                            <p class="mt-1 text-[11px] text-amber-700">{{ $order->checked_in_at->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
                        </div>
                        <form action="{{ route('admin.checkin.undo', ['code' => $code]) }}" method="post" class="mt-3" onsubmit="return confirm('Batalkan status check-in tiket ini?');">
                            @csrf
                            <button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-1.5 rounded-lg border border-mercury bg-white text-[12px] font-semibold text-foreground transition hover:bg-skull">
                                <i class="ri-arrow-go-back-line"></i>
                                Batalkan Check-in
                            </button>
                        </form>
                    @else
                        <div class="flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-3 text-[12px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                            <i class="ri-shield-check-fill text-base"></i>
                            Tiket valid &mdash; belum check-in.
                        </div>
                        <form action="{{ route('admin.checkin.confirm', ['code' => $code]) }}" method="post" class="mt-3">
                            @csrf
                            <button type="submit" class="inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-primary text-[13px] font-bold text-white shadow-sm transition active:scale-95 hover:bg-primary-light">
                                <i class="ri-checkbox-circle-line"></i>
                                Konfirmasi Check-in
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
