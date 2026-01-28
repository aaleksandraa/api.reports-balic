<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add work norms to users table (for employees)
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('daily_work_hours', 4, 2)->nullable()->after('job_title')->comment('Expected daily work hours');
            $table->decimal('weekly_work_hours', 5, 2)->nullable()->after('daily_work_hours')->comment('Expected weekly work hours');
            $table->decimal('monthly_work_hours', 6, 2)->nullable()->after('weekly_work_hours')->comment('Expected monthly work hours');
        });

        // Add status to work_schedule table
        Schema::table('work_schedule', function (Blueprint $table) {
            $table->string('status')->default('present')->after('hours_worked')->comment('present, vacation, sick_leave, absent');
            $table->text('notes')->nullable()->after('status')->comment('Additional notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_work_hours', 'weekly_work_hours', 'monthly_work_hours']);
        });

        Schema::table('work_schedule', function (Blueprint $table) {
            $table->dropColumn(['status', 'notes']);
        });
    }
};
