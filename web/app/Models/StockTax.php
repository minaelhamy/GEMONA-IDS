<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTax extends Model
{
    use HasFactory;

    protected $table = 'stock_taxes';

    protected $fillable = [
        'stock_id',
        'product_id',
        'tax_id',
        'name',
        'code',
        'tax_rate',
        'tax_amount'
    ];

    protected $casts = [
        'id'            => 'integer',
        'stock_id'      => 'integer',
        'product_id'    => 'integer',
        'tax_id'        => 'integer',
        'name'          => 'string',
        'code'          => 'string',
        'tax_rate'      => 'decimal:6',
        'tax_amount'    => 'decimal:6'
    ];

    public function stock(): void
    {
        $this->belongsTo(Stock::class , 'stock_id', 'id');
    }

    public function tax(): void
    {
        $this->belongsTo(Tax::class, 'tax_id' , 'id');
    }
}
