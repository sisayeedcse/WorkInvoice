<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'expense_name', 'category', 'amount',
        'expense_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get formatted category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'materials' => 'Materials',
            'labor'     => 'Labor',
            'transport' => 'Transport',
            'tools'     => 'Tools',
            'others'    => 'Others',
            default     => ucfirst($this->category),
        };
    }

    /**
     * Get category badge HTML
     */
    public function getCategoryBadgeAttribute(): string
    {
        return match ($this->category) {
            'materials' => '<span class="badge" style="background-color: var(--navy);">Materials</span>',
            'labor'     => '<span class="badge bg-info">Labor</span>',
            'transport' => '<span class="badge bg-secondary">Transport</span>',
            'tools'     => '<span class="badge bg-dark">Tools</span>',
            'others'    => '<span class="badge bg-secondary">Others</span>',
            default     => '<span class="badge bg-secondary">' . $this->category_label . '</span>',
        };
    }
}
