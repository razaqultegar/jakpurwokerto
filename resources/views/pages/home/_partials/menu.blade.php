@php
    $menus = [
        ['label' => 'Komunitas', 'color' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'ri-team-line', 'badge' => 'BARU', 'action' => 'komunitas'],
        ['label' => 'Statistik', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'ri-bar-chart-line', 'badge' => 'SEGERA'],
        ['label' => 'Tiket', 'color' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'ri-ticket-2-line', 'href' => route('ticket.show', 'the-7ourney')],
        ['label' => 'Merchandise', 'color' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'ri-t-shirt-line', 'href' => route('merchandise.show', 'the-7ourney')],
        ['label' => 'Berita', 'color' => 'bg-primary/15', 'text' => 'text-primary', 'icon' => 'ri-newspaper-line', 'href' => route('article.index')],
        ['label' => 'Tour', 'color' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'ri-bus-line', 'badge' => 'SEGERA'],
        ['label' => 'Donasi', 'color' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'ri-hand-heart-line', 'badge' => 'SEGERA'],
    ];

    $komunitasItems = [
        ['label' => 'Pendataan Anggota', 'desc' => 'Daftarkan diri sebagai anggota Biro 01 Purwokerto.', 'color' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'ri-user-add-line'],
        ['label' => 'Perpanjang KTA', 'desc' => 'Perpanjang masa berlaku Kartu Tanda Anggota.', 'color' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'ri-refresh-line'],
        ['label' => 'Pembuatan KTA Baru', 'desc' => 'Ajukan pembuatan Kartu Tanda Anggota baru.', 'color' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'icon' => 'ri-id-card-line'],
    ];
@endphp

@foreach ($menus as $menu)
    @if (isset($menu['action']) && $menu['action'] === 'komunitas')
    <button type="button" class="menu-tile" data-komunitas-open>
        <div class="relative">
            <div class="menu-tile__icon {{ $menu['color'] }}">
                <i class="{{ $menu['icon'] }} text-2xl {{ $menu['text'] }}"></i>
            </div>
            @if (isset($menu['badge']))
            <span class="tag-xs absolute -top-1 -left-2">{{ $menu['badge'] }}</span>
            @endif
        </div>
        <span class="menu-tile__label">{{ $menu['label'] }}</span>
    </button>
    @else
    <a href="{{ $menu['href'] ?? '#' }}" class="menu-tile">
        <div class="relative">
            <div class="menu-tile__icon {{ $menu['color'] }}">
                <i class="{{ $menu['icon'] }} text-2xl {{ $menu['text'] }}"></i>
            </div>
            @if (isset($menu['badge']))
            <span class="tag-xs absolute -top-1 -left-2">{{ $menu['badge'] }}</span>
            @endif
        </div>
        <span class="menu-tile__label">{{ $menu['label'] }}</span>
    </a>
    @endif
@endforeach

<div class="overlay" aria-hidden="true" data-komunitas-drawer>
    <div class="overlay-backdrop" data-komunitas-drawer-backdrop></div>
    <div class="absolute inset-x-0 bottom-0 mx-auto max-w-screen-sm">
        <div class="mx-auto max-w-480">
            <div class="translate-y-full rounded-t-3xl bg-white p-5 pb-7 shadow-[0_-10px_30px_-5px_rgba(0,0,0,0.25)] transition-transform duration-300 ease-out" data-komunitas-drawer-panel>
                <div class="drawer-grip mb-4"></div>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="section-title">Layanan Komunitas</h3>
                        <p class="text-[10px] text-onyx">Pilih layanan keanggotaan yang ingin kamu akses.</p>
                    </div>
                    <button type="button" class="icon-btn-xs" data-komunitas-drawer-close aria-label="Tutup">
                        <i class="ri-close-line text-base"></i>
                    </button>
                </div>
                <div class="flex flex-col gap-2.5">
                    @foreach ($komunitasItems as $item)
                    <a href="#" class="flex items-center gap-3 rounded-2xl border border-mercury bg-white p-3 transition active:scale-[0.99] hover:border-primary-soft hover:bg-primary-softer/40">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $item['color'] }}">
                            <i class="{{ $item['icon'] }} text-xl {{ $item['text'] }}"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-foreground">{{ $item['label'] }}</p>
                            <p class="mt-0.5 text-[10px] leading-snug text-onyx">{{ $item['desc'] }}</p>
                        </div>
                        <i class="ri-arrow-right-s-line text-lg text-onyx"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
