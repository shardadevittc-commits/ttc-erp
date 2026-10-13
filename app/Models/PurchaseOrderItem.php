<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';

    protected $fillable = [
        'purchase_order_id',

        'cust_id',
        'b_rate',
        'ref_saleorder_id',
        'basis',

        'product_id',
        'grade_id',
        'size_id',
        'size_unit_id',

        'length',
        'rate',
        'qty',
        'qty_unit',
        'qty_varaiate',

        'remarks',
        'created_by',
    ];


    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrder::class,
            'purchase_order_id'
        );
    }
}
