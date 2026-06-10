<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSection extends Model
{
    use HasFactory;

    protected $table = "product_sections";
    protected $fillable = ['name', 'slug', 'status'];
    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'slug'       => 'string',
        'status'     => 'integer',
    ];

    public function scopeActive($query, $col = 'status')
    {
        return $query->where($col, Status::ACTIVE);
    }
    public function products(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_section_products', 'product_section_id');
    }

    public function productSectionProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductSectionProduct::class, 'product_section_id', 'id');
    }
}
