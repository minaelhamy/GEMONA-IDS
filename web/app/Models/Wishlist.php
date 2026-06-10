<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $table = "wishlists";
    protected $fillable = ['product_id', 'user_id'];
    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
        'user_id'    => 'integer',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
