<?php

namespace App\Http\Controllers;

class CheckoutController extends Controller
{
    public function index()
    {
        $checkout = [
            'item' => [
                'name' => 'Jersey "the 7ourney"',
                'category' => 'Dewasa',
                'size' => 'L',
                'qty' => 1,
                'price' => 175000,
                'image' => 'medias/the-7ourney/artboard1.jpg',
            ],
            'banks' => [
                [
                    'key' => 'bca',
                    'name' => 'Bank BCA',
                    'logo_text' => 'BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'JakPurwokerto Raya',
                    'color' => 'bg-sky-600',
                ],
                [
                    'key' => 'mandiri',
                    'name' => 'Bank Mandiri',
                    'logo_text' => 'MDR',
                    'account_number' => '0987654321',
                    'account_name' => 'JakPurwokerto Raya',
                    'color' => 'bg-yellow-500',
                ],
            ],
            'qris' => [
                'name' => 'DANA',
                'merchant' => 'JakPurwokerto Raya',
                'image' => 'medias/qris-dana.png',
            ],
        ];

        return view('pages.checkout.index', [
            'title' => 'Checkout',
            'checkout' => $checkout,
        ]);
    }
}
