<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'work_orders';

    const DELETED_AT = 'delete_at';

    protected $fillable = [
        'sale_order_id',
        'sale_order_item_id',
        'purchase_order_id',
        'po_item_id',
        'vehicle_id',
        'grade_id',
        'size_id',
        'material_send_for_work',
        'pending_raw_material',
        'heat_no',
        'j_messages',
        'total_operation_perfome',
        'status',
        'created_by',
    ];

    protected $casts = [
        'sale_order_id' => 'integer',
        'sale_order_item_id' => 'integer',
        'purchase_order_id' => 'integer',
        'po_item_id' => 'integer',
        'vehicle_id' => 'integer',
        'grade_id' => 'integer',
        'size_id' => 'integer',
        'material_send_for_work' => 'decimal:3',
        'pending_raw_material' => 'decimal:3',
        'created_by' => 'integer',
    ];

    // Relationship with Sale Order
    public function saleOrder()
    {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    // Relationship with Sale Order Item
    public function saleOrderItem()
    {
        return $this->belongsTo(SaleOrderItem::class, 'sale_order_item_id');
    }

    // Relationship with Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    // Relationship with Purchase Order Item
    public function poItem()
    {
        return $this->belongsTo(PoItem::class, 'po_item_id');
    }
}
