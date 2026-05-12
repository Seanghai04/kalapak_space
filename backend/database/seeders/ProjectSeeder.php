<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $creators = User::query()->where('is_active', true)->orderBy('id')->pluck('id');
        if ($creators->isEmpty()) {
            return;
        }
        $creatorIds = $creators->all();

        $projects = [
            [
                'title' => 'Kalapak Website',
                'slug' => 'kalapak-website',
                'description' => 'Official Kalapak Code Team website built with Vue 3 and Laravel 11.',
                'long_description' => "# Kalapak Website\n\nA full-stack website showcasing the team's projects, blog, and mission.\n\n## Tech Stack\n- Vue 3 + Vite\n- Laravel 11\n- PostgreSQL 16\n- Docker\n- Tailwind CSS",
                'tech_stack' => ['Vue.js', 'Laravel', 'PostgreSQL', 'Docker', 'Tailwind CSS'],
                'status' => 'active',
                'is_featured' => true,
                'is_open_source' => true,
                'stars_count' => 45,
                'forks_count' => 12,
                'repo_url' => 'https://github.com/Kalapak-Team/kalapak-website',
                'demo_url' => 'https://kalapak.dev',
                'tags' => ['vuejs', 'laravel', 'postgresql', 'docker', 'tailwindcss'],
            ],
            [
                'title' => 'Khmer NLP Toolkit',
                'slug' => 'khmer-nlp-toolkit',
                'description' => 'Natural Language Processing tools for Khmer language.',
                'tech_stack' => ['Python', 'TensorFlow', 'FastAPI'],
                'status' => 'active',
                'is_featured' => true,
                'is_open_source' => true,
                'stars_count' => 128,
                'forks_count' => 34,
                'repo_url' => 'https://github.com/Kalapak-Team/khmer-nlp',
                'tags' => ['python', 'ai-ml'],
            ],
            [
                'title' => 'CamPay Mobile',
                'slug' => 'campay-mobile',
                'description' => 'Mobile payment solution for Cambodia built with Flutter.',
                'tech_stack' => ['Flutter', 'Dart', 'Firebase'],
                'status' => 'active',
                'is_featured' => true,
                'is_open_source' => true,
                'stars_count' => 67,
                'forks_count' => 15,
                'repo_url' => 'https://github.com/Kalapak-Team/campay-mobile',
                'tags' => ['flutter'],
            ],
            [
                'title' => 'DevOps Pipeline Template',
                'slug' => 'devops-pipeline-template',
                'description' => 'Ready-to-use CI/CD pipeline templates for common frameworks.',
                'tech_stack' => ['Docker', 'GitHub Actions', 'Kubernetes'],
                'status' => 'active',
                'is_featured' => false,
                'is_open_source' => true,
                'stars_count' => 23,
                'forks_count' => 8,
                'repo_url' => 'https://github.com/Kalapak-Team/devops-templates',
                'tags' => ['docker'],
            ],
            [
                'title' => 'React Dashboard Pro',
                'slug' => 'react-dashboard-pro',
                'description' => 'Professional admin dashboard template built with React and TypeScript.',
                'tech_stack' => ['React', 'TypeScript', 'Tailwind CSS'],
                'status' => 'active',
                'is_featured' => false,
                'is_open_source' => true,
                'stars_count' => 56,
                'forks_count' => 20,
                'repo_url' => 'https://github.com/Kalapak-Team/react-dashboard',
                'demo_url' => 'https://dashboard.kalapak.dev',
                'tags' => ['react', 'typescript', 'tailwindcss'],
            ],
            [
                'title' => 'Laravel API Starter',
                'slug' => 'laravel-api-starter',
                'description' => 'Boilerplate for building REST APIs with Laravel, Sanctum, and PostgreSQL.',
                'tech_stack' => ['Laravel', 'PostgreSQL', 'Redis'],
                'status' => 'active',
                'is_featured' => false,
                'is_open_source' => true,
                'stars_count' => 89,
                'forks_count' => 25,
                'repo_url' => 'https://github.com/Kalapak-Team/laravel-api-starter',
                'tags' => ['laravel', 'postgresql'],
            ],
            [
                'title' => 'Smart Classroom IoT',
                'slug' => 'smart-classroom-iot',
                'description' => 'IoT system for smart classroom management using Python and sensors.',
                'tech_stack' => ['Python', 'Raspberry Pi', 'MQTT'],
                'status' => 'wip',
                'is_featured' => false,
                'is_open_source' => true,
                'stars_count' => 12,
                'forks_count' => 3,
                'repo_url' => 'https://github.com/Kalapak-Team/smart-classroom',
                'tags' => ['python', 'ai-ml'],
            ],
            [
                'title' => 'Khmer Font Collection',
                'slug' => 'khmer-font-collection',
                'description' => 'Curated collection of open source Khmer fonts for web and print.',
                'tech_stack' => ['CSS', 'Web Fonts'],
                'status' => 'archived',
                'is_featured' => false,
                'is_open_source' => true,
                'stars_count' => 34,
                'forks_count' => 10,
                'repo_url' => 'https://github.com/Kalapak-Team/khmer-fonts',
                'tags' => ['tailwindcss'],
            ],
        ];

        $i = 0;
        foreach ($projects as $projectData) {
            $tagSlugs = $projectData['tags'] ?? [];
            unset($projectData['tags']);

            $projectData['created_by'] = $creatorIds[$i % count($creatorIds)];
            $i++;
            $project = Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );

            if ($tagSlugs) {
                $tagIds = Tag::whereIn('slug', $tagSlugs)->pluck('id');
                $project->tags()->sync($tagIds);
            }
        }
    }
}
