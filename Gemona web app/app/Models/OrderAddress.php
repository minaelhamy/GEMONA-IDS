<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $table = "order_addresses";
    protected $fillable = [
        'order_id',
        'user_id',
        'address_type',
        'full_name',
        'email',
        'country_code',
        'phone',
        'country',
        'address',
        'user_id',
        'state',
        'city',
        'zip_code',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'id'           => 'integer',
        'order_id'     => 'integer',
        'user_id'      => 'integer',
        'address_type' => 'integer',
        'full_name'    => 'string',
        'email'        => 'string',
        'country_code' => 'string',
        'phone'        => 'string',
        'country'      => 'string',
        'address'      => 'string',
        'state'        => 'string',
        'city'         => 'string',
        'zip_code'     => 'string',
        'latitude'     => 'string',
        'longitude'    => 'string',
    ];
}
