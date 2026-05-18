<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=0">
        <title>{{ $title }} | {{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap">
        @vite('resources/assets/css/app.css')
    </head>
    <body>
        <main class="mx-auto my-0 min-h-full max-w-screen-sm">
            <div class="mx-auto my-0 min-h-screen max-w-480 overflow-x-hidden bg-white pb-16.5">
                @yield('content')
                <x-footer />
            </div>
        </main>
        @vite('resources/assets/js/app.js')
        @stack('scripts')
    </body>
</html>
