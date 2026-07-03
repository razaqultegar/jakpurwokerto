@extends('layouts.admin')

@section('heading', $title ?? 'Pesanan Merchandise')

@push('styles')
    @vite([
        'resources/assets/plugins/datatables/datatables.css',
        'resources/assets/plugins/sweetalert2/sweetalert2.css',
        'resources/assets/plugins/select2/select2.css',
        'resources/assets/plugins/flatpickr/flatpickr.css',
    ])
@endpush

@section('content')
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
