<?php

namespace App\Http\Controllers;

use App\Models\TransactionItem;

class SalesHistoryController extends Controller
{

    public function index()
    {

        $sales = TransactionItem::orderBy('id','desc')
                    ->paginate(100);

        return view('sales.history', compact('sales'));

    }


    public function deleteAll()
    {

        TransactionItem::truncate();

        return back()->with('success','Semua riwayat kas dihapus');

    }
    

}
