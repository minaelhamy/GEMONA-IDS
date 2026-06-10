<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderArea extends Model
{
    use HasFactory;

    protected $table = "order_areas";
    protected $fillable    = ['country', 'state', 'city', 'shipping_cost', 'status'];
    protected $casts = [
        'id'            => 'integer',
        'country'       => 'string',
        'state'         => 'string',
        'city'          => 'string',
        'shipping_cost' => 'string',
        'status'        => 'integer',
    ];
}
