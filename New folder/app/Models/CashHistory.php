<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'total_omset',
        'total_jasa',
        'total_sparepart',
        'total_diskon',
        'total_hutang',
        'total_transfer',
        'total_cash'
    ];
}
