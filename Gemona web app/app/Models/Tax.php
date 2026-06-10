<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = "taxes";
    protected $fillable = ['name', 'code', 'tax_rate', 'status'];
    protected $casts = [
        'id'       => 'integer',
        'name'     => 'string',
        'code'     => 'string',
        'tax_rate' => 'string',
        'status'   => 'integer',
    ];

}
