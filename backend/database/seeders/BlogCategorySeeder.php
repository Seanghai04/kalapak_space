<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'description' => 'Step-by-step guides and tutorials', 'color' => '#7b2fff'],
            ['name' => 'Development', 'slug' => 'development', 'description' => 'Development tips and best practices', 'color' => '#00d4ff'],
            ['name' => 'Announcement', 'slug' => 'announcement', 'description' => 'Team updates and announcements', 'color' => '#00ff88'],
        ];

        foreach ($categories as $cat) {
            BlogCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
