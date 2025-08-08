<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'sku',
        'price',
        'images',
        'variants'
    ];

    protected $casts = [
        'variants' => 'array',
        'images' => 'array'
    ];

    public function getMainImageAttribute()
    {
        return $this->images[0]['src'] ?? null;
    }
}