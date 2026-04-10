<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class RiwayatTransaksiController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('items')
            ->latest()
            ->paginate(50);

        return view('sales.riwayat-transaksi', compact('transactions'));
    }
    public function deleteByDate(Request $request)
    {
        $tanggal = $request->tanggal;

        $transactions = \App\Models\Transaction::whereDate('created_at', $tanggal)->get();

        foreach ($transactions as $trx) {
            $trx->items()->delete(); // hapus detail
            $trx->delete(); // hapus transaksi
        }

        return back()->with('success', 'Data tanggal '.$tanggal.' berhasil dihapus');
    }
    public function delete($id)
    {
        $trx = \App\Models\Transaction::findOrFail($id);

        // hapus item dulu (biar aman)
        $trx->items()->delete();

        // hapus transaksi
        $trx->delete();

        return back()->with('success', 'Transaksi berhasil dihapus');
    }
}