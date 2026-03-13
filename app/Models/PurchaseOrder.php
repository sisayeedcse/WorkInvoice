<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number', 'supplier_name', 'supplier_phone', 'supplier_email', 'supplier_address',
        'date', 'delivery_date', 'subtotal', 'discount', 'tax', 'grand_total', 'status',
        'notes', 'terms', 'order_id', 'created_by',
    ];

    protected $casts = [
        'date'          => 'date',
        'delivery_date' => 'date',
        'subtotal'      => 'decimal:2',
        'discount'      => 'decimal:2',
        'tax'           => 'decimal:2',
        'grand_total'   => 'decimal:2',
    ];

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->po_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('sort_order');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'sent'      => '<span class="badge bg-info">Sent</span>',
            'received'  => '<span class="badge bg-success">Received</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
