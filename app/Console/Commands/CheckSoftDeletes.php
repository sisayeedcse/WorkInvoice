<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckSoftDeletes extends Command
{
    protected $signature = 'check:soft-deletes';
    protected $description = 'Check if soft delete columns exist in tables';

    public function handle()
    {
        $tables = ['customers', 'quotations', 'orders', 'invoices', 'purchase_orders', 'items'];
        
        $this->info("\n=== Checking Soft Delete Columns ===\n");
        
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->error("✗ Table '{$table}' does not exist!");
                continue;
            }
            
            if (Schema::hasColumn($table, 'deleted_at')) {
                $this->info("✓ {$table}: deleted_at column EXISTS");
                
                // Count soft-deleted records
                $count = DB::table($table)->whereNotNull('deleted_at')->count();
                if ($count > 0) {
                    $this->line("  └─ {$count} soft-deleted records");
                }
            } else {
                $this->error("✗ {$table}: deleted_at column MISSING!");
            }
        }
        
        $this->info("\n=== Testing Delete Functionality ===\n");
        
        // Get first customer
        $customer = DB::table('customers')->whereNull('deleted_at')->first();
        
        if (!$customer) {
            $this->warn("No available customers to test");
            return;
        }
        
        $this->line("Testing with Customer ID: {$customer->id}");
        $this->line("Before delete - deleted_at: " . ($customer->deleted_at ?? 'NULL'));
        
        return Command::SUCCESS;
    }
}

        // Now test using the model's delete method
        try {
            $model = \App\Models\Customer::find($customer->id);
            $this->line("Found model: " . $model->name);
            
            $deleteResult = $model->delete();
            $this->line("Delete method returned: " . ($deleteResult ? 'true' : 'false'));
            
            // Check if deleted_at was set
            $afterDelete = DB::table('customers')->where('id', $customer->id)->first();
            $this->line("After delete - deleted_at: " . ($afterDelete->deleted_at ?? 'NULL'));
            
            if ($afterDelete->deleted_at) {
                $this->info("✓ Soft delete is working correctly!");
            } else {
                $this->error("✗ Soft delete did NOT set deleted_at!");
            }
        } catch (\Exception $e) {
            $this->error("Error during delete test: " . $e->getMessage());
        }
        
        return Command::SUCCESS;
