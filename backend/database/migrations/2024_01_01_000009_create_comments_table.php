<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('body');
            $table->boolean('is_approved')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->timestamps();

            $table->index('blog_post_id');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
