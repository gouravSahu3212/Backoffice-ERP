<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->unsignedSmallInteger('days')->default(1);
            $table->unsignedTinyInteger('hotel_rating')->nullable()->comment('1-5 stars');
            $table->string('currency', 10)->default('USD');
            $table->decimal('retail_price', 10, 2)->default(0);
            $table->decimal('agent_price', 10, 2)->default(0);
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->longText('itinerary')->nullable();
            $table->string('itinerary_pdf')->nullable();
            $table->json('highlights')->nullable();
            $table->json('whats_included')->nullable();
            $table->json('image_urls')->nullable();
            $table->json('departure_months')->nullable();
            $table->unsignedSmallInteger('max_capacity')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
