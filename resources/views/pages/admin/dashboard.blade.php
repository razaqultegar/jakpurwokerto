@extends('layouts.admin')

@section('heading', 'Dashboard')

@push('styles')
@vite([
    'resources/assets/plugins/datatables/datatables.css',
    'resources/assets/plugins/sweetalert2/sweetalert2.css',
    'resources/assets/plugins/select2/select2.css',
    'resources/assets/plugins/flatpickr/flatpickr.css',
])
@endpush

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Selamat datang, {{ auth()->user()->name }}.</h2>
        <p class="text-sm text-gray-600">Ringkasan aktivitas {{ config('app.name') }}.</p>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" data-stats-root>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Total Pesanan</p>
            <p class="mt-1.5 text-xl font-black text-foreground" data-stat="total">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Menunggu Pembayaran</p>
            <p class="mt-1.5 text-xl font-black text-amber-600" data-stat="pending">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="rounded-xl border border-mercury bg-white p-4">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Pembayaran Diterima</p>
            <p class="mt-1.5 text-xl font-black text-emerald-600" data-stat="verified">{{ number_format($stats['verified']) }}</p>
        </div>
        <div class="col-span-2 rounded-xl bg-linear-to-br from-primary via-primary-light to-primary-lighter p-4 text-white sm:col-span-3 lg:col-span-1">
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
            data-export-url="{{ route('admin.orders.export') }}"
            data-detail-url="{{ url('admin/orders/__ORDER__') }}"
            data-status-url="{{ url('admin/orders/__ORDER__/status') }}">
            <div class="orders-filters grid grid-cols-1 gap-3 border-b border-mercury bg-skull/40 p-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-onyx">Tipe Pembayaran</label>
                    <select class="orders-filter w-full" data-filter="payment_type">
                        <option value="">Semua Tipe</option>
                        <option value="dp">DP (50%)</option>
                        <option value="full">Bayar Lunas</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-onyx">Status Pesanan</label>
                    <select class="orders-filter w-full" data-filter="status">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Pembayaran</option>
                        <option value="verified">Pembayaran Diterima</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Batal</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-onyx">Rentang Tanggal</label>
                    <div class="jpw-flatpickr-wrap">
                        <input type="text" class="jpw-flatpickr" data-filter="date_range" placeholder="Pilih rentang tanggal…" autocomplete="off" />
                        <button type="button" class="jpw-flatpickr-clear" data-filter-date-clear aria-label="Hapus tanggal" hidden>
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" data-filter-reset
                        class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg border border-mercury bg-white px-4 text-[13px] font-semibold text-foreground transition hover:bg-skull">
                        <i class="ri-refresh-line"></i>
                        Reset Filter
                    </button>
                    <button type="button" data-export-orders
                        class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-[13px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                        <i class="ri-file-excel-2-line"></i>
                        Ekspor
                    </button>
                </div>
            </div>
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

    <div id="order-detail-modal" class="order-modal" hidden aria-hidden="true">
        <div class="order-modal__backdrop" data-modal-close></div>
        <div class="order-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="order-detail-modal-title">
            <button type="button" class="order-modal__close" data-modal-close aria-label="Tutup">
                <i class="ri-close-line"></i>
            </button>
            <h2 id="order-detail-modal-title" class="sr-only">Detail Pesanan</h2>
            <div class="order-modal__body" data-modal-content></div>
        </div>
    </div>
@endsection

@push('scripts')
@vite([
    'resources/assets/plugins/sweetalert2/sweetalert2.js',
    'resources/assets/plugins/datatables/datatables.js',
    'resources/assets/plugins/select2/select2.js',
    'resources/assets/plugins/flatpickr/flatpickr.js',
    'resources/assets/js/pages/admin-dashboard.js',
])
@endpush
