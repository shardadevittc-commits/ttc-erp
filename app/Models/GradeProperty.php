<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeProperty extends Model
{
    use SoftDeletes;

    protected $table = 'grade_properties';

    protected $fillable = [
        'category_id',
        'name',
        'unit',
    ];

    public function gradeCategory()
    {
        return $this->belongsTo(
            GradeCategory::class,
            'category_id'
        );
    }
}
