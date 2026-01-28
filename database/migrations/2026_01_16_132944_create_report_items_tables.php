<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fiscal Items
        Schema::create('fiscal_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('doctor_counts')->nullable();
            $table->timestamps();
        });

        // Non-Fiscal Items
        Schema::create('non_fiscal_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('doctor_counts')->nullable();
            $table->timestamps();
        });

        // Card Payments
        Schema::create('card_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('service_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('doctor_counts')->nullable();
            $table->timestamps();
        });

        // Wire Transfers
        Schema::create('wire_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('patient_name');
            $table->decimal('price', 10, 2)->default(0);
            $table->json('doctor_counts')->nullable();
            $table->timestamps();
        });

        // Associates
        Schema::create('associates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('service_name');
            $table->foreignUuid('doctor_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('count')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('type', ['fiscal', 'non-fiscal'])->default('fiscal');
            $table->timestamps();
        });

        // Patients
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('full_name');
            $table->string('city')->nullable();
            $table->text('reason')->nullable();
            $table->foreignUuid('doctor_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Work Schedule
        Schema::create('work_schedule', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('employee_name');
            $table->time('arrival_time')->nullable();
            $table->time('departure_time')->nullable();
            $table->decimal('hours_worked', 4, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedule');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('associates');
        Schema::dropIfExists('wire_transfers');
        Schema::dropIfExists('card_payments');
        Schema::dropIfExists('non_fiscal_items');
        Schema::dropIfExists('fiscal_items');
    }
};
