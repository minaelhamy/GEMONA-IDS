<?php

namespace App\Models;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductReview extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table    = "product_reviews";
    protected $fillable = ['user_id', 'product_id', 'star', 'review'];
    protected $casts    = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'product_id' => 'integer',
        'star'       => 'integer',
        'review'     => 'string',
    ];

    public function getImagesAttribute(): array
    {
        $response = [];
        if (!empty($this->getFirstMediaUrl('product-review'))) {
            $images = $this->getMedia('product-review');
            foreach ($images as $image) {
                $response[] = $image['original_url'];
            }
        }
        return $response;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Fill, 112, 72)->keepOriginalImageFormat()->sharpen(10);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
