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
        if (Schema::hasTable('kits') && ! Schema::hasColumn('kits', 'status')) {
            Schema::table('kits', function (Blueprint $table) {
                $table->enum('status', [
                    'available',
                    'staged',
                    'in_transit',
                    'delivered',
                    'pending_cleaning',
                ])->default('available');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kits') && Schema::hasColumn('kits', 'status')) {
            Schema::table('kits', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
