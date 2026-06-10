<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SocialLogin extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table    = "social_logins";
    protected $fillable = ['name', 'slug', 'misc', 'status'];
    protected $casts    = [
        'id'     => 'integer',
        'name'   => 'string',
        'slug'   => 'string',
        'misc'   => 'string',
        'status' => 'integer',
    ];

    public function gatewayOptions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(GatewayOption::class, 'model');
    }

    public function getImageAttribute(): string
    {
        return asset($this->getFirstMediaUrl('social-login'));
    }
}
