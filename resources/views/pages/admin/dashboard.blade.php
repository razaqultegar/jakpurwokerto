@extends('layouts.admin')

@section('heading', 'Dashboard')

@push('styles')
@vite(['resources/assets/plugins/datatables/datatables.css', 'resources/assets/plugins/sweetalert2/sweetalert2.css'])
@endpush

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Selamat datang, {{ auth()->user()->name }}.</h2>
        <p class="text-sm text-gray-600">Ringkasan aktivitas {{ config('app.name') }}.</p>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4" data-stats-root>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Total Pesanan</p>
            <p class="mt-1.5 text-xl font-black text-foreground" data-stat="total">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Menunggu</p>
            <p class="mt-1.5 text-xl font-black text-amber-600" data-stat="pending">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Diverifikasi</p>
            <p class="mt-1.5 text-xl font-black text-emerald-600" data-stat="verified">{{ number_format($stats['verified']) }}</p>
        </div>
        <div class="col-span-2 rounded-xl bg-linear-to-br from-primary via-primary-light to-primary-lighter p-4 text-white lg:col-span-1">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-white/80">Total Pendapatan</p>
            <p class="mt-1.5 text-xl font-black" data-stat="revenue">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-6">
        <div class="mb-3">
            <h3 class="text-base font-bold text-foreground">Daftar Pesanan Merchandise</h3>
            <p class="text-xs text-onyx">Kelola seluruh pesanan dari pelanggan.</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-mercury bg-white shadow-sm"
            data-orders-root
            data-data-url="{{ route('admin.orders.data') }}"
            data-detail-url="{{ url('admin/orders/__ORDER__') }}"
            data-status-url="{{ url('admin/orders/__ORDER__/status') }}"
            data-shipping-url="{{ url('admin/orders/__ORDER__/shipping') }}"
            data-dp-proof-url="{{ url('admin/orders/__ORDER__/dp-proof') }}">
            <div class="overflow-x-auto">
                <table id="orders-table" class="display w-full">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Pembayaran</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@vite(['resources/assets/plugins/sweetalert2/sweetalert2.js', 'resources/assets/plugins/datatables/datatables.js', 'resources/assets/js/pages/admin-dashboard.js'])
@endpush
