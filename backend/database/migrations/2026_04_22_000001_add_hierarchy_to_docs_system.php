<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add parent_id to doc_menus (Main Menu → Sub-Menu hierarchy)
        Schema::table('doc_menus', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('doc_menus')->nullOnDelete();
        });

        // 2. Add doc_menu_id to docs (replaces loose category string with proper FK)
        Schema::table('docs', function (Blueprint $table) {
            $table->unsignedBigInteger('doc_menu_id')->nullable()->after('parent_id');
            $table->foreign('doc_menu_id')->references('id')->on('doc_menus')->nullOnDelete();
        });

        // 3. Migrate existing data: match docs.category → doc_menus.name → doc_menu_id
        $menus = DB::table('doc_menus')->get(['id', 'name']);
        foreach ($menus as $menu) {
            DB::table('docs')
                ->where('category', $menu->name)
                ->update(['doc_menu_id' => $menu->id]);
        }
    }

    public function down(): void
    {
        Schema::table('docs', function (Blueprint $table) {
            $table->dropForeign(['doc_menu_id']);
            $table->dropColumn('doc_menu_id');
        });

        Schema::table('doc_menus', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
