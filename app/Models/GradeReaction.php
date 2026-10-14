<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeReaction extends Model
{
    use SoftDeletes;

    protected $table = 'grade_reactions';

    protected $fillable = [
        'grade_id',
        'property_id',
        'grade_category_id',
        'min_value',
        'max_value',
    ];

    protected $casts = [
        'min_value' => 'decimal:4',
        'max_value' => 'decimal:4',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function property()
    {
        return $this->belongsTo(
            GradeProperty::class,
            'property_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            GradeCategory::class,
            'grade_category_id'
        );
    }
}
