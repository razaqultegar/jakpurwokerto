@php
    $menus = [
        ['label' => 'Tiket', 'color' => 'bg-primary-light/15', 'text' => 'text-primary', 'icon' => 'ri-ticket-2-line'],
        ['label' => 'Nobar', 'color' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'ri-tv-2-line'],
        ['label' => 'Merchandise', 'color' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'ri-t-shirt-line'],
        ['label' => 'Tour Away', 'color' => 'bg-primary-lighter/20', 'text' => 'text-primary-lighter', 'icon' => 'ri-bus-line'],
        ['label' => 'Komunitas', 'color' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'ri-team-line', 'badge' => 'BARU'],
        ['label' => 'Donasi', 'color' => 'bg-teal-100', 'text' => 'text-teal-600', 'icon' => 'ri-heart-3-line'],
        ['label' => 'Berita', 'color' => 'bg-primary/15', 'text' => 'text-primary', 'icon' => 'ri-newspaper-line', 'badge' => 'BARU'],
        ['label' => 'Galeri', 'color' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'ri-image-2-line', 'badge' => 'SALE'],
    ];
@endphp

@foreach($menus as $menu)
<a href="#" class="flex flex-col items-center gap-1.5 text-center">
    <div class="relative">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $menu['color'] }}">
            <i class="{{ $menu['icon'] }} text-2xl {{ $menu['text'] }}"></i>
        </div>
        @if(isset($menu['badge']))
            <span class="absolute -top-1 -left-2 rounded bg-red-500 px-1 py-0.5 text-[8px] font-bold text-white">{{ $menu['badge'] }}</span>
        @endif
    </div>
    <span class="text-[11px] font-medium leading-tight text-foreground">{{ $menu['label'] }}</span>
</a>
@endforeach
