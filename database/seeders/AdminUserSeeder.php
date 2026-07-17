<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Services\CreditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public const ACCOUNTS = [
        [
            'name' => 'NPNHCREATIVE Admin',
            'email' => 'admin@npnhcreative.test',
            'password' => 'Admin123!',
            'role' => 'admin',
            'credits' => 1000,
        ],
    ];

    public function run(): void
    {
        $creditService = app(CreditService::class);

        foreach (self::ACCOUNTS as $account) {
            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make(env('SEEDED_ADMIN_PASSWORD', $account['password'])),
                    'email_verified_at' => now(),
                    'provider' => 'local',
                    'role' => $account['role'],
                    'status' => 'active',
                ]
            );

            $user->syncRoles([$account['role']]);

            if ($user->credits_balance < $account['credits']) {
                $creditService->grant(
                    user: $user,
                    amount: $account['credits'] - $user->credits_balance,
                    action: 'Admin Seed Credits',
                    metadata: ['seeded_account' => true]
                );
            }
        }
    }
}
