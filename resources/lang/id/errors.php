<?php

return [
    'badge' => 'Error',
    'version' => 'Versi',
    'meta_description' => 'Ada yang tidak berjalan sesuai rencana. Berikut yang bisa Anda lakukan selanjutnya.',

    'actions' => [
        'back_home' => 'Kembali ke Beranda',
        'go_back' => 'Kembali',
        'dashboard' => 'Dashboard',
        'admin_dashboard' => 'Dashboard Admin',
    ],

    'extra' => [
        'request_id' => 'Request ID',
        'timestamp' => 'Waktu',
        'environment' => 'Environment',
    ],

    '400' => [
        'title' => 'Permintaan Tidak Valid',
        'description' => 'Server tidak dapat memahami permintaan Anda. Silakan periksa kembali input Anda dan coba lagi.',
    ],
    '401' => [
        'title' => 'Tidak Terautentikasi',
        'description' => 'Anda perlu masuk terlebih dahulu untuk mengakses halaman ini.',
    ],
    '403' => [
        'title' => 'Akses Ditolak',
        'description' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
    ],
    '404' => [
        'title' => 'Halaman Tidak Ditemukan',
        'description' => 'Halaman yang Anda cari tidak ada atau telah dipindahkan.',
    ],
    '405' => [
        'title' => 'Metode Tidak Diizinkan',
        'description' => 'Metode permintaan ini tidak didukung untuk halaman ini.',
    ],
    '419' => [
        'title' => 'Halaman Kedaluwarsa',
        'description' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman dan coba lagi.',
    ],
    '422' => [
        'title' => 'Permintaan Tidak Dapat Diproses',
        'description' => 'Kami tidak dapat memproses permintaan Anda karena ada data yang tidak valid.',
    ],
    '429' => [
        'title' => 'Terlalu Banyak Permintaan',
        'description' => 'Anda telah membuat terlalu banyak permintaan. Silakan tunggu sebentar dan coba lagi.',
    ],
    '500' => [
        'title' => 'Kesalahan Server Internal',
        'description' => 'Terjadi kesalahan di sisi kami. Kami sedang berupaya memperbaikinya.',
    ],
    '502' => [
        'title' => 'Gateway Bermasalah',
        'description' => 'Kami menerima respons tidak valid dari server upstream. Silakan coba lagi sebentar lagi.',
    ],
    '503' => [
        'title' => 'Layanan Tidak Tersedia',
        'description' => 'Kami sedang menjalani pemeliharaan. Silakan coba lagi nanti.',
    ],
];
