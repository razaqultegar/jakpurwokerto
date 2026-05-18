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
            'price' => 175000,
            'price_original' => 225000,
            'discount_percent' => 22,
            'rating' => 4.9,
            'sold' => 0,
            'stock_limit' => null,
            'po_start' => '2026-05-20T00:00:00+07:00',
            'po_end' => '2026-06-20T23:59:59+07:00',
            'estimated_ship' => '15 Juli 2026',
            'gallery' => [
                'medias/the-7ourney/artboard1.jpg',
                'medias/the-7ourney/artboard2.jpg',
                'medias/the-7ourney/artboard3.jpg',
                'medias/the-7ourney/artboard4.jpg',
                'medias/the-7ourney/artboard5.jpg',
                'medias/the-7ourney/artboard6.jpg',
                'medias/the-7ourney/artboard7.jpg',
            ],
            'categories' => [
                ['key' => 'dewasa', 'name' => 'Dewasa', 'desc' => '15+ tahun', 'icon' => 'ri-user-3-fill', 'active' => true],
                ['key' => 'anak', 'name' => 'Anak', 'desc' => '4 - 14 tahun', 'icon' => 'ri-bear-smile-fill', 'active' => false],
            ],
            'sizes' => ['S', 'M', 'L', 'XL', '2XL', '3XL'],
            'highlights' => [
                ['icon' => 'ri-shirt-line', 'label' => 'Dryfit Premium', 'desc' => 'Adem & menyerap keringat'],
                ['icon' => 'ri-scissors-cut-line', 'label' => 'Jahitan Rapi', 'desc' => 'Finishing standar match-day'],
                ['icon' => 'ri-recycle-line', 'label' => 'Eco Print', 'desc' => 'Sublimasi tahan lama'],
                ['icon' => 'ri-shield-star-line', 'label' => 'Resmi', 'desc' => 'Lisensi JakPurwokerto'],
            ],
            'specs' => [
                ['label' => 'Bahan', 'value' => 'Dryfit Premium 180gsm'],
                ['label' => 'Teknik Cetak', 'value' => 'Sublimasi Full Print'],
                ['label' => 'Kerah', 'value' => 'O-neck dengan aksen'],
                ['label' => 'Asal Produksi', 'value' => 'Purwokerto, Indonesia'],
            ],
            'reviews' => [
                ['name' => 'Bayu A.', 'initial' => 'B', 'rating' => 5, 'comment' => 'Bahannya adem banget, jahitannya juga rapi. Worth it!'],
                ['name' => 'Sinta R.', 'initial' => 'S', 'rating' => 5, 'comment' => 'Motif batiknya keren, beda dari yang lain. Bangga pakainya.'],
                ['name' => 'Dimas P.', 'initial' => 'D', 'rating' => 4, 'comment' => 'Ukuran pas badan, cuma agak lama pengirimannya.'],
            ],
        ];

        return view('pages.merchandise.show', [
            'title' => $merchandise['name'],
            'merch' => $merchandise,
        ]);
    }
}
