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
        if (Schema::hasColumn('items', 'items_status_id')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropForeign(['items_status_id']);
                $table->dropColumn('items_status_id');
            });
        }

        Schema::dropIfExists('item_statuses');
    }

    /**
     * Reverse the migrations.
     *
     * The removed item-status assignments cannot be restored.
     */
    public function down(): void
    {
        if (! Schema::hasTable('item_statuses')) {
            Schema::create('item_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('items') && ! Schema::hasColumn('items', 'items_status_id')) {
            Schema::table('items', function (Blueprint $table) {
                $table->foreignId('items_status_id')
                    ->nullable()
                    ->constrained('item_statuses');
            });
        }
    }
};
