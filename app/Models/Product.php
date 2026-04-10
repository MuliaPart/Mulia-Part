<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'cost_price',
        'sell_price',
        'stock',
        'image',
    ];

    // Optional tapi bagus untuk casting angka
    protected $casts = [
        'cost_price' => 'integer',
        'sell_price' => 'integer',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function restocks()
    {
        return $this->hasMany(Restock::class)
            ->latest(); // sama dengan orderBy('created_at','desc')
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class)
            ->latest();
    }
}