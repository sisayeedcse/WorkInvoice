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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number', 20)->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('project_name');
            $table->date('start_date');
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->enum('status', ['pending', 'started', 'in_progress', 'completed', 'delivered'])->default('pending');
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('advance_received', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering
            $table->index('status');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
