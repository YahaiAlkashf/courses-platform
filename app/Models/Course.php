<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course  extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'image',
        'price'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
