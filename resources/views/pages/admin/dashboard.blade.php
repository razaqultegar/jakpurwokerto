@extends('layouts.admin')

@section('heading', $title ?? 'Dashboard')

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
        <p class="text-sm text-gray-600">Pilih menu <span class="font-semibold">Pesanan Tiket</span> atau <span class="font-semibold">Pesanan Merchandise</span> di sisi kiri untuk mengelola pesanan.</p>
    </div>
    @include('pages.admin._partials.orders-manager')
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
