<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SaleOrder;
use App\Models\Product;
use App\Models\Grade;
use App\Models\Brand;
use App\Models\Size;
use App\Models\User;
use App\Models\Customer;

class SaleOrderItem extends Model
{
    use SoftDeletes;

    protected $table = 'sale_order_items';

    protected $fillable = [
        'size_unit',
        'sale_order_id',
        'product_id',
        'grade_id',
        'brand_id',
        'size_id',
        'sale_item_prices',
        'size_extra',
        'item_qty',
        'qty_type',
        'dispatch_qty',
        'dispatched_qty',
        'pending_qty',
        's_marked_completed',
        'item_remarks',
        'straightening',
        'point_discard',
        'phosphating',
        'hardness',
        'hardness_unit',
        'pcs_length_unit',
        'coating',
        'product_type',
        'fs_bundle_weight',
        'fs_bundle_unit',
        'created_by',
    ];

    protected $casts = [
        'sale_order_id' => 'integer',
        'product_id' => 'integer',
        'grade_id' => 'integer',
        'brand_id' => 'integer',
        'size_id' => 'integer',
        'product_type' => 'integer',
        'created_by' => 'integer',

        'sale_item_prices' => 'decimal:3',
        'size_extra' => 'decimal:3',
        'item_qty' => 'decimal:3',
        'dispatch_qty' => 'decimal:3',
        'dispatched_qty' => 'decimal:3',
        'pending_qty' => 'decimal:3',
        'fs_bundle_weight' => 'decimal:3',

        'qty_type' => 'integer',
        's_marked_completed' => 'integer',
        'straightening' => 'integer',
        'point_discard' => 'integer',
        'phosphating' => 'integer',
        'fs_bundle_unit' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    // qty_type
    public const QTY_TONS = 1;
    public const QTY_KGS = 2;

    // s_marked_completed
    public const NOT_COMPLETED = 1;
    public const COMPLETED = 2;

    // Yes / No
    public const YES = 1;
    public const NO = 2;

    // fs_bundle_unit
    public const BUNDLE_KG = 1;
    public const BUNDLE_TONS = 2;


    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
