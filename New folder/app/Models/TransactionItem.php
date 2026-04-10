<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{

    protected $fillable = [

        'transaction_id',

        'category',
        'code',
        'name',

        'cost_price',
        'sell_price',

        'qty',
        'total_price'

    ];
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
