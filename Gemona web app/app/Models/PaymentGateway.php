<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class PaymentGateway extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;

    protected $table = "payment_gateways";
    protected $fillable = ['name', 'slug', 'misc', 'status'];

    protected $casts = [
        'id'     => 'integer',
        'name'   => 'string',
        'slug'   => 'string',
        'misc'   => 'string',
        'status' => 'integer',
    ];

    public function gatewayOptions()
    {
        return $this->morphMany(GatewayOption::class, 'model');
    }


    public function getImageAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('payment-gateway'))) {
            return asset($this->getFirstMediaUrl('payment-gateway'));
        }
        return asset('images/default/payment-gateway/payment-gateway.png');
    }
}
