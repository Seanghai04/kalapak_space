<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('github_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('role_applied');
            $table->text('motivation');
            $table->text('skills');
            $table->string('portfolio_url')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'accepted', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
