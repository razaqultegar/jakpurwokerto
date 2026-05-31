<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Titik Pengambilan (Pickup)
    |--------------------------------------------------------------------------
    |
    | Detail tiap kota pengambilan. `name` dipakai untuk dropdown checkout,
    | sedangkan `address` & `contact_*` ditampilkan pada email
    | "Pesanan Siap Diambil" agar pembeli tahu lokasi & pengurus yang dituju.
    |
    | PENTING: ganti alamat & nomor kontak placeholder di bawah dengan data asli.
    | Nomor kontak ditulis format internasional tanpa "+" (mis. 6281234567890).
    |
    */
    'locations' => [
        'purwokerto' => [
            'name' => 'Purwokerto',
            'address' => 'Jl. Jenderal Soedirman No.— , Purwokerto', // TODO: alamat asli
            'contact_name' => 'Pengurus Purwokerto',                  // TODO: nama pengurus
            'contact_phone' => '6282298001051',                       // TODO: nomor pengurus
        ],
        'ajibarang' => [
            'name' => 'Ajibarang',
            'address' => 'Jl. Raya Ajibarang No.— , Banyumas', // TODO: alamat asli
            'contact_name' => 'Pengurus Ajibarang',            // TODO: nama pengurus
            'contact_phone' => '6282298001051',                // TODO: nomor pengurus
        ],
        'jakarta' => [
            'name' => 'Jakarta',
            'address' => 'Jl. — No.— , Jakarta', // TODO: alamat asli
            'contact_name' => 'Pengurus Jakarta', // TODO: nama pengurus
            'contact_phone' => '6282298001051',   // TODO: nomor pengurus
        ],
    ],
];
