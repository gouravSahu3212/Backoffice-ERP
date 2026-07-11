<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_transfer_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_location_id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->foreignId('to_location_id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->enum('fare_type', ['fixed', 'per_km', 'per_hr'])->default('fixed');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('AED');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_transfer_rates');
    }
};
