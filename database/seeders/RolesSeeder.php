<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $contentMaker = Role::firstOrCreate(['name' => 'content-maker']);

        $all = Permission::query()->pluck('name')->toArray();

        // admin (не is_admin) — полный доступ по permissions
        $admin->syncPermissions($all);

        // content-maker — все кроме удаления
        $contentMaker->syncPermissions(array_values(array_filter($all, fn ($p) => !str_contains($p, 'delete'))));
    }
}
