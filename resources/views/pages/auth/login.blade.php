@extends('layouts.admin', ['title' => 'Masuk'])

@section('content')
    <div class="relative w-full max-w-md">
        <div class="pointer-events-none absolute -top-24 left-1/2 h-64 w-64 -translate-x-1/2 rounded-full bg-primary/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -right-10 h-56 w-56 rounded-full bg-primary-light/20 blur-3xl"></div>
        <div class="relative overflow-hidden rounded-3xl bg-white shadow-[0_20px_60px_-15px_rgba(216,67,21,0.25)] ring-1 ring-mercury">
            <div class="relative overflow-hidden bg-linear-to-br from-primary via-primary-light to-primary-lighter px-6 py-8 text-white">
                <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -left-12 bottom-0 h-32 w-32 rounded-full bg-white/10"></div>
                <div class="relative flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/30 backdrop-blur-md">
                        <img src="{{ asset('build/medias/logo.png') }}" class="h-8 w-8 object-contain" alt="{{ config('app.name') }}">
                    </span>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/80">Panel Admin</p>
                        <h1 class="text-lg font-black leading-tight">{{ config('app.name') }}</h1>
                    </div>
                </div>
                <div class="relative mt-6">
                    <h2 class="text-2xl font-black">Selamat datang kembali</h2>
                    <p class="mt-1 text-xs leading-relaxed text-white/85">Masuk untuk mengelola semua yang ada di aplikasi.</p>
                </div>
            </div>
            <div class="rounded-t-3xl bg-white px-6 pb-8 pt-6 sm:px-8">
                @if (session('status'))
                <div class="mb-4 flex items-start gap-2 rounded-xl bg-emerald-50 px-3 py-2.5 text-xs text-emerald-700 ring-1 ring-emerald-200">
                    <i class="ri-checkbox-circle-line mt-0.5 text-sm"></i>
                    <span>{{ session('status') }}</span>
                </div>
                @endif
                @if ($errors->any())
                <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-3 py-2.5 text-xs text-red-700 ring-1 ring-red-200">
                    <i class="ri-error-warning-line mt-0.5 text-sm"></i>
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('login') }}" method="post" class="space-y-5" novalidate>
                    @csrf
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-foreground">Alamat Email</label>
                        <div class="relative">
                            <span class="field-icon"><i class="ri-mail-line text-base"></i></span>
                            <input type="email" id="email" class="field-control field-control--with-icon py-3! text-sm!" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username">
                        </div>
                    </div>
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-foreground">Kata Sandi</label>
                        <div class="relative" data-password-wrap>
                            <span class="field-icon"><i class="ri-lock-2-line text-base"></i></span>
                            <input type="password" id="password" class="field-control field-control--with-icon py-3! text-sm! pr-10" name="password" placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" data-password-toggle class="absolute inset-y-0 right-2 flex items-center justify-center px-2 text-onyx hover:text-primary">
                                <i class="ri-eye-line text-base" data-password-icon></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 text-xs font-medium text-foreground">
                            <input type="checkbox" class="h-4 w-4 rounded border-mercury text-primary focus:ring-primary" name="remember">
                            Ingat saya
                        </label>
                        <span class="text-xs text-onyx">Akses khusus admin</span>
                    </div>
                    <button type="submit" class="btn-primary-gradient w-full">
                        <i class="ri-login-circle-line text-base"></i>
                        Masuk
                    </button>
                </form>
                <p class="mt-6 text-center text-xs text-onyx">&copy; {{ date('Y') }} {{ config('app.name') }}. Hak Cipta Terpelihara.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const btn = document.querySelector('[data-password-toggle]');
        const input = document.getElementById('password');
        const icon = document.querySelector('[data-password-icon]');
        if (!btn || !input || !icon) return;

        btn.addEventListener('click', () => {
            const isPwd = input.type === 'password';
            input.type = isPwd ? 'text' : 'password';
            icon.classList.toggle('ri-eye-line', !isPwd);
            icon.classList.toggle('ri-eye-off-line', isPwd);
        });
    })();
</script>
@endpush
