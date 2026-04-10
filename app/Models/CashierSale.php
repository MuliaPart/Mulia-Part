<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierSale extends Model
{

    protected $fillable = [

        'category',
        'code',
        'name',

        'cost_price',
        'qty',

        'sell_price',
        'total_price',

        'paid_at'

    ];

}
