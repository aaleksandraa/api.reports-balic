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
        Schema::create('planned_procedures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('patient_first_name');
            $table->string('patient_last_name');
            $table->enum('procedure_type', [
                'HSC operacija',
                'Vantjelesna oplodnja',
                'Inseminacija',
                'Pregled za inseminaciju',
                'Kandidat za operaciju',
                'Ostalo'
            ]);
            $table->string('procedure_details')->nullable(); // Dodatni detalji o proceduri
            $table->date('planned_date')->nullable(); // Tačan datum ako je poznat
            $table->string('planned_month')->nullable(); // Mjesec ako tačan datum nije poznat (npr. "April 2026")
            $table->text('notes')->nullable(); // Dodatne napomene
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_procedures');
    }
};
