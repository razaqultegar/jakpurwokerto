@extends('layouts.app')

@section('content')
    @include('pages.checkout._partials.header')
    <form action="{{ route('checkout.store') }}" method="post" class="pb-6" novalidate>
        @csrf
        @include('pages.checkout._partials.summary')
        <hr class="section-divider">
        @include('pages.checkout._partials.form')
        <hr class="section-divider" data-shipping-divider>
        <div data-shipping-section>
            @include('pages.checkout._partials.shipping-method')
        </div>
        <hr class="section-divider" data-payment-type-divider>
        <div data-payment-type-section>
            @include('pages.checkout._partials.payment-type')
        </div>
        <hr class="section-divider">
        @include('pages.checkout._partials.payment-method')
        @include('pages.checkout._partials.cta')
    </form>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkout.js')
@endpush
