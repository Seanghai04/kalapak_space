<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'allowed_storage_providers'],
            [
                'key' => 'allowed_storage_providers',
                'value' => 'both',
                'type' => 'string',
                'group' => 'storage',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'allowed_storage_providers')->delete();
    }
};
