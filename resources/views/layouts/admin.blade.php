<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title }} | {{ config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('build/medias/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('build/medias/logo.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap">
        @vite('resources/assets/css/app.css')
    </head>
    <body class="min-h-screen bg-gray-100 text-gray-900">
        @auth
        <div class="flex min-h-screen">
            <aside class="hidden w-64 shrink-0 flex-col bg-gray-900 text-gray-100 md:flex">
                <div class="px-6 py-5 border-b border-gray-800">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">{{ config('app.name') }}</a>
                    <p class="text-xs text-gray-400">Panel Admin</p>
                </div>
                <nav class="flex-1 space-y-1 px-3 py-4 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-md px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="ri-dashboard-line"></i> Dashboard
                    </a>
                </nav>
                <div class="border-t border-gray-800 px-3 py-3">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="ri-logout-box-r-line"></i> Keluar
                        </button>
                    </form>
                </div>
            </aside>
            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
                    <h1 class="text-base font-semibold text-gray-900">@yield('heading', $title ?? '')</h1>
                    <div class="flex items-center gap-3 text-sm text-gray-700">
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="post" class="md:hidden">
                            @csrf
                            <button type="submit" class="rounded-md border border-gray-300 px-3 py-1 text-xs hover:bg-gray-50">Keluar</button>
                        </form>
                    </div>
                </header>
                <main class="flex-1 overflow-x-auto px-6 py-6">
                    @yield('content')
                </main>
            </div>
        </div>
        @else
        <main class="flex min-h-screen items-center justify-center px-4 py-12">
            @yield('content')
        </main>
        @endauth
        @vite('resources/assets/js/app.js')
        @stack('scripts')
    </body>
</html>
