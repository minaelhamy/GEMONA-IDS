<?php

namespace App\Models;


use App\Enums\Status;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;


class ProductCategory extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasRecursiveRelationships;

    protected bool $fallbackExternalImageResolved = false;
    protected ?string $fallbackExternalImageUrl = null;
    protected $table = "product_categories";
    protected $fillable = ['name', 'slug', 'description', 'status', 'parent_id'];
    protected $casts = [
        'id'          => 'integer',
        'name'        => 'string',
        'slug'        => 'string',
        'description' => 'string',
        'status'      => 'integer',
        'parent_id'   => 'integer',
    ];
    protected $appends = array('cover');

    public function getThumbAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('product-category'))) {
            $category = $this->getMedia('product-category')->last();
            return $category->getUrl('thumb');
        }
        if ($url = $this->fallbackExternalImageUrl()) {
            return Product::externalImageProxyUrl($url);
        }
        return asset('images/default/category/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if (!empty($this->getFirstMediaUrl('product-category'))) {
            $category = $this->getMedia('product-category')->last();
            return $category->getUrl('cover');
        }
        if ($url = $this->fallbackExternalImageUrl()) {
            return Product::externalImageProxyUrl($url);
        }
        return asset('images/default/category/cover.png');
    }

    private function fallbackExternalImageUrl(): ?string
    {
        if (!$this->fallbackExternalImageResolved) {
            $this->fallbackExternalImageUrl = Product::query()
                ->where('product_category_id', $this->id)
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
        $this->addMediaConversion('thumb')->width(252)->height(183)->keepOriginalImageFormat()->sharpen(10);
        $this->addMediaConversion('cover')->width(960)->height(1440)->keepOriginalImageFormat()->sharpen(10);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class)->where(['status' => Status::ACTIVE]);
    }

    public function parent_category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }
}
