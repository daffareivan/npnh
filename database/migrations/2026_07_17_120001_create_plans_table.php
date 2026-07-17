<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('credits')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->json('features')->nullable();
            $table->string('status')->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('plans')->insert([
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'credits' => 8,
                'is_custom' => false,
                'features' => json_encode(['8 Credits', '1 Credit = 1 Download', 'Tidak ada masa aktif', 'Cocok untuk mencoba aplikasi']),
                'status' => 'active',
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'price' => 50000,
                'credits' => 100,
                'is_custom' => false,
                'features' => json_encode(['Semua fitur tersedia', '100 Credits', 'Riwayat konversi lengkap', 'Prioritas proses normal']),
                'status' => 'active',
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price' => 80000,
                'credits' => 300,
                'is_custom' => false,
                'features' => json_encode(['Semua fitur tersedia', '300 Credits', 'Prioritas proses lebih tinggi', 'Akses lebih cepat saat antrean ramai']),
                'status' => 'active',
                'sort_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Custom',
                'slug' => 'custom',
                'price' => null,
                'credits' => null,
                'is_custom' => true,
                'features' => json_encode(['Unlimited atau custom credits', 'Harga khusus', 'Studio, tim, komunitas, developer, agency', 'Dedicated support']),
                'status' => 'active',
                'sort_order' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
