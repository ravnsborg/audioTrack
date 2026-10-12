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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained();
            $table->foreignId('client_location_id')->constrained();
            $table->foreignId('kit_id')->constrained();
            $table->enum('status', [
                'staged',
                'in_transit',
                'delivered',
                'completed',
            ])->default('staged');
            $table->unsignedInteger('queue_position')->default(0);
            $table->string('token', 64)->unique();
            $table->timestamp('delivery_datetime')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'queue_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
