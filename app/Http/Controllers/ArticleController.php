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
                'slug' => 'persija-tanpa-komunikasi',
                'image' => 'medias/2026/persija-tanpa-komunikasi.png',
                'title' => 'Persija tanpa Komunikasi',
                'excerpt' => 'Sikap boikot PP The Jakmania bukan sekadar soal harga tiket yang naik, melainkan soal komunikasi Persija yang dinilai kurang transparan dan tidak melibatkan pendukungnya sejak awal.',
                'body' => "Sikap boikot yang dikeluarkan PP The Jakmania persoalannya bukan sekadar naiknya harga tiket, melainkan lebih dari itu: sejak awal The Jakmania tidak diikutsertakan. Persija punya pendukung yang besar, resmi, dan terorganisir, tetapi dalam penentuan harga tiket Persija justru mengambil langkah sendiri.\n\n> *Kasarannya begini: masa iya mau menaikkan harga tiket, tetapi pendukungnya sendiri (The Jakmania) tidak diajak berdiskusi? Setidaknya berterus terang di awal — apa masalahnya, apa urgensinya, kenapa harus dilakukan, bagaimana rencana ke depannya, dan lain sebagainya. Hal-hal itu bisa didiskusikan terlebih dahulu untuk dicari solusi terbaiknya.*\n\nTermasuk penutupan Tribun Timur, rasanya kurang etis ketika Persija mengambil keputusan seenaknya sendiri tanpa pemberitahuan di awal. Karena kita semua tahu, di Persija, Tribun Timur itu banyak penghuninya, dan Tribun Timur — termasuk Utara dan Selatan — The Jakmania punya hak di area itu. Polemik Persija dan The Jakmania saat ini tidak bisa disamakan dengan event berbayar lainnya.\n\n> *Misalnya, katakanlah ketika kita pergi ke sebuah konser: kasusnya jelas, hanya sebatas pembeli dan penjual. Promotor mau membuka tiket berapa pun atau dengan sistem seperti apa pun, kita tidak akan ambil pusing. Nah, kasus Persija dengan The Jakmania di sini lebih dari itu — ibarat rangkaian gerbong kereta yang seharusnya saling melengkapi, bergandengan, dan berkesinambungan, semata-mata karena tujuannya sama: kejayaan Persija.*\n\nIntinya, yang saya lihat dari kejadian ini adalah komunikasi Persija ke The Jakmania kurang baik, semena-mena, bahkan terkesan menyepelekan. Ketika kejadian seperti ini dibiarkan atau tidak ada sikap (gertakan) dari PP The Jakmania, maka kasus seperti ini pasti akan terjadi lagi di kemudian hari. Dan seandainya itu terjadi, yang rugi bukan hanya yang ber-KTA atau yang ikut korwil, melainkan semua PersijaFans yang ada. Pasti akan muncul juga kebijakan-kebijakan yang terasa timpang sebelah, karena sistem kontrol dari The Jakmania sendiri sudah ditiadakan atau disepelekan.\n\nSekarang, bagi yang tidak sependapat dengan **boikot**, mungkin setelah Persija merilis harga tiket — yang ternyata hanya naik ke Rp130.000–150.000 — akan berkata:\n\n> *“Naik Rp20.000 saja kok koar-koar pakai boikot segala. Kampungan, mental kismin, tidak lihat apa pelatih dan skuadnya.”*\n\nBaik, kita juga sama-sama tahu itu. Tetapi banyak di antara kita yang tidak tahu bahwa harga termurah (**Tier 2**) yang akan dikeluarkan Persija sebelum adanya sikap **boikot** itu jauh di atas harga rilis tiket tier bawah (**Tier 1**).\n\nOpini saya, keputusan Persija merilis harga Rp130.000–150.000 setelah pernyataan sikap **boikot** turun adalah sebuah kepanikan belaka — karena takut peluncuran yang digadang-gadang bakal meriah ternyata justru sepi akibat sikap **boikot** The Jakmania. Sebagai bahan penguat, Persija juga berinisiatif membagikan tiket gratis untuk siswa di enam cabang SS Persija Academy beserta keluarga inti siswa tersebut.\n\nYang perlu diperhatikan, kita tidak tahu setelah laga kemarin harga tiket kandang akan seperti apa: bisa sama, bisa turun, bisa naik lagi, atau bahkan ada kebijakan lain yang lebih merugikan. Kita tidak tahu, tidak ada jaminan. Karena — balik lagi ke poin di atas — komunikasi Persija ke The Jakmania kurang baik dan kurang transparan. *(Ini soal antisipasi jangka panjang.)*\n\nAda kasus menarik juga soal donasi, yang menurut informasi sebagian dari penjualan tiket kemarin didonasikan ke korban bencana alam. Kalau kita lihat, sejak awal tidak ada rencana atau informasi seperti itu. Bahkan ketika hari Rabu PP The Jakmania beserta korwil mendatangi kantor Persija untuk memprotes dan berdiskusi soal kebijakan tiket, sama sekali tidak ada informasi soal rencana donasi tersebut. Jujur, janggal.\n\nDi tempat lain, ketika ada laga amal — atau laga yang minimal sebagian penghasilan tiketnya didonasikan — pasti akan diinformasikan sejak awal, supaya calon penonton tergugah atau tertarik untuk menonton laga tersebut (semacam strategi pemasaran). Tetapi di sini tidak ada rencana seperti itu. Sangat disayangkan. Justru yang saya lihat, ini sebatas tindakan cuci tangan Persija terhadap polemik yang ada saat ini. Mungkin supaya terlihat baik di mata teman-teman yang tidak sependapat dengan sikap **boikot** — seolah-olah kenaikan harga tiket itu demi kepentingan donasi. Padahal sebenarnya murni karena kepentingan bisnis. Haha. *(Ini murni opini pribadi.)*\n\nSekarang begini. Mari kita bayangkan seandainya sejak awal komunikasi Persija dengan The Jakmania baik — baik soal tiket, sistem, rencana donasi, atau apa pun itu. Masalah tiket naik, saya rasa, bukan sebuah masalah. Justru saya yakin The Jakmania akan mendukung kebijakan tersebut, dan pada akhirnya polemik di atas tidak pernah ada: peluncuran meriah, penjualan tiket dan jersey lebih tinggi, dan yang pasti akan lebih indah untuk saudara-saudara yang kita bantu.\n\nSikap **boikot** yang diambil PP The Jakmania, menurut saya, sudah benar dan memang diperlukan untuk saat ini — sebagai sistem kontrol, langkah antisipasi, dan bahan koreksi, dengan harapan ke depannya Persija bisa lebih komunikatif dan transparan, terutama untuk kebijakan yang berkaitan langsung dengan The Jakmania, sehingga polemik seperti ini tidak terjadi lagi di masa mendatang. Dan yang perlu kita ketahui: PP The Jakmania berjuang dan berpikir sekeras ini demi kebaikan bersama — untuk Persija, untuk The Jakmania, dan juga untuk seluruh PersijaFans yang ada.\n\n**Salam dari Purwokerto!! Jakkkkk**\n\n*Khilmi Choerul F*",
                'published_at' => '2026-08-31 14:00:00',
                'featured' => true,
            ],
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
                        'label' => 'Laporan Pertanggung Jawaban The Jakmania Biro Purwokerto',
                        'description' => 'Dokumen resmi hasil rapat biro menetapkan Ketua The Jakmania Purwokerto periode 2026-2029.',
                        'url' => 'https://drive.google.com/file/d/1zgzZCOeMA6FF1Zjb_lXnO62bGYISHvaA/view?usp=sharing',
                    ],
                    [
                        'label' => 'Draft - Peraturan Organisasi AD/ART The Jakmania Biro Purwokerto',
                        'description' => 'Daftar lengkap susunan pengurus The Jakmania Biro Purwokerto periode 2026-2029.',
                        'url' => 'https://drive.google.com/file/d/1fCI3sEVkeACA4jSExB_uQkb14ny7X_ru/view?usp=sharing',
                    ],
                ],
                'published_at' => '2026-07-09 19:30:00',
                'featured' => false,
            ],
        ];
    }
}
