<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'discription',
        'image',
    ];
    public function courses(){
        return $this->hasMany(Course::class);
    }
}
