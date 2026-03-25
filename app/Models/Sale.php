<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_number',
        'customer_id',
        'customer_name',
        'sale_date',
        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'payment_method',
        'payment_reference',
        'notes',
        'receipt_printed',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'receipt_printed' => 'boolean',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Generate unique sale number
    public static function generateNumber(): string
    {
        $lastSale = self::withTrashed()->orderByDesc('id')->first();
        $nextNumber = $lastSale ? ((int) substr($lastSale->sale_number, 5)) + 1 : 1;
        return 'SALE-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    // Accessors
    public function getPaymentMethodBadgeAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => '<span class="badge bg-success">Cash</span>',
            'bank_transfer' => '<span class="badge bg-primary">Bank Transfer</span>',
            'online' => '<span class="badge bg-info">Online</span>',
            'cheque' => '<span class="badge bg-warning">Cheque</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getDisplayCustomerAttribute(): string
    {
        return $this->customer?->name ?? $this->customer_name ?? 'Walk-in Customer';
    }
}
