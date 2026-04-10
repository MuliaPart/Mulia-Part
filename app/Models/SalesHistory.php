<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
        use HasFactory;
    protected $fillable = [
        'category',
        'code',
        'name',
        'terjual',
        'total_harga_pokok',
        'total_harga_jual',
        'total_laba',
    ];

}
