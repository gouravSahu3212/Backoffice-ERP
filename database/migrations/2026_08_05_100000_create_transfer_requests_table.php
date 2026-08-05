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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_reference')->unique();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->string('transfer_type_category')->default('city'); // city, airport, fullday
            $table->foreignId('city_rate_id')->nullable()->constrained('city_transfer_rates')->nullOnDelete();
            $table->foreignId('airport_rate_id')->nullable()->constrained('airport_transfer_rates')->nullOnDelete();
            $table->foreignId('full_day_rate_id')->nullable()->constrained('full_day_transfer_rates')->nullOnDelete();
            $table->string('title');
            $table->string('route_label');
            $table->string('vehicle')->nullable();
            $table->string('customer_name');
            $table->date('date_of_birth');
            $table->string('passport_number');
            $table->date('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('currency', 10)->default('AED');
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
