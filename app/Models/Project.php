<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_number', 'order_id', 'project_name', 'start_date',
        'expected_end_date', 'actual_end_date', 'status', 'total_revenue',
        'advance_received', 'payment_status', 'notes', 'created_by',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'expected_end_date' => 'date',
        'actual_end_date'   => 'date',
        'total_revenue'     => 'decimal:2',
        'advance_received'  => 'decimal:2',
    ];

    /**
     * Generate next project number
     */
    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->project_number, 4)) + 1 : 1;
        return 'PRJ-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class)->orderByDesc('expense_date');
    }

    /**
     * Accessors for calculations
     */
    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return (float) ($this->total_revenue - $this->total_expense);
    }

    public function getDueAmountAttribute(): float
    {
        $invoice = $this->order?->invoice;
        $paidAmount = $invoice ? (float) $invoice->paid_amount : 0;

        return (float) ($this->total_revenue - ($this->advance_received + $paidAmount));
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => '<span class="badge bg-warning text-dark">Pending</span>',
            'started'     => '<span class="badge bg-info">Started</span>',
            'in_progress' => '<span class="badge bg-primary">In Progress</span>',
            'completed'   => '<span class="badge bg-success">Completed</span>',
            'delivered'   => '<span class="badge bg-success">Delivered</span>',
            default       => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Payment status badge HTML
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'    => '<span class="badge bg-success">Paid</span>',
            'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
            'unpaid'  => '<span class="badge bg-danger">Unpaid</span>',
            default   => '<span class="badge bg-secondary">' . ucfirst($this->payment_status) . '</span>',
        };
    }
}
