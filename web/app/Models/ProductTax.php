<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductTax extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "product_taxes";
    protected $fillable    = ['product_id', 'tax_id'];
    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'tax_id'     => 'integer',
    ];

    public function tax(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
