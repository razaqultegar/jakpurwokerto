@extends('layouts.admin')

@section('heading', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Selamat datang, {{ auth()->user()->name }}.</h2>
        <p class="text-sm text-gray-600">Ringkasan aktivitas {{ config('app.name') }}.</p>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Pesanan</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">—</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Menunggu Pembayaran</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">—</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Dikirim</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">—</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Selesai</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">—</p>
        </div>
    </div>
    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 text-sm text-gray-600">Konten dashboard admin akan ditampilkan di sini.</div>
@endsection
