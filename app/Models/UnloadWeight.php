<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnloadWeight extends Model
{
    protected $table = 'unload_weights';

    protected $fillable = [
        'weight_id',
        'vehicle_id',
        'cust_id',
        'formlist',
        'tareweight',
        'loadedweight',
        'unload_tare_weight',
        'unload_loaded_weight',
        'material_weight',
        'size_id',
        'purchase_order_item_id',
        'product_id',
        'grade_id',
        'received_qty',
        'actual_qty',
    ];

    protected $casts = [
        'loadedweight' => 'decimal:2',
        'unload_tare_weight' => 'decimal:2',
        'unload_loaded_weight' => 'decimal:2',
        'material_weight' => 'decimal:2',
        'received_qty' => 'decimal:3',
        'actual_qty' => 'decimal:3',
    ];
}
