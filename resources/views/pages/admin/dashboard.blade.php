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

    @if (!empty($stockCards))
        <div class="mt-3 flex flex-col gap-3">
            @foreach ($stockCards as $card)
                @php
                    $tone = $card['remaining'] === null
                        ? ['bar' => 'bg-mercury', 'text' => 'text-foreground']
                        : ($card['remaining'] <= 0
                            ? ['bar' => 'bg-red-500', 'text' => 'text-red-600']
                            : ($card['limit'] > 0 && $card['remaining'] < ($card['limit'] * 0.2)
                                ? ['bar' => 'bg-amber-500', 'text' => 'text-amber-600']
                                : ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-600']));
                @endphp
                <div class="flex flex-wrap items-center gap-4 rounded-xl border border-mercury bg-white p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                            <i class="ri-shirt-line text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-onyx">Stok Terjual</p>
                            <p class="truncate text-[13px] font-bold text-foreground">{{ $card['name'] }}</p>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        @if ($card['limit'] > 0)
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[13px] font-black {{ $tone['text'] }}">
                                    {{ number_format($card['sold']) }}
                                    <span class="text-[12px] font-bold text-onyx">/ {{ number_format($card['limit']) }}</span>
                                </p>
                                <p class="text-[11px] text-onyx">
                                    Sisa <span class="font-bold {{ $tone['text'] }}">{{ number_format($card['remaining']) }}</span> · {{ $card['progress'] }}%
                                </p>
                            </div>
                            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-skull">
                                <div class="h-full {{ $tone['bar'] }} transition-all" style="width: {{ $card['progress'] }}%"></div>
                            </div>
                        @else
                            <p class="text-[13px] font-black {{ $tone['text'] }}">{{ number_format($card['sold']) }}</p>
                            <p class="mt-0.5 text-[11px] text-onyx">Stok tidak diatur</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

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
            data-status-url="{{ url('admin/orders/__ORDER__/status') }}"
            data-sync-payment-url="{{ url('admin/orders/__ORDER__/sync-payment') }}"
            data-settlement-verify-url="{{ url('admin/orders/__ORDER__/settlement-verify') }}"
            data-delete-url="{{ url('admin/orders/__ORDER__') }}">
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
                        <option value="cancelled">Dibatalkan</option>
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
                            <th class="whitespace-nowrap">ID Pesanan</th>
                            <th class="whitespace-nowrap">Tanggal</th>
                            <th class="whitespace-nowrap">Pelanggan</th>
                            <th class="whitespace-nowrap text-center">Item</th>
                            <th class="whitespace-nowrap text-right">Total</th>
                            <th class="whitespace-nowrap">Pembayaran</th>
                            <th class="whitespace-nowrap">Status</th>
                            <th class="whitespace-nowrap text-right">Aksi</th>
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

    <div id="sync-payment-modal" class="order-modal" hidden aria-hidden="true">
        <div class="order-modal__backdrop" data-sync-close></div>
        <div class="order-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="sync-payment-modal-title">
            <button type="button" class="order-modal__close" data-sync-close aria-label="Tutup">
                <i class="ri-close-line"></i>
            </button>
            <div class="order-modal__body">
                <div class="detail-hero">
                    <div class="detail-hero__bg"></div>
                    <div class="relative flex flex-col gap-2 pr-12">
                        <span class="detail-chip detail-chip--glass w-fit font-mono"><i class="ri-refresh-line"></i> <span data-sync-order-id>—</span></span>
                        <h2 id="sync-payment-modal-title" class="text-base font-black leading-tight text-white">Sinkronisasi Pembayaran</h2>
                        <p class="text-[12px] leading-relaxed text-white/85">Sesuaikan total pembayaran sesuai nominal yang sudah diterima dari pembeli.</p>
                    </div>
                </div>

                <form data-sync-form class="flex flex-col gap-4 px-5 py-5">
                    <div class="rounded-2xl border border-mercury bg-skull/40 p-4">
                        <div class="grid grid-cols-3 divide-x divide-mercury">
                            <div class="flex flex-col items-start pr-3">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-onyx">Subtotal</span>
                                <span class="mt-1 text-[14px] font-black leading-tight text-foreground" data-sync-subtotal>Rp0</span>
                            </div>
                            <div class="flex flex-col items-start px-3">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-onyx">Saat Ini</span>
                                <span class="mt-1 text-[14px] font-black leading-tight text-foreground" data-sync-current>Rp0</span>
                            </div>
                            <div class="flex flex-col items-start pl-3">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Sisa Setelahnya</span>
                                <span class="mt-1 text-[14px] font-black leading-tight text-amber-800" data-sync-remaining>Rp0</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="relative h-2 w-full overflow-hidden rounded-full bg-white ring-1 ring-mercury">
                                <div class="absolute inset-y-0 left-0 bg-linear-to-r from-primary to-primary-light transition-[width] duration-200" style="width: 0%" data-sync-progress></div>
                            </div>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] font-bold text-onyx">
                                <span><span data-sync-percent>0</span>% dari subtotal</span>
                                <span data-sync-percent-label class="text-onyx">Belum dibayar</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-end justify-between">
                            <label for="sync-amount-input" class="text-[11px] font-bold uppercase tracking-wider text-onyx">Total Pembayaran Baru</label>
                            <span class="text-[10px] font-semibold text-onyx">Maks. <span data-sync-max>Rp0</span></span>
                        </div>
                        <div class="group relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center border-r border-mercury text-[13px] font-bold text-onyx">Rp</span>
                            <input id="sync-amount-input" type="text" inputmode="numeric" autocomplete="off" data-sync-input
                                class="h-12 w-full rounded-xl border border-mercury bg-white pl-14 pr-4 text-[16px] font-black tabular-nums text-foreground outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
                        </div>
                        <p class="mt-1.5 hidden text-[11px] font-semibold text-red-600" data-sync-error></p>
                    </div>

                    <div>
                        <div class="mb-1.5 text-[11px] font-bold uppercase tracking-wider text-onyx">Isi Cepat</div>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" data-sync-preset="50" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-mercury bg-white px-2 text-[11px] font-bold text-foreground transition hover:border-primary hover:bg-primary-softer hover:text-primary"><i class="ri-percent-line text-[12px]"></i> 50% DP</button>
                            <button type="button" data-sync-preset="75" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-mercury bg-white px-2 text-[11px] font-bold text-foreground transition hover:border-primary hover:bg-primary-softer hover:text-primary"><i class="ri-percent-line text-[12px]"></i> 75%</button>
                            <button type="button" data-sync-preset="100" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 text-[11px] font-bold text-emerald-700 transition hover:bg-emerald-100"><i class="ri-checkbox-circle-line text-[12px]"></i> Lunas</button>
                        </div>
                    </div>

                    <div class="-mx-5 -mb-5 mt-1 flex items-center justify-end gap-2 border-t border-mercury bg-skull/40 px-5 py-3">
                        <button type="button" data-sync-close class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg border border-mercury bg-white px-4 text-[13px] font-semibold text-foreground transition hover:bg-skull">Batal</button>
                        <button type="submit" data-sync-submit class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg bg-primary px-4 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-light disabled:cursor-not-allowed disabled:opacity-60">
                            <i class="ri-save-3-line"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
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
