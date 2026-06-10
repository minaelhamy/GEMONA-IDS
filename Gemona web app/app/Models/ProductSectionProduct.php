<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSectionProduct extends Model
{
    use HasFactory;

    protected $table = "product_section_products";
    protected $fillable = ['product_section_id', 'product_id'];
    protected $casts = [
        'id'                 => 'integer',
        'product_section_id' => 'integer',
        'product_id'         => 'integer',
    ];

    public function productSection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductSection::class, 'product_section_id', 'id');
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withTrashed();
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_section_products', 'product_section_id', 'product_id');
    }


}
