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
            $table->foreignId('vehicle_model_id')->nullable()->after('vehicle_type_id')->constrained('vehicle_models')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('full_day_transfer_rates', function (Blueprint $table) {
            $table->dropForeign(['vehicle_model_id']);
            $table->dropColumn('vehicle_model_id');
        });
    }
};
