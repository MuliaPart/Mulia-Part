<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashHistory;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
    {
        $histories = CashHistory::whereMonth('tanggal', Carbon::now()->month)
                    ->whereYear('tanggal', Carbon::now()->year)
                    ->orderBy('tanggal')
                    ->get();

        $dates = $histories->pluck('tanggal')->toArray();
        $omsets = $histories->pluck('total_omset')->toArray();

        return view('dashboard', compact('dates', 'omsets'));
    }
}