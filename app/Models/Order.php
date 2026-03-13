<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number', 'customer_id', 'quotation_id', 'order_date', 'delivery_date',
        'subtotal', 'discount', 'tax', 'grand_total', 'status',
        'notes', 'delivery_info', 'created_by',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'delivery_date' => 'date',
        'subtotal'      => 'decimal:2',
        'discount'      => 'decimal:2',
        'tax'           => 'decimal:2',
        'grand_total'   => 'decimal:2',
    ];

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->order_number, 4)) + 1 : 1;
        return 'ORD-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class)->orderBy('sort_order');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'     => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved'    => '<span class="badge bg-info">Approved</span>',
            'in_progress' => '<span class="badge bg-primary">In Progress</span>',
            'completed'   => '<span class="badge bg-success">Completed</span>',
            'delivered'   => '<span class="badge bg-success">Delivered</span>',
            'cancelled'   => '<span class="badge bg-danger">Cancelled</span>',
            default       => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
