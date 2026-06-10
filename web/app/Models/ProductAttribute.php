<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    protected $table = "product_attributes";
    protected $fillable = ['name'];
    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
    ];

    public function productAttributeOptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductAttributeOption::class, 'product_attribute_id', 'id');
    }
}
