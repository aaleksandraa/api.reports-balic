<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_locations', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['user_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_locations');
    }
};
