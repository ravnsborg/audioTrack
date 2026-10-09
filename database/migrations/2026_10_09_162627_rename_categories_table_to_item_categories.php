<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('categories') && ! Schema::hasTable('item_categories')) {
            Schema::rename('categories', 'item_categories');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('item_categories') && ! Schema::hasTable('categories')) {
            Schema::rename('item_categories', 'categories');
        }
    }
};
