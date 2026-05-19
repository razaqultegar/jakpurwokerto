@extends('layouts.admin', ['title' => 'Login Admin'])

@section('content')
    <div class="w-full max-w-md rounded-lg bg-white p-8 shadow-sm">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900">Login Admin</h1>
            <p class="mt-1 text-sm text-gray-600">Masuk untuk mengelola {{ config('app.name') }}.</p>
        </div>
        @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('login') }}" method="post" class="space-y-4" novalidate>
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">Kata Sandi</label>
                <input type="password" id="password" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900" name="password" required autocomplete="current-password">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="remember" class="rounded border-gray-300">
                Ingat saya
            </label>
            <button type="submit" class="w-full rounded-md bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800">Masuk</button>
        </form>
    </div>
@endsection
