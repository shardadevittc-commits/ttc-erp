<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Grade;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\User;
use App\Models\Product;
use App\Models\SaleOrderItem;


class Size extends Model
{
    use SoftDeletes;

    protected $table = 'sizes';

    protected $fillable = [
        'size_name',
        'status',
        'grade_id',
        'brand_id',
        'unit_id',
        'length',
        'width',
        'qty',
        'opening_stock',
        'location',
        'avg_bundle_weight',
        'min_stock_level',
        'rack_no',
        'warehouse',
        'created_by',
    ];

    protected $casts = [
        'grade_id' => 'integer',
        'brand_id' => 'integer',
        'unit_id' => 'integer',
        'created_by' => 'integer',
        'status' => 'integer',

        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'qty' => 'decimal:2',
        'opening_stock' => 'decimal:2',
        'avg_bundle_weight' => 'decimal:3',
        'min_stock_level' => 'decimal:2',
    ];

    /* Relationships */

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'size_id');
    }

    public function saleOrderItems(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class, 'size_id');
    }
}
