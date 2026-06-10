<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = "states";
    protected $fillable = ["name", "country_id", "status"];

    protected $casts = [
        'id'        => 'integer',
        'name'      => 'string',
        'country_id'=> 'integer',
        'status'    => 'integer'
    ];

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
}
