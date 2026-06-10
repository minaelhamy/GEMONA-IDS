<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table ="countries";

    protected $fillable = ["name","code","status"];

    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'code'      => 'string',
        'status'    => 'integer'
    ];
}
