<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'cust_id',
        'b_rate',
        'ref_saleorder_id',
        'basis',
        'term',
        'remarks',
        'created_by',
    ];


    public function items(): HasMany
    {
        return $this->hasMany(
            PurchaseOrderItem::class,
            'purchase_order_id'
        );
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

}
