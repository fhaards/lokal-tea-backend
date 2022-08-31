<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Exceptions\ResourceNotFound;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'category',
        'price',
        'category',
        'description',
        'thumbnail',
    ];
    protected $hidden = [];
    protected $casts = [];
    public static function findByIdOrFail(int $id): self
    {
        $product = Product::find($id);
        if (is_null($product)) {
            throw new ResourceNotFound('Product not found');
        }
        return $product;
    }
}
