<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'return_materials';

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
        'other_data' => 'array',
    ];
}