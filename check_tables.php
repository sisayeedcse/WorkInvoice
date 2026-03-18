<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['customers', 'quotations', 'orders', 'invoices', 'purchase_orders'];

foreach ($tables as $table) {
    echo "\n=== Table: {$table} ===\n";
    $columns = Schema::getColumnListing($table);
    
    if (in_array('deleted_at', $columns)) {
        echo "✓ deleted_at column exists\n";
    } else {
        echo "✗ deleted_at column MISSING!\n";
    }
    
    echo "All columns: " . implode(', ', $columns) . "\n";
}
