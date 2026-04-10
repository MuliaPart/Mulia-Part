<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function bulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        // =========================
        // TOTAL OMSET
        // =========================
        $totalOmset = Transaction::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->sum('total_price');

        // =========================
        // JASA
        // =========================
        $totalJasa = TransactionItem::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('category', 'JASA')
            ->sum('total_price');

        // =========================
        // SPAREPART
        // =========================
        $totalSparepart = TransactionItem::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('category', '!=', 'JASA')
            ->sum('total_price');

        // =========================
        // MODAL
        // =========================
        $totalModal = TransactionItem::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->select(DB::raw('SUM(cost_price * qty) as modal'))
            ->value('modal') ?? 0;

        // =========================
        // MODAL SPAREPART
        // =========================
        $modalSparepart = TransactionItem::whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('category', '!=', 'JASA')
            ->select(DB::raw('SUM(cost_price * qty) as modal'))
            ->value('modal') ?? 0;

        // =========================
        // LABA
        // =========================
        $labaSparepart = $totalSparepart - $modalSparepart;
        $labaJasa      = $totalJasa;
        $labaTotal     = $labaSparepart + $labaJasa;

        return view('laporan.bulanan', compact(
            'bulan',
            'tahun',
            'totalOmset',
            'totalJasa',
            'totalSparepart',
            'totalModal',
            'labaSparepart',
            'labaJasa',
            'labaTotal'
        ));
    }
}