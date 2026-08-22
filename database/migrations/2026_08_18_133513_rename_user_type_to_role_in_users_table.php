<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run the ALTER query if user_type still exists
        if (Schema::hasColumn('users', 'user_type') && !Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users CHANGE user_type role ENUM('admin', 'member') NOT NULL DEFAULT 'member'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role') && !Schema::hasColumn('users', 'user_type')) {
            DB::statement("ALTER TABLE users CHANGE role user_type ENUM('admin', 'member') NOT NULL DEFAULT 'member'");
        }
    }
};
