<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->comment('create|update|delete');
            $table->string('subject_type')->comment('App\Models\Project, App\Models\Tag, etc.');
            $table->unsignedBigInteger('subject_id')->nullable()->comment('null for create actions');
            $table->json('payload')->comment('The data to apply when approved');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'subject_type']);
            $table->index('requested_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};
