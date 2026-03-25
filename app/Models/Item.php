<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'description', 'default_price', 'cost_price',
        'unit', 'category', 'item_type', 'track_inventory',
        'stock_quantity', 'reorder_level', 'is_active',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'decimal:3',
        'reorder_level' => 'decimal:3',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    // Relationships
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function productionItems()
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function finishedProductions()
    {
        return $this->hasMany(ProductionOrder::class, 'finished_item_id');
    }

    // Inventory methods
    public function addStock(float $quantity, string $referenceType, ?int $referenceId = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($quantity, $referenceType, $referenceId, $notes) {
            $this->increment('stock_quantity', $quantity);
            $this->refresh();

            StockMovement::create([
                'item_id' => $this->id,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'balance_after' => $this->stock_quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'movement_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function removeStock(float $quantity, string $referenceType, ?int $referenceId = null, ?string $notes = null): void
    {
        if ($this->stock_quantity < $quantity) {
            throw new \Exception("Insufficient stock for {$this->name}. Available: {$this->stock_quantity} {$this->unit}");
        }

        DB::transaction(function () use ($quantity, $referenceType, $referenceId, $notes) {
            $this->decrement('stock_quantity', $quantity);
            $this->refresh();

            StockMovement::create([
                'item_id' => $this->id,
                'movement_type' => 'out',
                'quantity' => $quantity,
                'balance_after' => $this->stock_quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'movement_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function adjustStock(float $newQuantity, ?string $notes = null): void
    {
        $oldQuantity = $this->stock_quantity;
        $difference = $newQuantity - $oldQuantity;

        DB::transaction(function () use ($newQuantity, $difference, $notes) {
            $this->update(['stock_quantity' => $newQuantity]);

            StockMovement::create([
                'item_id' => $this->id,
                'movement_type' => $difference >= 0 ? 'in' : 'out',
                'quantity' => abs($difference),
                'balance_after' => $newQuantity,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => $notes ?? 'Stock adjustment',
                'movement_date' => now(),
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function isLowStock(): bool
    {
        if (!$this->track_inventory || !$this->reorder_level) {
            return false;
        }

        return $this->stock_quantity <= $this->reorder_level;
    }

    // Accessors
    public function getLowStockBadgeAttribute(): string
    {
        if (!$this->track_inventory) {
            return '<span class="badge bg-secondary">No Tracking</span>';
        }

        if ($this->stock_quantity <= 0) {
            return '<span class="badge bg-danger">Out of Stock</span>';
        }

        if ($this->isLowStock()) {
            return '<span class="badge bg-warning">Low Stock</span>';
        }

        return '<span class="badge bg-success">In Stock</span>';
    }

    public function getItemTypeBadgeAttribute(): string
    {
        return match ($this->item_type) {
            'trading' => '<span class="badge bg-primary">Trading</span>',
            'manufactured' => '<span class="badge bg-info">Manufactured</span>',
            'raw_material' => '<span class="badge bg-secondary">Raw Material</span>',
            'service' => '<span class="badge bg-success">Service</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
