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
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('pickup_lat', 10, 8)->after('pickup_location');
            $table->decimal('pickup_lng', 11, 8)->after('pickup_lat');
            $table->decimal('dropoff_lat', 10, 8)->after('dropoff_location');
            $table->decimal('dropoff_lng', 11, 8)->after('dropoff_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_lat',
                'pickup_lng',
                'dropoff_lat',
                'dropoff_lng'
            ]);
        });
    }
};
