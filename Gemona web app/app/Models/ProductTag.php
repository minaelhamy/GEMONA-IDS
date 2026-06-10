<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $table = "product_tags";
    protected $fillable = ['product_id', 'name'];
    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'name'       => 'string',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
