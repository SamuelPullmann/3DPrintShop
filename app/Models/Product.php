<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'price',
        'product_type',
        'category',
        'file_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
