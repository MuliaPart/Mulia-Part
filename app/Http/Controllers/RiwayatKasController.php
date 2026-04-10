<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class RiwayatKasController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('items')->get();

        // 🔥 GROUP PER TANGGAL
        $grouped = $transactions->groupBy(function ($trx) {
            return $trx->created_at->format('Y-m-d');
        });

        $histories = [];

        foreach ($grouped as $date => $trxGroup) {

            $omset = 0;
            $diskon = 0;
            $jasa = 0;
            $sparepart = 0;
            $hutang = 0;
            $transfer = 0;

            foreach ($trxGroup as $trx) {

                // ✔ OMSET (sudah dikurangi diskon)
                $omset += $trx->total_price;

                $trxTotalAsli = 0;

                foreach ($trx->items as $item) {

                    $hargaAsli = $item->sell_price * $item->qty;
                    $trxTotalAsli += $hargaAsli;

                    // ✔ JASA / SPAREPART
                    if (strtoupper($item->category) === 'JASA') {
                        $jasa += $hargaAsli;
                    } else {
                        $sparepart += $hargaAsli;
                    }
                }

                // ✔ DISKON PER TRANSAKSI
                $trxDiskon = $trxTotalAsli - $trx->total_price;
                $diskon += $trxDiskon;

                // ✔ PAYMENT METHOD
                if ($trx->payment_method === 'HUTANG') {
                    $hutang += $trx->total_price;
                }

                if ($trx->payment_method === 'TRANSFER') {
                    $transfer += $trx->total_price;
                }
            }

            // ✔ TOTAL CASH
            $totalCash = $omset - $hutang - $transfer;

            $histories[] = (object)[
                'tanggal' => $date,
                'total_omset' => $omset,
                'total_jasa' => $jasa,
                'total_sparepart' => $sparepart - $diskon,
                'total_diskon' => $diskon,
                'total_hutang' => $hutang,
                'total_transfer' => $transfer,
                'total_cash' => $totalCash,
            ];
        }

        // 🔥 URUT TERBARU
        $histories = collect($histories)->sortByDesc('tanggal');

        return view('sales.riwayat-kas', compact('histories'));
    }
}