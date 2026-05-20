<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;

class MerchandiseController extends Controller
{
    public function show(string $slug)
    {
        $merchandise = [
            'slug' => 'the-7ourney',
            'badge' => 'Open Pre-Order',
            'edition' => 'Edisi Spesial #7 Tahun',
            'name' => 'THE 7OURNEY',
            'tagline' => 'Years of Stories, Loyalty, and Pride',
            'description' => "Sebuah jersey yang bukan cuma dibuat untuk dipakai, tapi juga untuk mengingat setiap cerita dan proses yang sudah dilewati bersama selama 7 tahun terakhir.\n\nDirancang menggunakan motif khas batik Banyumasan yang dipadukan dengan corak identitas Persija, jersey yang menjadi simbol perjalanan, kebersamaan, dan kebanggaan keluarga besar The Jakmania Biro Purwokerto. Perpaduan unsur budaya lokal dan semangat supporter culture menghadirkan desain yang kuat, klasik, dan tetap relevan dipakai di mana saja — baik saat matchday maupun daily wear.\n\nBukan cuma soal tampilan, kualitas juga jadi bagian penting dari perjalanan ini. Menggunakan bahan dryfit premium yang nyaman dipakai, jahitan rapi dan teknik cetak sublimasi tahan lama, jersey ini siap menemani setiap perjalanan tanpa menghilangkan identitas dan kualitas terbaiknya.\n\nKarena pada akhirnya, jersey ini bukan sekadar merchandise. Ini adalah simbol cerita yang terus hidup. Tentang rumah, tentang kebersamaan, dan tentang perjalanan yang akan terus berjalan bersama.",
            'price' => 135000,
            'price_original' => 175000,
            'discount_percent' => 22,
            'sold' => 0,
            'stock_limit' => 300,
            'po_start' => '2026-05-20T19:28:00+07:00',
            'po_end' => '2026-06-20T23:59:59+07:00',
            'estimated_ship' => '15 Juli 2026',
            'gallery' => [
                'medias/the-7ourney/artboard1.jpg',
                'medias/the-7ourney/artboard2.jpg',
                'medias/the-7ourney/artboard3.jpg',
                'medias/the-7ourney/artboard4.jpg',
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

        $stockLimit = $merchandise['stock_limit'] ?? 0;
        $sold = $this->countSold($merchandise['slug']);
        $merchandise['sold'] = $sold;
        $remaining = max(0, $stockLimit - $sold);
        $progress = $stockLimit > 0 ? min(100, (int) round(($sold / $stockLimit) * 100)) : 0;

        $now = now();
        $poStart = Carbon::parse($merchandise['po_start']);
        $poEnd = Carbon::parse($merchandise['po_end']);
        $isSoldOut = $stockLimit > 0 && $remaining <= 0;
        $isBeforeStart = $now->lt($poStart);
        $isAfterEnd = $now->gt($poEnd);
        $isActive = ! $isBeforeStart && ! $isAfterEnd && ! $isSoldOut;

        $merchandise['state'] = [
            'stock_remaining' => $remaining,
            'stock_progress' => $progress,
            'po_start_at' => $poStart,
            'po_end_at' => $poEnd,
            'is_sold_out' => $isSoldOut,
            'is_before_start' => $isBeforeStart,
            'is_after_end' => $isAfterEnd,
            'is_active' => $isActive,
        ];

        return view('pages.merchandise.show', [
            'title' => $merchandise['name'],
            'merch' => $merchandise,
        ]);
    }

    private function countSold(string $slug): int
    {
        $sold = 0;
        Order::whereIn('status', ['verified', 'completed'])
            ->select(['item'])
            ->chunk(200, function ($orders) use (&$sold, $slug) {
                foreach ($orders as $order) {
                    foreach ($order->item ?? [] as $line) {
                        if (($line['slug'] ?? null) === $slug) {
                            $sold += (int) ($line['qty'] ?? 0);
                        }
                    }
                }
            });

        return $sold;
    }
}
