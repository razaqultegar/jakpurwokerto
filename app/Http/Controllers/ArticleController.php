<?php

namespace App\Http\Controllers;

class ArticleController extends Controller
{
    public function index()
    {
        return view('pages.article.index', [
            'title' => 'Berita',
            'articles' => self::data(),
        ]);
    }

    public function show(string $slug)
    {
        $articles = self::data();
        $article = collect($articles)->firstWhere('slug', $slug);

        if (! $article) {
            abort(404);
        }

        $related = collect($articles)
            ->reject(fn ($item) => $item['slug'] === $slug)
            ->take(3)
            ->all();

        return view('pages.article.show', [
            'title' => $article['title'],
            'article' => $article,
            'related' => $related,
        ]);
    }

    public static function latest(int $count = 3): array
    {
        return array_slice(self::data(), 0, $count);
    }

    private static function data(): array
    {
        return [
            [
                'slug' => 'rapat-biro-pemilihan-ketua-periode-2026-2029',
                'image' => 'medias/2026/731624296_18472717318098906_2108768429454495536_n.jpg',
                'gallery' => [
                    'medias/2026/728730313_18472717291098906_9034187455820194629_n.jpg',
                    'medias/2026/728893189_18472717315098906_495615224677232746_n.jpg',
                    'medias/2026/728963110_18472717300098906_6414708927837296149_n.jpg',
                    'medias/2026/731624296_18472717318098906_2108768429454495536_n.jpg',
                ],
                'title' => 'Rapat Biro The Jakmania Purwokerto: Pemilihan Ketua Biro Periode 2026-2029',
                'excerpt' => 'Rapat biro resmi digelar untuk menentukan kabinet kepengurusan baru, menandai babak baru kepemimpinan The Jakmania Biro Purwokerto untuk periode 2026-2029.',
                'body' => "The Jakmania Biro Purwokerto kembali menggelar rapat biro yang menjadi momen penting bagi kelangsungan organisasi. Dalam kesempatan ini, dilakukan pemilihan Ketua Biro untuk periode kepemimpinan 2026-2029.\n\nRapat berlangsung hangat namun penuh tanggung jawab, dihadiri oleh pengurus lama, anggota aktif, serta calon-calon yang siap mengemban amanah memimpin keluarga besar Jakmania di wilayah Purwokerto. Suasana kekeluargaan tetap terjaga sepanjang proses berlangsung, mencerminkan nilai persatuan yang selama ini dipegang teguh.\n\nDengan hasil musyawarah, Sdr. **Khilmi Choirul Fuadi** resmi terpilih sebagai Ketua Biro The Jakmania Purwokerto periode 2026-2029. Dalam sambutannya, beliau menyampaikan visi dan misi yang akan menjadi arah pergerakan biro untuk tiga tahun ke depan.\n\n**Visi:**\n\"Mewujudkan The Jakmania Biro Purwokerto sebagai supporter yang tertata, modern, transparan, terorganisir, dan mampu menjadi rumah yang nyaman bagi seluruh anggota.\"\n\n**Misi:**\n1. Membangun sistem organisasi yang tertata dan transparan melalui pendataan anggota, administrasi, serta penyampaian informasi yang jelas dan terbuka.\n2. Mengoptimalkan media sosial dan platform komunikasi biro sebagai sarana informasi, koordinasi, dan wadah aspirasi anggota secara aktif dan modern.\n3. Mengadakan kegiatan rutin yang mempererat solidaritas anggota, seperti nobar, gathering, touring, futsal, dan kegiatan sosial kemasyarakatan.\n4. Menciptakan lingkungan supporter yang nyaman, aman, dan saling menghargai tanpa membedakan latar belakang anggota.\n5. Meningkatkan koordinasi antar pengurus dan anggota agar tercipta biro yang lebih aktif, terorganisir, dan responsif terhadap kebutuhan komunitas.\n6. Menanamkan budaya mendukung Persija secara kreatif, loyal, tertib, dan tetap menjaga nama baik The Jakmania dan Persija di masyarakat.\n7. Membuka ruang diskusi dan aspirasi bagi anggota agar seluruh anggota dapat terlibat dalam perkembangan dan arah pergerakan biro.\n8. Mendorong regenerasi anggota muda yang aktif, bertanggung jawab, dan memiliki rasa kepedulian tinggi terhadap komunitas serta solidaritas supporter.\n\nTerpilihnya kepengurusan baru ini menandai babak baru dalam perjalanan biro. Berbagai program kerja dan rencana kegiatan ke depan akan disusun bersama oleh kabinet baru, dengan harapan dapat semakin mempererat solidaritas anggota serta memperkuat dukungan terhadap Persija Jakarta.\n\nSelamat dan sukses untuk Sdr. Khilmi Choirul Fuadi beserta kepengurusan baru The Jakmania Biro Purwokerto periode 2026-2029. Semoga langkah ini membawa warna baru yang lebih baik untuk kemajuan biro dan seluruh anggotanya. Macan Kemayoran tetap bersatu!",
                'attachments' => [
                    [
                        'label' => 'Surat Keputusan Ketua Biro',
                        'description' => 'Dokumen resmi hasil rapat biro menetapkan Ketua The Jakmania Purwokerto periode 2026-2029.',
                        'url' => 'https://drive.google.com/file/d/1zgzZCOeMA6FF1Zjb_lXnO62bGYISHvaA/view?usp=sharing',
                    ],
                    [
                        'label' => 'Susunan Kabinet Kepengurusan',
                        'description' => 'Daftar lengkap susunan pengurus The Jakmania Biro Purwokerto periode 2026-2029.',
                        'url' => 'https://drive.google.com/file/d/1fCI3sEVkeACA4jSExB_uQkb14ny7X_ru/view?usp=sharing',
                    ],
                ],
                'published_at' => '2026-07-09 19:30:00',
                'featured' => true,
            ],
        ];
    }
}
