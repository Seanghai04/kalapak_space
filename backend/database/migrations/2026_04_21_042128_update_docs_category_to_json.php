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
        Schema::table('docs', function (Blueprint $table) {
            $table->jsonb('categories')->nullable()->after('category');
        });

        // Seed categories from existing string category column
        \Illuminate\Support\Facades\DB::statement("
            UPDATE docs SET categories = jsonb_build_array(category) WHERE category IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('docs', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
