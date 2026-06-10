<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'country_code', 'latitude', 'longitude', 'city', 'state', 'zip_code', 'address', 'status'];
    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'email'        => 'string',
        'phone'        => 'string',
        'country_code' => 'string',
        'latitude'     => 'string',
        'longitude'    => 'string',
        'city'         => 'string',
        'state'        => 'string',
        'zip_code'     => 'string',
        'address'      => 'string',
        'status'       => 'integer',
    ];
}
