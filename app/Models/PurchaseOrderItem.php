<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'item_id', 'item_name', 'description',
        'quantity', 'received_quantity', 'unit', 'unit_price', 'total', 'sort_order', 'line_date',
    ];

    protected $casts = [
        'quantity'           => 'decimal:2',
        'received_quantity'  => 'decimal:3',
        'unit_price'         => 'decimal:2',
        'total'              => 'decimal:2',
        'line_date'          => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
