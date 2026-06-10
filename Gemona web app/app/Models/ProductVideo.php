<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    use HasFactory;
    protected $table = "product_videos";
    protected $fillable = ['product_id', 'video_provider', 'link'];
    protected $casts = [
        'id'             => 'integer',
        'product_id'     => 'integer',
        'video_provider' => 'integer',
        'link'           => 'string',
    ];
}
