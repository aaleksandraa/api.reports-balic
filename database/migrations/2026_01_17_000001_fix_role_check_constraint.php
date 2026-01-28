<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Drop the old check constraint
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

            // Add new check constraint that allows all three roles
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff', 'radnik'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Drop the new check constraint
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");

            // Add back the old check constraint
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('admin', 'staff'))");
        }
    }
};
