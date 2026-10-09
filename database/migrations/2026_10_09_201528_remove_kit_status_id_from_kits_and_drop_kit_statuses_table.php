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
        if (Schema::hasTable('kits') && Schema::hasColumn('kits', 'kit_status_id')) {
            Schema::table('kits', function (Blueprint $table) {
                $table->dropForeign(['kit_status_id']);
                $table->dropColumn('kit_status_id');
            });
        }

        Schema::dropIfExists('kit_statuses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('kit_statuses')) {
            Schema::create('kit_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('kits') && ! Schema::hasColumn('kits', 'kit_status_id')) {
            Schema::table('kits', function (Blueprint $table) {
                $table->foreignId('kit_status_id')
                    ->nullable()
                    ->constrained('kit_statuses');
            });
        }
    }
};
