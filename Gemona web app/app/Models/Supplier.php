<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = "suppliers";
    protected $fillable = ['company', 'name', 'email', 'country_code', 'phone', 'address', 'country', 'state', 'city', 'postal_code'];
    protected $casts = [
        'id'           => 'integer',
        'company'      => 'string',
        'name'         => 'string',
        'email'        => 'string',
        'country_code' => 'string',
        'phone'        => 'string',
        'address'      => 'string',
        'country'      => 'string',
        'state'        => 'string',
        'city'         => 'string',
        'postal_code'  => 'string',
    ];

    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('supplier'))) {
            return asset($this->getFirstMediaUrl('supplier'));
        }
        return asset('images/required/profile.png');
    }

    public function purchases()
    {
        $this->hasMany(Purchase::class, 'supplier_id', 'id');
    }
}
