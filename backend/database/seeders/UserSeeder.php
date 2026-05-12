<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $memberRole = Role::where('name', 'member')->first();

        User::updateOrCreate(
            ['email' => 'admin@kalapak.dev'],
            [
                'name' => 'Khat Vanna',
                'username' => 'khat_vanna',
                'password' => 'password',
                'role_id' => $adminRole->id,
                'bio' => 'Founder & Full-Stack Developer at Kalapak Code Team',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'member@kalapak.dev'],
            [
                'name' => 'Rom Chamraeun',
                'username' => 'rom_chamraeun',
                'password' => 'password',
                'role_id' => $memberRole->id,
                'bio' => 'Co-Founder & Full-Stack Developer',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'guest@kalapak.dev'],
            [
                'name' => 'Test Guest',
                'username' => 'test_guest',
                'password' => 'password',
                'role_id' => $memberRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'norng@kalapak.dev'],
            [
                'name' => 'Phuem Norng',
                'username' => 'phuem_norng',
                'password' => 'password',
                'role_id' => $memberRole->id,
                'bio' => 'Co-Founder & Full-Stack Developer',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'seanghai@kalapak.dev'],
            [
                'name' => 'Pheun Seanghai',
                'username' => 'pheun_seanghai',
                'password' => 'password',
                'role_id' => $memberRole->id,
                'bio' => 'Co-Founder & Full-Stack Developer',
                'is_active' => true,
            ]
        );
    }
}
