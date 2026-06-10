<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ReturnOrder extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'date',
        'reference_no',
        'subtotal',
        'tax',
        'discount',
        'total',
        'reason'
    ];

    protected $casts = [
        'id'            => 'integer',
        'user_id'       => 'integer',
        'date'          => 'datetime',
        'reference_no'  => 'string',
        'subtotal'      => 'decimal:6',
        'tax'           => 'decimal:6',
        'discount'      => 'decimal:6',
        'total'         => 'decimal:6',
        'reason'        => 'string',
    ];

    public function stocks(): \Illuminate\Database\Eloquent\Relations\morphMany
    {
        return $this->morphMany(Stock::class, 'model');
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
       return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }
    public function creator()
    {
       return $this->belongsTo(User::class, 'creator_id', 'id');
    }
    public function getFileAttribute()
    {
        if (!empty($this->getFirstMediaUrl('returnOrder'))) {
            $file = $this->getMedia('returnOrder')->first();
            return $file->getUrl();
        }
    }

}
