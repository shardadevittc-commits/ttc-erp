<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProduction extends Model
{
    use SoftDeletes;

    protected $table = 'stock_productions';

    protected $fillable = [
        // Links
        'work_order_id',
        'poitem_receiving_id',
        'purchase_order_item_id',
        'dispatch_id',
        'sale_order_id',
        'sale_order_item_id',
        'linked_to_sale_order_at',

        // Product Details
        'product_id',
        'brand_id',
        'size_id',
        'size_extra',
        'unit',
        'grade_id',

        // Quantity
        'item_qty',
        'qty_type',

        // Steps
        'straightening',
        'point_discard',
        'phosphating',

        // Specs
        'hardness',
        'hardness_unit',
        'pcs_length_unit',
        'coating',

        // Bundle
        'fs_bundle_weight',
        'fs_bundle_unit',

        // Other
        'item_remarks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'linked_to_sale_order_at' => 'datetime',

        'item_qty' => 'decimal:3',
        'fs_bundle_weight' => 'decimal:3',

        'work_order_id' => 'integer',
        'poitem_receiving_id' => 'integer',
        'purchase_order_item_id' => 'integer',
        'dispatch_id' => 'integer',
        'sale_order_id' => 'integer',
        'sale_order_item_id' => 'integer',

        'product_id' => 'integer',
        'brand_id' => 'integer',
        'size_id' => 'integer',
        'grade_id' => 'integer',

        'qty_type' => 'integer',
        'straightening' => 'integer',
        'point_discard' => 'integer',
        'phosphating' => 'integer',
        'fs_bundle_unit' => 'integer',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(
            WorkOrdersAndProcess::class,
            'work_order_id'
        );
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function saleOrderItem()
    {
        return $this->belongsTo(SaleOrderItem::class, 'sale_order_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    } 
}
