<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // =========================
        // PARAMETER FILTER
        // =========================
        $mode    = $request->mode ?? 'harian';
        $tanggal = $request->tanggal ?? $now->toDateString();
        $bulan   = $request->bulan ?? $now->month;
        $tahun   = $request->tahun ?? $now->year;

        // =========================
        // AMBIL DATA TRANSAKSI
        // =========================
        if ($mode == 'harian') {

            $transactions = Transaction::with('items')
                ->whereDate('created_at', $tanggal)
                ->get();

        } else {

            $transactions = Transaction::with('items')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->get();
        }

        // =========================
        // HITUNG KARTU
        // =========================
        $totalOmset = 0;
        $totalJasa = 0;
        $totalSparepart = 0;
        $totalTransfer = 0;
        $totalCash = 0;

        foreach ($transactions as $trx) {

            $totalOmset += $trx->total_price;

            // PAYMENT METHOD
            if ($trx->payment_method == 'TRANSFER') {
                $totalTransfer += $trx->total_price;
            }

            if ($trx->payment_method == 'CASH') {
                $totalCash += $trx->total_price;
            }

            // DETAIL ITEM
            foreach ($trx->items as $item) {

                if ($item->category == 'JASA') {
                    $totalJasa += $item->sell_price * $item->qty;
                } else {
                    $totalSparepart += $item->sell_price * $item->qty;
                }
            }
        }

        // =========================
        // DATA GRAFIK (BULANAN)
        // =========================
        $daily = Transaction::selectRaw('DATE(created_at) as tanggal, SUM(total_price) as total')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $dates = $daily->pluck('tanggal')->map(function($d){
            return Carbon::parse($d)->format('d-m');
        });

        $omsets = $daily->pluck('total');

        // =========================
        // TRANSAKSI TERAKHIR
        // =========================
        if ($mode == 'harian') {

            $lastTransactions = Transaction::whereDate('created_at', $tanggal)
                ->latest()
                ->take(4)
                ->get();

        } else {

            $lastTransactions = Transaction::whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->latest()
                ->take(4)
                ->get();
        }

        // =========================
        // RETURN VIEW
        // =========================
        return view('dashboard', compact(
            'mode',
            'tanggal',
            'bulan',
            'tahun',
            'dates',
            'omsets',
            'totalOmset',
            'totalSparepart',
            'totalJasa',
            'totalTransfer',
            'totalCash',
            'lastTransactions'
        ));
    }


    function rupiah($angka){
        return 'Rp '.number_format($angka,0,',','.');
    }
}