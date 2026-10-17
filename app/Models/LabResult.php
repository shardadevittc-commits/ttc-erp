<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $table = 'lab_results';

    protected $fillable = [
        'vehicle_id',
        'weight_id',
        'formlist',
        'cust_id',
        'purchase_order_item_id',
        'size_id',
        'grade_id',
        'grade_category_id',
        'property_id',
        'actual_value',
    ];

    protected $casts = [
        'formlist' => 'array',
        'actual_value' => 'decimal:4',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function category()
    {
        return $this->belongsTo(
            GradeCategory::class,
            'grade_category_id'
        );
    }

    public function property()
    {
        return $this->belongsTo(
            GradeProperty::class,
            'property_id'
        );
    }
}
