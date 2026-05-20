@php
    $menus = [
        ['label' => 'Komunitas', 'color' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'ri-team-line', 'badge' => 'SEGERA'],
        ['label' => 'Statistik', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'ri-bar-chart-line', 'badge' => 'SEGERA'],
        ['label' => 'Tiket', 'color' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'ri-ticket-2-line', 'badge' => 'SEGERA'],
        ['label' => 'Merchandise', 'color' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'ri-t-shirt-line', 'badge' => 'BARU', 'href' => url('/merchandise/the-7ourney')],
        ['label' => 'Berita', 'color' => 'bg-primary/15', 'text' => 'text-primary', 'icon' => 'ri-newspaper-line', 'badge' => 'SEGERA'],
        ['label' => 'Tour', 'color' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'ri-bus-line', 'badge' => 'SEGERA'],
        ['label' => 'Donasi', 'color' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'ri-hand-heart-line', 'badge' => 'SEGERA'],
    ];

    $primary = array_slice($menus, 0, 3);
    $others = array_slice($menus, 3);
@endphp

@foreach ($primary as $menu)
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
@endforeach

<button type="button" class="menu-tile" data-menu-more-open>
    <div class="menu-tile__icon bg-skull ring-1 ring-mercury">
        <i class="ri-apps-2-line text-2xl text-foreground"></i>
    </div>
    <span class="menu-tile__label">Lainnya</span>
</button>

<div class="overlay" aria-hidden="true" data-menu-drawer>
    <div class="overlay-backdrop" data-menu-drawer-backdrop></div>
    <div class="absolute inset-x-0 bottom-0 mx-auto max-w-screen-sm">
        <div class="mx-auto max-w-480">
            <div class="translate-y-full rounded-t-3xl bg-white p-5 pb-7 shadow-[0_-10px_30px_-5px_rgba(0,0,0,0.25)] transition-transform duration-300 ease-out" data-menu-drawer-panel>
                <div class="drawer-grip mb-4"></div>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="section-title">Menu Lainnya</h3>
                        <p class="text-[10px] text-onyx">Pilih kegiatan lain yang mau kamu ikuti.</p>
                    </div>
                    <button type="button" class="icon-btn-xs" data-menu-drawer-close aria-label="Tutup">
                        <i class="ri-close-line text-base"></i>
                    </button>
                </div>
                <div class="grid grid-cols-4 gap-y-5">
                    @foreach ($others as $menu)
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
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
