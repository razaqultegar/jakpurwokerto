@extends('layouts.app')

@section('content')
    @include('pages.checkout._partials.header')
    <form action="" method="post" class="pb-6" novalidate>
        @csrf
        @include('pages.checkout._partials.summary')
        <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
        @include('pages.checkout._partials.form')
        <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
        @include('pages.checkout._partials.payment-type')
        <hr class="m-0 h-2 w-full border-0 bg-skull p-0">
        @include('pages.checkout._partials.payment-method')
        @include('pages.checkout._partials.cta')
    </form>
@endsection

@push('scripts')
    @vite('resources/assets/js/pages/checkout.js')
@endpush
