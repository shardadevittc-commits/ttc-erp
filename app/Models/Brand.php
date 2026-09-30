<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\SaleOrderItem;

class Brand extends Model
{
    use SoftDeletes;

    protected $table = 'brands';

    protected $fillable = [
        'brand_name',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'status' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function saleOrderItems(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class, 'brand_id');
    }
}
