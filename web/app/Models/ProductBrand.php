<?php

namespace App\Models;

use App\Enums\Status;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductBrand extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected bool $fallbackExternalImageResolved = false;
    protected ?string $fallbackExternalImageUrl = null;
    protected $table = "product_brands";
    protected $fillable = ['name', 'slug', 'description', 'status'];
    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'slug'        => 'string',
        'description' => 'string',
        'status'      => 'integer',
    ];

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('product-brand'))) {
            $brand = $this->getMedia('product-brand')->last();
            return $brand->getUrl('thumb');
        }
        if ($url = $this->fallbackExternalImageUrl()) {
            return Product::externalImageProxyUrl($url);
        }
        return asset('images/default/brand/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('product-brand'))) {
            $brand = $this->getMedia('product-brand')->last();
            return $brand->getUrl('cover');
        }
        if ($url = $this->fallbackExternalImageUrl()) {
            return Product::externalImageProxyUrl($url);
        }
        return asset('images/default/brand/cover.png');
    }

    private function fallbackExternalImageUrl(): ?string
    {
        if (!$this->fallbackExternalImageResolved) {
            $this->fallbackExternalImageUrl = Product::query()
                ->where('product_brand_id', $this->id)
                ->where('status', Status::ACTIVE)
                ->whereNotNull('external_image_url')
                ->latest('id')
                ->value('external_image_url');
            $this->fallbackExternalImageResolved = true;
        }

        return $this->fallbackExternalImageUrl;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Fill, 108, 108)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(450)->keepOriginalImageFormat()->sharpen(10);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class)->where(['status' => Status::ACTIVE]);
    }
}
