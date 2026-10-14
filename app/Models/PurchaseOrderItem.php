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
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cust_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function sizeUnit()
    {
        return $this->belongsTo(Unit::class, 'size_unit_id');
    }
}
