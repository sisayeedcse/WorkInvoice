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
        Schema::table('items', function (Blueprint $table) {
            // Product type classification
            $table->enum('item_type', ['trading', 'manufactured', 'raw_material', 'service'])
                  ->default('trading')
                  ->after('category');

            // Inventory tracking
            $table->boolean('track_inventory')->default(true)->after('item_type');
            $table->decimal('stock_quantity', 12, 3)->default(0)->after('track_inventory');
            $table->decimal('reorder_level', 12, 3)->nullable()->after('stock_quantity');
            $table->decimal('cost_price', 12, 2)->nullable()->after('reorder_level');
            $table->string('sku', 50)->unique()->nullable()->after('name');

            // Indexes for performance
            $table->index(['item_type', 'is_active']);
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex(['item_type', 'is_active']);
            $table->dropIndex(['stock_quantity']);
            $table->dropColumn([
                'item_type',
                'track_inventory',
                'stock_quantity',
                'reorder_level',
                'cost_price',
                'sku'
            ]);
        });
    }
};
