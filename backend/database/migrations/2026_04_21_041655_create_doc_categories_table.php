<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doc_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        // Seed from existing categories on docs
        $existing = \Illuminate\Support\Facades\DB::table('docs')
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        foreach ($existing as $cat) {
            \Illuminate\Support\Facades\DB::table('doc_categories')->insertOrIgnore(['name' => $cat, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_categories');
    }
};
