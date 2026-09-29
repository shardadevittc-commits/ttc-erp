<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Product;
use App\Models\SaleOrderItem;

class Grade extends Model
{
    use SoftDeletes;

    protected $table = 'grades';

    protected $fillable = [
        'grade_name',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'grade_id');
    }

    public function saleOrderItems(): HasMany
    {
        return $this->hasMany(SaleOrderItem::class, 'grade_id');
    }
}
