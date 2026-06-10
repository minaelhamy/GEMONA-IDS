<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderOutletAddress extends Model
{
    use HasFactory;

    protected $table = "order_outlet_addresses";

    protected $fillable = [
        'order_id',
        'user_id',
        'name',
        'email',
        'phone',
        'country_code',
        'latitude',
        'longitude',
        'city',
        'state',
        'zip_code',
        'address'
    ];

    protected $casts = [
        'id'           => 'integer',
        'order_id'     => 'integer',
        'user_id'      => 'integer',
        'name'         => 'string',
        'email'        => 'string',
        'phone'        => 'string',
        'country_code' => 'string',
        'latitude'     => 'string',
        'longitude'    => 'string',
        'city'         => 'string',
        'state'        => 'string',
        'zip_code'     => 'string',
        'address'      => 'string'
    ];
}
