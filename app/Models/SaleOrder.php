<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleOrder extends Model
{
    use SoftDeletes;

    protected $table = 'sale_orders';

    protected $fillable = [
        'customer_id',
        'payment_term',
        'customer_po_no',
        'order_qty',
        'basic_rate',
        'dispatch_date',
        'remarks',
        'freight_basis',
        'created_by',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'freight_basis' => 'integer',
        'created_by' => 'integer',
    ];

    // Freight basis constants
    public const FREIGHT_EX = 1;
    public const FREIGHT_FOR = 2;

    public function items(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class, 'sale_order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
