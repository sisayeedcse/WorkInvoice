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
        // Add stock receiving fields to purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->boolean('stock_received')->default(false)->after('status');
            $table->timestamp('received_at')->nullable()->after('stock_received');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete()->after('received_at');
        });

        // Add item_id link and received_quantity to purchase_order_items
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete()->after('id');
            $table->decimal('received_quantity', 12, 3)->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropColumn(['item_id', 'received_quantity']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn(['stock_received', 'received_at', 'received_by']);
        });
    }
};
