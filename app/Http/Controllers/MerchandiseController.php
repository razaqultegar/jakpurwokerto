<?php

namespace App\Http\Controllers;

class MerchandiseController extends Controller
{
    public function show(string $slug)
    {
        $merchandise = [
            'slug' => 'the-7ourney',
            'badge' => 'Open Pre-Order',
            'edition' => 'Edisi Spesial #7 Tahun',
            'name' => 'Jersey "the 7ourney"',
            'tagline' => 'Bersama jadi keluarga, satu jiwa untuk Macan Kemayoran.',
            'description' => 'Jersey edisi spesial 7 tahun JakPurwokerto. Dirancang dengan motif batik Banyumasan yang dipadukan corak khas Persija, melambangkan perjalanan, kebersamaan, dan kebanggaan keluarga The Jakmania Purwokerto Raya. Bahan dryfit premium, jahitan rapi, dan cetak sublimasi tahan lama.',
            'price_min' => 135000,
            'price_max' => 200000,
            'price_original_min' => 175000,
            'price_original_max' => 260000,
            'discount_percent_min' => 22,
            'discount_percent_max' => 23,
            'sold' => 0,
            'stock_limit' => 300,
            'po_start' => '2026-05-20T00:00:00+07:00',
            'po_end' => '2026-06-20T23:59:59+07:00',
            'estimated_ship' => '15 Juli 2026',
            'gallery' => [
                'medias/the-7ourney/artboard1.jpg',
                'medias/the-7ourney/artboard2.jpg',
                'medias/the-7ourney/artboard4.jpg',
                'medias/the-7ourney/artboard5.jpg',
            ],
            'categories' => [
                ['key' => 'dewasa', 'name' => 'Dewasa', 'desc' => '15+ tahun', 'icon' => 'ri-user-3-fill', 'active' => true, 'price' => 175000],
                ['key' => 'anak', 'name' => 'Anak', 'desc' => '4 - 14 tahun', 'icon' => 'ri-bear-smile-fill', 'active' => false, 'price' => 135000],
            ],
            'sleeves' => [
                ['key' => 'pendek', 'name' => 'Lengan Pendek', 'desc' => 'Standar match-day', 'icon' => 'ri-t-shirt-line', 'active' => true, 'prices' => ['dewasa' => 175000, 'anak' => 135000]],
                ['key' => 'panjang', 'name' => 'Lengan Panjang', 'desc' => 'Cocok untuk cuaca dingin', 'icon' => 'ri-shirt-line', 'active' => false, 'prices' => ['dewasa' => 200000, 'anak' => 150000]],
            ],
            'sizes' => ['S', 'M', 'L', 'XL', 'Kustom'],
            'custom_size_fee' => 15000,
            'specs' => [
                ['label' => 'Bahan', 'value' => 'Puma'],
                ['label' => 'Logo', 'value' => '3D Rubber'],
                ['label' => 'Pola', 'value' => 'Reglan Retro'],
                ['label' => 'Sablon', 'value' => 'Polyflex'],
            ],
        ];

        return view('pages.merchandise.show', [
            'title' => $merchandise['name'],
            'merch' => $merchandise,
        ]);
    }
}
