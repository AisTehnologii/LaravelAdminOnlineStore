<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ChatPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'chat.access',
            'chat.message',
            'chat.create_group',
            'chat.manage_participants',
            'chat.view_all',
        ];

        foreach ($perms as $p) {
            Permission::findOrCreate($p);
        }

        // Подстрой под свои роли
        if ($role = Role::where('name', 'content-maker')->first()) {
            $role->givePermissionTo(['chat.access', 'chat.message']);
        }

        if ($role = Role::where('name', 'admin')->first()) {
            $role->givePermissionTo($perms);
        }

        // SUPER_ADMIN у тебя через Gate::before уже “всё может”
    }
}
