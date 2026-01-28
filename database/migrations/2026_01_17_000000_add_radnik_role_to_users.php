<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Convert any 'employee' values to 'staff'
            DB::statement("UPDATE users SET role = 'staff' WHERE role = 'employee'");

            // Drop default
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");

            // Create new enum type with all three values
            DB::statement("CREATE TYPE role_enum_new AS ENUM('admin', 'staff', 'radnik')");

            // Convert column type
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE role_enum_new USING role::text::role_enum_new");

            // Set default back
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'staff'::role_enum_new");

            // Drop old type if it exists and rename new one
            DB::statement("DROP TYPE IF EXISTS role_enum CASCADE");
            DB::statement("ALTER TYPE role_enum_new RENAME TO role_enum");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'staff', 'radnik') DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role DROP DEFAULT");
            DB::statement("CREATE TYPE role_enum_new AS ENUM('admin', 'staff')");
            DB::statement("UPDATE users SET role = 'staff' WHERE role = 'radnik'");
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE role_enum_new USING role::text::role_enum_new");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'staff'::role_enum_new");
            DB::statement("DROP TYPE IF EXISTS role_enum CASCADE");
            DB::statement("ALTER TYPE role_enum_new RENAME TO role_enum");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'staff') DEFAULT 'staff'");
        }
    }
};
