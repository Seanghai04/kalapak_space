<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@kalapak.dev')->first();
        $tutorial = BlogCategory::where('slug', 'tutorial')->first();
        $development = BlogCategory::where('slug', 'development')->first();
        $announcement = BlogCategory::where('slug', 'announcement')->first();

        $posts = [
            [
                'title' => 'Getting Started with Vue 3 Composition API',
                'slug' => 'getting-started-vue3-composition-api',
                'excerpt' => 'Learn how to use Vue 3 Composition API to build modern, reactive web applications.',
                'content' => "# Getting Started with Vue 3 Composition API\n\nThe Composition API is one of the most exciting features in Vue 3. It provides a more flexible way to organize component logic.\n\n## Why Composition API?\n\n- Better TypeScript support\n- More reusable logic through composables\n- Cleaner code organization for complex components\n\n## Basic Example\n\n```javascript\nimport { ref, computed, onMounted } from 'vue'\n\nexport default {\n  setup() {\n    const count = ref(0)\n    const doubled = computed(() => count.value * 2)\n\n    function increment() {\n      count.value++\n    }\n\n    onMounted(() => {\n      console.log('Component mounted!')\n    })\n\n    return { count, doubled, increment }\n  }\n}\n```\n\n## Conclusion\n\nThe Composition API gives you more control and flexibility in how you structure your Vue components.",
                'category_id' => $tutorial->id,
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 342,
                'reading_time' => 8,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Building REST APIs with Laravel 11',
                'slug' => 'building-rest-apis-laravel-11',
                'excerpt' => 'A comprehensive guide to building robust REST APIs using Laravel 11 and Sanctum authentication.',
                'content' => "# Building REST APIs with Laravel 11\n\nLaravel 11 brings a simplified application structure perfect for API development.\n\n## Setting Up\n\n```bash\nlaravel new api-project\ncd api-project\nphp artisan install:api\n```\n\n## API Routes\n\nDefine your routes in `routes/api.php`:\n\n```php\nRoute::apiResource('posts', PostController::class);\n```\n\n## Authentication with Sanctum\n\nLaravel Sanctum provides a lightweight authentication system perfect for SPAs.\n\n## Conclusion\n\nLaravel 11 makes building APIs easier than ever.",
                'category_id' => $tutorial->id,
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 256,
                'reading_time' => 12,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Docker for Full-Stack Development',
                'slug' => 'docker-fullstack-development',
                'excerpt' => 'How to containerize your full-stack application using Docker and Docker Compose.',
                'content' => "# Docker for Full-Stack Development\n\nDocker simplifies development by ensuring consistent environments across the team.\n\n## docker-compose.yml\n\n```yaml\nversion: '3.8'\nservices:\n  app:\n    build: .\n    ports:\n      - '3000:3000'\n  db:\n    image: postgres:16-alpine\n```\n\n## Benefits\n\n- Consistent development environments\n- Easy onboarding for new team members\n- Production-like local setup",
                'category_id' => $development->id,
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 189,
                'reading_time' => 6,
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Kalapak Code Team Launch Announcement',
                'slug' => 'kalapak-code-team-launch',
                'excerpt' => 'We are excited to announce the official launch of Kalapak Code Team!',
                'content' => "# Kalapak Code Team Launch 🚀\n\nWe are thrilled to announce the launch of **Kalapak Code Team**, a collective of passionate Cambodian developers committed to building impactful open-source projects.\n\n## Our Mission\n\nResearch. Document. Develop. Showcase. Help Others.\n\n## What's Next?\n\n- More open-source projects\n- Developer tutorials and blog content\n- Community building\n\nJoin us on this exciting journey!",
                'category_id' => $announcement->id,
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 523,
                'reading_time' => 3,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Tailwind CSS Tips and Tricks',
                'slug' => 'tailwind-css-tips-tricks',
                'excerpt' => 'Discover advanced Tailwind CSS techniques for building beautiful UIs faster.',
                'content' => "# Tailwind CSS Tips and Tricks\n\n## Custom Gradients\n\n```html\n<div class=\"bg-gradient-to-r from-purple-600 to-cyan-400\">\n  Gradient Background\n</div>\n```\n\n## Glassmorphism Effect\n\n```css\n.glass {\n  background: rgba(255, 255, 255, 0.1);\n  backdrop-filter: blur(16px);\n  border: 1px solid rgba(255, 255, 255, 0.2);\n}\n```\n\n## Dark Mode\n\nUse the `dark:` prefix for easy dark mode support.",
                'category_id' => $tutorial->id,
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 178,
                'reading_time' => 5,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Introduction to PostgreSQL 16',
                'slug' => 'introduction-postgresql-16',
                'excerpt' => 'Explore the new features in PostgreSQL 16 and why it should be your next database choice.',
                'content' => "# Introduction to PostgreSQL 16\n\nPostgreSQL 16 introduces several performance improvements and new features.\n\n## Key Features\n\n- Improved query parallelism\n- Better logical replication\n- Enhanced monitoring capabilities\n\n## Getting Started\n\n```sql\nCREATE DATABASE myapp;\nCREATE USER myuser WITH PASSWORD 'secret';\nGRANT ALL PRIVILEGES ON DATABASE myapp TO myuser;\n```",
                'category_id' => $development->id,
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 145,
                'reading_time' => 7,
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Flutter Mobile App Development Guide',
                'slug' => 'flutter-mobile-development-guide',
                'excerpt' => 'A beginner-friendly guide to building cross-platform mobile apps with Flutter.',
                'content' => "# Flutter Mobile App Development\n\nFlutter enables you to build beautiful, natively compiled apps from a single codebase.\n\n## Getting Started\n\n```dart\nimport 'package:flutter/material.dart';\n\nvoid main() => runApp(MyApp());\n\nclass MyApp extends StatelessWidget {\n  @override\n  Widget build(BuildContext context) {\n    return MaterialApp(\n      home: Scaffold(\n        appBar: AppBar(title: Text('Kalapak App')),\n      ),\n    );\n  }\n}\n```",
                'category_id' => $tutorial->id,
                'status' => 'draft',
                'is_featured' => false,
                'views_count' => 0,
                'reading_time' => 10,
                'published_at' => null,
            ],
            [
                'title' => 'Open Source Contribution Guide',
                'slug' => 'open-source-contribution-guide',
                'excerpt' => 'How to contribute to open source projects and grow as a developer.',
                'content' => "# Open Source Contribution Guide\n\nContributing to open source is one of the best ways to improve your skills.\n\n## Steps\n\n1. Find a project you're interested in\n2. Read the contribution guidelines\n3. Fork and clone the repository\n4. Create a feature branch\n5. Make your changes\n6. Submit a pull request\n\n## Tips\n\n- Start with small issues labeled 'good first issue'\n- Be respectful and patient\n- Write good commit messages",
                'category_id' => $development->id,
                'status' => 'draft',
                'is_featured' => false,
                'views_count' => 0,
                'reading_time' => 5,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $post) {
            $post['author_id'] = $admin->id;
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
