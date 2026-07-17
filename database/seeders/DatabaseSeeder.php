<?php

namespace Database\Seeders;

use App\Models\CreditSetting;
use App\Models\User;
use App\Services\CreditService;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RbacSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(NavigationMenuSeeder::class);

        foreach ([
            CreditService::REGISTRATION_BONUS => 100,
            CreditService::DOWNLOAD_COST => 1,
            CreditService::ROBLOX_UPLOAD_COST => 2,
            CreditService::ALLOW_NEGATIVE_BALANCE => 0,
            CreditService::REFUND_FAILED_UPLOAD => 1,
        ] as $key => $value) {
            CreditSetting::query()->updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->assignRole('user');
        app(CreditService::class)->grant($user, 100, 'Registration Bonus');
    }
}
