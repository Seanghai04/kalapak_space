<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TeamMemberSeeder::class,
            BlogCategorySeeder::class,
            TagSeeder::class,
            ProjectSeeder::class,
            BlogPostSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
