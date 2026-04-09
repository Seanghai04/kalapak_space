<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'color' => '#42b883'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'color' => '#ff2d20'],
            ['name' => 'Python', 'slug' => 'python', 'color' => '#3776ab'],
            ['name' => 'Flutter', 'slug' => 'flutter', 'color' => '#02569b'],
            ['name' => 'React', 'slug' => 'react', 'color' => '#61dafb'],
            ['name' => 'Docker', 'slug' => 'docker', 'color' => '#2496ed'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'color' => '#336791'],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'color' => '#3178c6'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss', 'color' => '#38bdf8'],
            ['name' => 'AI/ML', 'slug' => 'ai-ml', 'color' => '#ff6f00'],
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
