<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class RiwayatKasirController extends Controller
{
    public function index()
    {
        $sales = \App\Models\TransactionItem::latest()
            ->paginate(50);

        return view('sales.riwayat-kasir', compact('sales'));
    }

    public function deleteAll()
    {
        \App\Models\Transaction::truncate();
        \App\Models\TransactionItem::truncate();

        return back()->with('success','Semua riwayat kasir dihapus');
    }
}