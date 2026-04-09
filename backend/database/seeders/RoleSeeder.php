<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access to all features'],
            ['name' => 'member', 'display_name' => 'Member', 'description' => 'Team member with profile access'],
            ['name' => 'guest', 'display_name' => 'Guest', 'description' => 'Registered user with basic access'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
