<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'production_number',
        'finished_item_id',
        'project_id',
        'quantity_to_produce',
        'quantity_produced',
        'production_date',
        'completion_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'production_date' => 'date',
        'completion_date' => 'date',
        'quantity_to_produce' => 'decimal:3',
        'quantity_produced' => 'decimal:3',
    ];

    // Relationships
    public function finishedItem()
    {
        return $this->belongsTo(Item::class, 'finished_item_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function materials()
    {
        return $this->hasMany(ProductionItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Generate unique production number
    public static function generateNumber(): string
    {
        $lastProduction = self::withTrashed()->orderByDesc('id')->first();
        $nextNumber = $lastProduction ? ((int) substr($lastProduction->production_number, 5)) + 1 : 1;
        return 'PROD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-secondary">Pending</span>',
            'in_progress' => '<span class="badge bg-primary">In Progress</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getTotalMaterialCostAttribute(): float
    {
        return $this->materials->sum('total_cost') ?? 0;
    }
}
