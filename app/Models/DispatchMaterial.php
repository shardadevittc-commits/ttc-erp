<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchMaterial extends Model
{
    protected $table = 'dispatch_material';

    protected $fillable = [
        'gate_id',
        'sale_order_id',
        'sale_order_item_id',
        'weight_id',
        'actual_qty',
        'dispatch_qty',
        'status',
        'created_by',
    ];

    protected $casts = [
        'gate_id' => 'integer',
        'sale_order_id' => 'integer',
        'sale_order_item_id' => 'integer',
        'weight_id' => 'integer',
        'actual_qty' => 'decimal:3',
        'dispatch_qty' => 'decimal:3',
        'created_by' => 'integer',
    ];
}
