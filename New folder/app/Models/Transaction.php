<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Transaction extends Model
{

    protected $fillable = [
    'invoice',
    'customer_name',
    'customer_type',
    'payment_method',
    'total_items',
    'total_price'
    ];
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

}
