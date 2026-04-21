<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('docs', function (Blueprint $table) {
            if (!Schema::hasColumn('docs', 'category_order')) {
                $table->integer('category_order')->default(0)->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('docs', function (Blueprint $table) {
            if (Schema::hasColumn('docs', 'category_order')) {
                $table->dropColumn('category_order');
            }
        });
    }
};
