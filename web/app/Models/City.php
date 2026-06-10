<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = "cities";
    protected $fillable = ["name", "state_id", "status"];

    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'state_id'  => 'integer',
        'status'    => 'integer'
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }
}
