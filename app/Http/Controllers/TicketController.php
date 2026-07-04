<?php

namespace App\Http\Controllers;

class TicketController extends Controller
{
    public function show(string $slug)
    {
        $events = [
            'the-7ourney' => [
                'slug' => 'the-7ourney',
                'badge' => 'Tiket Sudah Dibuka',
                'name' => 'THE 7OURNEY',
                'subtitle' => 'Acara komunitas, hiburan, dan momen berkesan untuk keluarga besar The Jakmania Biro Purwokerto.',
                'day' => 'Sabtu - Minggu',
                'date' => '11 - 12 Juli 2026',
                'venue' => 'Villa Kasih Baturaden',
                'maps_query' => 'Villa Kasih Baturaden',
                'venues' => [
                    ['day' => 'Sabtu', 'date' => '11 Juli', 'name' => 'Villa Kasih Baturaden', 'maps_query' => 'Villa Kasih Baturaden'],
                    ['day' => 'Minggu', 'date' => '12 Juli', 'name' => 'B23 Soccerfield', 'maps_query' => 'B23 Soccerfield'],
                ],
                'description' => "THE7OURNEY\n7th Anniversary The Jakmania Biro Purwokerto\n\nTujuh tahun bukan sekadar angka. Tujuh tahun adalah perjalanan yang dipenuhi cerita, tawa, perjuangan, dan loyalitas dalam satu keluarga besar.\n\nTHE7OURNEY hadir sebagai simbol perjalanan tersebut. Sebuah perayaan yang bukan hanya mengenang setiap langkah yang telah dilalui, melainkan juga menjadi awal untuk melangkah lebih jauh bersama. Dari tribun hingga jalan pulang, dari satu generasi ke generasi berikutnya, semangat kebersamaan selalu menjadi alasan kita tetap berdiri dalam satu warna.\n\nRangkaian kegiatan ini menjadi lebih lengkap dengan keseruan Fun Football bersama JAA, serta berbagai momen yang akan mempererat silaturahmi didalamnya. Jadi Ini bukan hanya tentang sebuah anniversary, melainkan tentang menciptakan kenangan baru bersama orang-orang yang memiliki semangat dan kecintaan yang sama.\n\n#THE7OURNEY #JakmaniaPurwokerto",
                'gallery' => [
                    'medias/the-7ourney/thumbnail.png',
                ],
                'agenda' => [
                    [
                        'day' => 'Sabtu',
                        'date' => '11 Juli',
                        'items' => [
                            ['icon' => 'ri-hand-heart-line', 'title' => 'Gathering', 'desc' => 'Silaturahmi dan kebersamaan keluarga besar The Jakmania Purwokerto.'],
                            ['icon' => 'ri-gift-2-line', 'title' => 'Games & Doorprize', 'desc' => 'Sesi permainan seru dengan beragam hadiah doorprize.'],
                            ['icon' => 'ri-mic-2-line', 'title' => 'Live Music', 'desc' => 'Penampilan musik dan hiburan utama.'],
                        ],
                    ],
                    [
                        'day' => 'Minggu',
                        'date' => '12 Juli',
                        'items' => [
                            ['icon' => 'ri-football-line', 'title' => 'Fun Soccer', 'desc' => 'Keseruan Fun Soccer bersama JAA di B23 Soccerfield.'],
                        ],
                    ],
                ],
                'tickets' => [
                    [
                        'key' => 'ticket',
                        'name' => 'Tiket',
                        'desc' => 'Akses entry acara',
                        'price' => 35000,
                        'note' => 'Masuk pukul 18.00',
                        'featured' => true,
                    ],
                ],
                'faqs' => [
                    [
                        'question' => 'Apakah ada penjualan OTS?',
                        'answer' => 'Ya, panitia menyediakan tiket OTS namun terbatas yaaaaaa...',
                    ],
                    [
                        'question' => 'Apakah tiket dapat dipindahtangankan?',
                        'answer' => 'Bisa, selama tiket belum digunakan untuk proses check-in oleh panitia.',
                    ],
                    [
                        'question' => 'Apakah tiket dapat dibatalkan atau diuangkan kembali?',
                        'answer' => 'Tidak. Tiket yang telah dibeli tidak dapat dibatalkan, ditukar, atau diuangkan kembali, kecuali terdapat kebijakan lain dari panitia.',
                    ],
                    [
                        'question' => 'Apakah tersedia tempat menginap?',
                        'answer' => 'Ya, panitia menyediakan tempat untuk menginap selama acara berlangsung.',
                    ],
                    [
                        'question' => 'Bagaimana proses check-in saat hari acara?',
                        'answer' => 'Cukup tunjukkan e-ticket atau QR Code yang diterima setelah pembelian kepada panitia di meja registrasi.',
                    ],
                    [
                        'question' => 'Kapan registrasi peserta dibuka?',
                        'answer' => 'Cukup tunjukkan e-ticket atau QR Code yang diterima setelah pembelian kepada panitia di meja registrasi.',
                    ],
                ],
            ],
        ];

        if (! isset($events[$slug])) {
            abort(404);
        }

        $event = $events[$slug];
        $event['state'] = [
            'is_active' => true,
        ];

        return view('pages.ticket.show', [
            'title' => $event['name'],
            'event' => $event,
        ]);
    }
}
