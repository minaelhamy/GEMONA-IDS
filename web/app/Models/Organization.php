<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'city',
        'address',
        'type',
        'status',
        'is_seeded',
    ];

    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'country'   => 'string',
        'city'      => 'string',
        'address'   => 'string',
        'type'      => 'string',
        'status'    => 'integer',
        'is_seeded' => 'integer',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }
}
