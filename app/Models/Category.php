<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'name',
        'image',
        'status',
        'parent_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }
}


