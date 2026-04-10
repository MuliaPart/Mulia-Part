<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    protected $fillable = [
        'product_id',
        'qty',
        'cost_price',
        'sell_price',
        'supplier'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
