<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $primaryKey = 'review_id';

    public $timestamps = false; // We only use created_at

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'review_text',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
