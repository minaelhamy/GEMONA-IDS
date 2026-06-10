<?php

namespace App\Models;



use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Benefit extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = "benefits";
    protected $fillable = ['title', 'description', 'status', 'sort'];
    protected $casts = [
        'id'          => 'integer',
        'title'       => 'string',
        'description' => 'string',
        'status'      => 'integer',
        'sort'        => 'integer',
    ];

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('benefit'))) {
            $benefit = $this->getMedia('benefit')->last();
            return $benefit->getUrl('thumb');
        }
        return asset('images/default/benefit/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('benefit'))) {
            $benefit = $this->getMedia('benefit')->last();
            return $benefit->getUrl('cover');
        }
        return asset('images/default/benefit/cover.png');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Fill, 36, 36)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(600)->keepOriginalImageFormat()->sharpen(10);
    }
}
