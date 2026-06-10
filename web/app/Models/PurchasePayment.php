<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PurchasePayment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'purchase_id',
        'date',
        'reference_no',
        'amount',
        'payment_method'
    ];
    protected $casts = [
        'id'             => 'integer',
        'purchase_id'    => 'integer',
        'date'           => 'string',
        'reference_no'   => 'string',
        'amount'         => 'decimal:6',
        'payment_method' => 'integer'
    ];

    public function getFileAttribute()
    {
        if (!empty($this->getFirstMediaUrl('purchase_payment'))) {
            $product = $this->getMedia('purchase_payment')->first();
            return $product->getUrl();
        }
    }
}
