<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'converter.upload',
            'converter.convert',
            'converter.download.own',
            'converter.delete.own',
            'admin.access',
            'admin.users.manage',
            'admin.conversions.manage',
            'admin.queue.manage',
            'admin.analytics.view',
            'admin.settings.manage',
            'admin.credits.manage',
            'admin.activity.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('user')->syncPermissions([
            'converter.upload',
            'converter.convert',
            'converter.download.own',
            'converter.delete.own',
        ]);

        Role::findOrCreate('admin')->syncPermissions($permissions);
    }
}
