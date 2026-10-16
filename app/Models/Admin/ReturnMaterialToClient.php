<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnMaterialToClient extends Model
{
    use SoftDeletes;

    protected $table = 'return_material_to_client';

    protected $fillable = [
        'vehicle_id',
        'customer_id',
        'weight_id',
        'purchase_orders_details',
        'return_reason',
        'truckno',
        'created_by',
    ];

    protected $casts = [
        'purchase_orders_details' => 'array',
    ];
}
