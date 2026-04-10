<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'category',
        'code',
        'name',
        'stock_awal',
        'terjual',
        'stock_akhir',
        'total_harga_pokok',
        'total_harga_jual',
        'total_laba',
    ];
}
