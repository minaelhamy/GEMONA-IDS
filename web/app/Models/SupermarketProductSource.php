<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupermarketProductSource extends Model
{
    protected $fillable = [
        'product_id',
        'source',
        'source_product_id',
        'source_sku',
        'source_price',
        'source_currency',
        'source_image_url',
        'source_product_url',
        'source_category_path',
        'source_payload',
        'scraped_at',
    ];

    protected $casts = [
        'product_id'            => 'integer',
        'source_price'          => 'decimal:6',
        'source_category_path'  => 'array',
        'source_payload'        => 'array',
        'scraped_at'            => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
