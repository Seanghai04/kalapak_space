<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Safely create docs table if it was never created in production
        if (!Schema::hasTable('docs')) {
            Schema::create('docs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->string('category')->default('General');
                $table->integer('order_num')->default(0);
                $table->enum('status', ['draft', 'published'])->default('draft');
                $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('slug');
                $table->index('status');
                $table->index(['category', 'order_num']);
                $table->index('parent_id');
            });

            Schema::table('docs', function (Blueprint $table) {
                $table->foreign('parent_id')->references('id')->on('docs')->nullOnDelete();
            });
        }

        // Safely create doc_sections table if it was never created in production
        if (!Schema::hasTable('doc_sections')) {
            Schema::create('doc_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doc_id')->constrained('docs')->cascadeOnDelete();
                $table->string('heading');
                $table->longText('content')->nullable();
                $table->integer('order_num')->default(0);
                $table->timestamps();

                $table->index(['doc_id', 'order_num']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_sections');

        if (Schema::hasTable('docs')) {
            Schema::table('docs', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
            });
        }

        Schema::dropIfExists('docs');
    }
};
