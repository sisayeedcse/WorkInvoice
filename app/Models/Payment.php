<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'customer_id', 'payment_date', 'amount',
        'method', 'reference', 'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'cash'          => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'online'        => 'Online Payment',
            'cheque'        => 'Cheque',
            default         => 'Other',
        };
    }
}
