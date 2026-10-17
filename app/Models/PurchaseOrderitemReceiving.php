<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderitemReceiving extends Model
{
    protected $table = 'poitem_receiving';
    protected $id = 'id';

    protected $fillable = [
        'vehical_id',
        'weight_id',
        'purchase_order_item_id',
        'po_item_qty',
        'received_qty',
        'production_used_qty',
        'received_price',
        'size_id',
        'grade_id',
        'furnance_heat_no',
        'genrated_heat_no',
        'labtest',
        'item_remarks',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }
}
