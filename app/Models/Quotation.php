<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quotation_number', 'customer_id', 'date', 'valid_until',
        'subtotal', 'discount', 'tax', 'grand_total',
        'notes', 'terms', 'status', 'created_by',
    ];

    protected $casts = [
        'date'        => 'date',
        'valid_until' => 'date',
        'subtotal'    => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public static function generateNumber(): string
    {
        $last = static::withTrashed()->orderByDesc('id')->first();
        $next = $last ? ((int) substr($last->quotation_number, 2)) + 1 : 1;
        return 'Q-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'sent'      => '<span class="badge bg-info">Sent</span>',
            'accepted'  => '<span class="badge bg-success">Accepted</span>',
            'rejected'  => '<span class="badge bg-danger">Rejected</span>',
            'converted' => '<span class="badge bg-primary">Converted</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
