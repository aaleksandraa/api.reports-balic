<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('location_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('day_of_week');
            $table->text('notes')->nullable();
            $table->foreignUuid('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'archived'])->default('draft');
            $table->timestamps();

            $table->unique(['location_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
