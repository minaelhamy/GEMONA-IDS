<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgent extends Model
{
    protected $table = "ai_agents";
    protected $fillable = ['name', 'slug', 'misc', 'status'];
    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
        'slug'   => 'string',
        'misc'   => 'string',
        'status' => 'integer'
    ];

    public function gatewayOptions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(GatewayOption::class, 'model');
    }
}
