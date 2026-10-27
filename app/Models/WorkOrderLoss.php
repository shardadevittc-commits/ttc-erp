<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrderLoss extends Model
{
    use SoftDeletes;

    protected $table = 'work_order_loss';

    protected $fillable = [
        'work_order_id',
        'vehicle_id',
        'sale_order_item_id',
        'stage_name',
        'unique_stage_name',
        'initial_weight',
        'scrap_type',
        'loss_percentage',
        'loss_amount',
        'loss_weight',
        'weight_after_loss',
        'adjustable_material',
        'weight_after_adjustable_material',
        'created_by',
    ];

    protected $casts = [
        'initial_weight' => 'decimal:3',
        'scrap_type' => 'integer',
        'loss_percentage' => 'decimal:3',
        'loss_amount' => 'decimal:3',
        'loss_weight' => 'decimal:3',
        'weight_after_loss' => 'decimal:3',
        'adjustable_material' => 'decimal:3',
        'weight_after_adjustable_material' => 'decimal:3',
    ];
}
