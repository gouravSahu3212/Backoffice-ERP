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
        Schema::table('hotel_room_slots', function (Blueprint $table) {
            $table->string('month')->nullable()->after('currency');
            $table->string('season_type')->nullable()->after('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_room_slots', function (Blueprint $table) {
            $table->dropColumn(['season_type', 'month']);
        });
    }
};
