<?php

return [
    'accepted' => ':Attribute harus diterima.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'exists' => ':Attribute yang dipilih tidak valid.',
    'max' => [
        'string' => ':Attribute tidak boleh lebih dari :max karakter.',
        'file' => ':Attribute tidak boleh lebih dari :max kilobyte.',
        'numeric' => ':Attribute tidak boleh lebih dari :max.',
    ],
    'min' => [
        'string' => ':Attribute minimal harus :min karakter.',
        'file' => ':Attribute minimal harus :min kilobyte.',
        'numeric' => ':Attribute minimal harus :min.',
    ],
    'password' => [
        'letters' => ':Attribute harus mengandung minimal satu huruf.',
        'mixed' => ':Attribute harus mengandung minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => ':Attribute harus mengandung minimal satu angka.',
        'symbols' => ':Attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':Attribute tersebut pernah muncul dalam kebocoran data. Pilih :attribute lain.',
    ],
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'unique' => ':Attribute sudah digunakan.',
    'uploaded' => ':Attribute gagal diupload.',
    'attributes' => [
        'email' => 'email',
        'password' => 'password',
        'name' => 'nama',
        'title' => 'judul',
        'content' => 'konten',
        'rating' => 'rating',
    ],
];
