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
        Schema::table('full_day_transfer_rates', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'location_id',
                'hours_limit',
                'distance_limit',
                'extra_hour_charge',
                'extra_km_charge',
            ]);

            $table->foreignId('from_location_id')->after('id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->foreignId('to_location_id')->after('from_location_id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->enum('fare_type', ['half_day', 'full_day'])->default('full_day')->after('vehicle_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('full_day_transfer_rates', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id', 'fare_type']);

            $table->foreignId('location_id')->after('id')->constrained('transfer_locations')->cascadeOnDelete();
            $table->integer('hours_limit')->default(8);
            $table->integer('distance_limit')->default(100);
            $table->decimal('extra_hour_charge', 10, 2)->default(0);
            $table->decimal('extra_km_charge', 10, 2)->default(0);
        });
    }
};
