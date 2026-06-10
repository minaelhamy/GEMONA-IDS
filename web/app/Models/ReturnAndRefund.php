<?php

namespace App\Models;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Enums\CropPosition;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ReturnAndRefund extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = "return_and_refunds";
    protected $fillable = ['return_reason_id', 'note', 'reject_reason', 'order_id', 'user_id', 'order_serial_no', 'status'];
    protected $casts = [
        'id'               => 'integer',
        'return_reason_id' => 'integer',
        'note'             => 'string',
        'reject_reason'    => 'string',
        'order_id'         => 'integer',
        'user_id'          => 'integer',
        'order_serial_no'  => 'integer',
        'status'           => 'integer'
    ];

    public function getImagesAttribute(): array
    {
        $response = [];
        if (!empty($this->getFirstMediaUrl('return'))) {
            $images = $this->getMedia('return');
            foreach ($images as $image) {
                $response[] = $image['original_url'];
            }
        }
        return $response;
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(168)->height(180)->keepOriginalImageFormat()->sharpen(10);
    }

    public function returnProducts(): \Illuminate\Database\Eloquent\Relations\hasMany
    {
        return $this->hasMany(ReturnAndRefundProduct::class, 'return_and_refund_id');
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function returnReason(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(ReturnReason::class, 'return_reason_id');
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
