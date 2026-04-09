<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('long_description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->json('tech_stack')->nullable();
            $table->enum('status', ['active', 'archived', 'wip'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_open_source')->default(true);
            $table->integer('stars_count')->default(0);
            $table->integer('forks_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
