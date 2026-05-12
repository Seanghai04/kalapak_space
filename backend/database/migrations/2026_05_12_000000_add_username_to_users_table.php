<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 30)->nullable()->unique()->after('name');
            });
        }

        User::query()->whereNull('username')->each(function (User $user) {
            $fromEmail = Str::before((string) $user->email, '@') ?: 'user';
            $user->username = User::generateUniqueUsername($fromEmail);
            $user->saveQuietly();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'pgsql') {
            $row = DB::selectOne(
                "select is_nullable from information_schema.columns where table_schema = current_schema() and table_name = 'users' and column_name = 'username'"
            );
            if ($row && $row->is_nullable === 'YES') {
                DB::statement('ALTER TABLE users ALTER COLUMN username SET NOT NULL');
            }
        } elseif ($driver === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY username VARCHAR(30) NOT NULL');
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'username')) {
            return;
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
