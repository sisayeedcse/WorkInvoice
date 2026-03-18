<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function testDelete()
    {
        // Get total customers (including soft-deleted)
        $allCount = Customer::withTrashed()->count();
        
        // Get visible customers (excluding soft-deleted)
        $visibleCount = Customer::whereNull('deleted_at')->count();
        
        // Get soft-deleted
        $deletedCount = Customer::onlyTrashed()->count();
        
        // Try to delete first customer
        $customer = Customer::withTrashed()->first();
        
        return response()->json([
            'message' => 'Database deletion test',
            'total_customers' => $allCount,
            'visible_customers' => $visibleCount,
            'deleted_customers' => $deletedCount,
            'first_customer' => $customer ? [
                'id' => $customer->id,
                'name' => $customer->name,
                'deleted_at' => $customer->deleted_at,
            ] : null,
            'tables_in_db' => DB::select('SHOW TABLES'),
        ]);
    }
    
    public function testDestroyCustomer($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            
            \Log::info('Starting delete for customer', ['id' => $id, 'name' => $customer->name]);
            
            $result = $customer->delete();
            
            \Log::info('Delete completed', ['result' => $result, 'deleted_at' => $customer->deleted_at]);
            
            $verify = Customer::withTrashed()->find($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted',
                'delete_result' => $result,
                'customer_after_delete' => [
                    'id' => $verify->id,
                    'name' => $verify->name,
                    'deleted_at' => $verify->deleted_at,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

    public function testDeleteModel()
    {
        try {
            $customer = Customer::whereNull('deleted_at')->first();
            
            if (!$customer) {
                return response()->json([
                    'error' => 'No customers available to test',
                ], 400);
            }
            
            $testId = $customer->id;
            $testName = $customer->name;
            
            // Check before delete
            $before = \DB::table('customers')->where('id', $testId)->first();
            
            // Perform delete
            $deleteResult = $customer->delete();
            
            // Check after delete
            $after = \DB::table('customers')->where('id', $testId)->first();
            
            return response()->json([
                'success' => true,
                'test_customer' => [
                    'id' => $testId,
                    'name' => $testName,
                ],
                'before_delete' => [
                    'deleted_at' => $before->deleted_at,
                ],
                'delete_result' => $deleteResult,
                'after_delete' => [
                    'deleted_at' => $after->deleted_at,
                ],
                'soft_delete_working' => $after->deleted_at !== null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
