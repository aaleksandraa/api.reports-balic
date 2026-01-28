<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_locations', function (Blueprint $table) {
            $table->foreignUuid('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['doctor_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_locations');
    }
};
