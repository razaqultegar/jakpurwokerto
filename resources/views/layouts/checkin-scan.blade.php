<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f7f7f7">
        <title>{{ $title }} | {{ config('app.name') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('build/medias/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('build/medias/logo.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap">
        @vite('resources/assets/css/app.css')
        @stack('styles')
    </head>
    <body class="min-h-screen bg-background text-foreground antialiased">
        <div class="mx-auto flex min-h-screen w-full max-w-md flex-col">
            @yield('content')
        </div>
        @vite('resources/assets/js/app.js')
        @stack('scripts')
    </body>
</html>
