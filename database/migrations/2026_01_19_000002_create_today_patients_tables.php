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
        // Quick entries - samo broj po usluzi
        Schema::create('today_patients_quick', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services')->onDelete('cascade');
            $table->string('service_name'); // Denormalized za brži pristup
            $table->integer('count')->default(1);
            $table->timestamps();
        });

        // Detailed entries - puno ime, prezime, usluga, napomena
        Schema::create('today_patients_detailed', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->foreignUuid('service_id')->constrained('services')->onDelete('cascade');
            $table->string('service_name'); // Denormalized za brži pristup
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('today_patients_detailed');
        Schema::dropIfExists('today_patients_quick');
    }
};
