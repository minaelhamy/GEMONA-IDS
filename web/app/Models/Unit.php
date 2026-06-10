<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;
    protected $table = "units";
    protected $fillable = ['name', 'code', 'status'];
    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
        'code'   => 'string',
        'status' => 'integer',
    ];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class)->where(['status' => Status::ACTIVE]);
    }
}
