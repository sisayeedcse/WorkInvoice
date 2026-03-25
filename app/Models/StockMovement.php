<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'notes',
        'movement_date',
        'created_by',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
        'quantity' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    // Relationships
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getMovementTypeBadgeAttribute(): string
    {
        return match ($this->movement_type) {
            'in' => '<span class="badge bg-success">In</span>',
            'out' => '<span class="badge bg-danger">Out</span>',
            'adjustment' => '<span class="badge bg-warning">Adjustment</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getReferenceTypeLabelAttribute(): string
    {
        return match ($this->reference_type) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'production' => 'Production',
            'adjustment' => 'Adjustment',
            'stock_receive' => 'Stock Received',
            default => 'Unknown',
        };
    }
}
