<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number', 'customer_id', 'order_id', 'quotation_id',
        'date', 'due_date', 'subtotal', 'discount', 'tax',
        'grand_total', 'paid_amount', 'balance', 'status',
        'notes', 'terms', 'created_by',
    ];

    protected $casts = [
        'date'        => 'date',
        'due_date'    => 'date',
        'subtotal'    => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance'     => 'decimal:2',
    ];

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'sent'      => '<span class="badge bg-info">Sent</span>',
            'partial'   => '<span class="badge bg-warning text-dark">Partial</span>',
            'paid'      => '<span class="badge bg-success">Paid</span>',
            'overdue'   => '<span class="badge bg-danger">Overdue</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
