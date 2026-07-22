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
        Schema::create('full_day_transfer_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->integer('hours_limit')->default(8);
            $table->integer('distance_limit')->default(100);
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('extra_hour_charge', 10, 2)->default(0);
            $table->decimal('extra_km_charge', 10, 2)->default(0);
            $table->string('currency', 10)->default('AED');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('full_day_transfer_rates');
    }
};
