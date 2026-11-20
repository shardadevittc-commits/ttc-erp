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
        'dispatch_qty_unit',
        'return_qty',
        'return_remarks',
        'status',
        'fs_bundle_weight',
        'fs_bundle_unit',
        'total_full_bundle_count',
        'lose_bundle_weight',
        'lose_bundle_count',
        'per_pcs_weight',
        'total_pcs',
        'no_of_boxes',
        'loss_box_pcs',
        'loss_box_count',
        'created_by',
    ];

    protected $casts = [
        'gate_id' => 'integer',
        'sale_order_id' => 'integer',
        'sale_order_item_id' => 'integer',
        'weight_id' => 'integer',
        'actual_qty' => 'decimal:3',
        'dispatch_qty' => 'decimal:3',
        'fs_bundle_weight' => 'decimal:3',
        'total_full_bundle_count' => 'integer',
        'lose_bundle_weight' => 'decimal:3',
        'lose_bundle_count' => 'integer',
        'per_pcs_weight' => 'decimal:3',
        'total_pcs' => 'integer',
        'no_of_boxes' => 'integer',
        'loss_box_pcs' => 'integer',
        'loss_box_count' => 'integer',
        'created_by' => 'integer',
        'return_qty' => 'decimal:3',
    ];

    public function applyProductionSnapshot(int $quantityType, WorkOrderProcess $workOrder): void
    {
        $this->dispatch_qty_unit = $quantityType === SaleOrderItem::QTY_KGS ? 'KGS' : 'Tons';
        $this->fill([
            'fs_bundle_weight' => $workOrder->fs_bundle_weight,
            'fs_bundle_unit' => $workOrder->fs_bundle_unit,
            'total_full_bundle_count' => $workOrder->total_full_bundle_count,
            'lose_bundle_weight' => $workOrder->lose_bundle_weight,
            'lose_bundle_count' => $workOrder->lose_bundle_count,
            'per_pcs_weight' => $workOrder->per_pcs_weight,
            'total_pcs' => $workOrder->finished_pcs,
            'no_of_boxes' => $workOrder->no_of_boxes,
            'loss_box_pcs' => $workOrder->loss_box_pcs,
            'loss_box_count' => $workOrder->loss_box_count,
        ]);
    }

    public function gate()
    {
        return $this->belongsTo(GateAndGaadi::class, 'gate_id');
    }

    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function saleOrderItem()
    {
        return $this->belongsTo(SaleOrderItem::class, 'sale_order_item_id');
    }

    public function weight()
    {
        return $this->belongsTo(Weight::class, 'weight_id');
    }
}
