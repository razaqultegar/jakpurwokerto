@extends('layouts.app')

@section('content')
    @include('pages.checkout._partials.header')
    <form action="{{ route('checkout.store') }}" method="post" class="pb-6" novalidate>
        @csrf
        @include('pages.checkout._partials.summary')
        <hr class="section-divider">
        @include('pages.checkout._partials.form')
        <hr class="section-divider">
        @include('pages.checkout._partials.shipping-method')
        <hr class="section-divider">
        @include('pages.checkout._partials.payment-type')
        <hr class="section-divider">
        @include('pages.checkout._partials.payment-method')
        @include('pages.checkout._partials.cta')
    </form>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkout.js')
@endpush
