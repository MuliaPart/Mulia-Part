<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\DailySummary;
use App\Models\CashHistory;
use Carbon\Carbon;
use App\Models\TransactionItem;



class SalesController extends Controller
{
    public function index()
    {
        $sales = Sale::latest()->get();

        $totalOmset = Sale::sum('total_harga_jual');
        $totalJasa = Sale::where('category', 'JASA')
                            ->sum('total_harga_jual');

        $totalSparepart = Sale::where('category', '!=', 'JASA')
                                ->sum('total_harga_jual');

        $summary = DailySummary::latest()->first();

        $totalHutang = $summary->total_hutang ?? 0;
        $totalTransfer = $summary->total_transfer ?? 0;
        $totalDiskon = $summary->total_diskon ?? 0;

        $totalOmsetAfterDiskon = $totalOmset - $totalDiskon;
        $totalSparepartAfterDiskon = $totalSparepart - $totalDiskon;

        $totalCash = $totalOmsetAfterDiskon - $totalHutang - $totalTransfer;

        return view('sales.index', compact(
            'sales',
            'totalOmset',
            'totalJasa',
            'totalSparepart',
            'totalSparepartAfterDiskon',
            'totalOmsetAfterDiskon',
            'totalHutang',
            'totalTransfer',
            'totalDiskon',
            'totalCash'
        ));
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        $header = fgetcsv($file, 0, ';');

        while (($row = fgetcsv($file, 0, ';')) !== false) {

            if (count(array_filter($row)) === 0) {
                continue;
            }

            Sale::create([
                'no' => isset($row[0]) ? (int) trim($row[0]) : 0,
                'category' => trim($row[1] ?? ''),
                'code' => trim($row[2] ?? ''),
                'name' => trim($row[3] ?? ''),
                'stock_awal' => (int) ($row[4] ?? 0),
                'terjual' => (int) ($row[5] ?? 0),
                'stock_akhir' => (int) ($row[6] ?? 0),
                'total_harga_pokok' => (int) ($row[7] ?? 0),
                'total_harga_jual' => (int) ($row[8] ?? 0),
                'total_laba' => (int) ($row[9] ?? 0),
            ]);
        }

        fclose($file);

        return back()->with('success', 'Import data penjualan berhasil');
    }
    public function deleteAll()
    {
        Sale::truncate(); // hapus semua data
        DailySummary::truncate();

        return redirect()->route('sales.index')
            ->with('success', 'Semua data penjualan berhasil dihapus');
    }
    public function riwayat()
    {
        $sales = SalesHistory::orderBy('created_at', 'desc')->paginate(100);

        return view('sales.riwayat', compact('sales'));
    }

    public function deleteAllRiwayat()
    {
        SalesHistory::truncate();

        return redirect()->route('sales.riwayat')
            ->with('success', 'Semua riwayat produk berhasil dihapus');
    }

    public function simpanKeRiwayat()
    {
        // ✅ CEK DULU apakah hari ini sudah ditutup

        $sales = Sale::all();

        if ($sales->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk disimpan.');
        }

        // ======================
        // 1️⃣ Simpan Riwayat Produk
        // ======================
        foreach ($sales as $sale) {
            SalesHistory::create([
                'category' => $sale->category,
                'code' => $sale->code,
                'name' => $sale->name,
                'terjual' => $sale->terjual,
                'total_harga_pokok' => $sale->total_harga_pokok,
                'total_harga_jual' => $sale->total_harga_jual,
                'total_laba' => $sale->total_laba,
            ]);
        }

        // ======================
        // 2️⃣ Hitung Ringkasan Kartu
        // ======================
        $totalOmset = Sale::sum('total_harga_jual');

        $totalJasa = Sale::where('category', 'JASA')
                            ->sum('total_harga_jual');

        $totalSparepart = Sale::where('category', '!=', 'JASA')
                                ->sum('total_harga_jual');

        $summary = DailySummary::latest()->first();

        $totalHutang = $summary->total_hutang ?? 0;
        $totalTransfer = $summary->total_transfer ?? 0;
        $totalDiskon = $summary->total_diskon ?? 0;

        $totalCash = ($totalOmset - $totalDiskon) - $totalHutang - $totalTransfer;

        // ======================
        // 3️⃣ Simpan Riwayat Kas
        // ======================
        CashHistory::create([
            'total_omset' => $totalOmset,
            'total_jasa' => $totalJasa,
            'total_sparepart' => $totalSparepart,
            'total_diskon' => $totalDiskon,
            'total_hutang' => $totalHutang,
            'total_transfer' => $totalTransfer,
            'total_cash' => $totalCash,
            'tanggal' => Carbon::today(),
        ]);

        // ======================
        // 4️⃣ Reset Semua
        // ======================
        Sale::truncate();
        DailySummary::truncate();

        return back()->with('success', 'Data penjualan & ringkasan berhasil disimpan!');
    }
    public function deleteAllRiwayatKas()
    {
        CashHistory::truncate();

        return redirect()->route('sales.riwayatKas')
            ->with('success', 'Semua riwayat produk berhasil dihapus');
    }
    public function updateHutang(Request $request)
    {
        $request->validate([
            'total_hutang' => 'required|numeric'
        ]);

        $summary = DailySummary::first();

        if (!$summary) {
            $summary = DailySummary::create([
                'total_hutang' => $request->total_hutang,
                'total_transfer' => 0
            ]);
        } else {
            $summary->update([
                'total_hutang' => $request->total_hutang
            ]);
        }

        return response()->json(['success' => true]);
    }
    public function updateTransfer(Request $request)
    {
        $request->validate([
            'total_transfer' => 'required|numeric'
        ]);

        $summary = DailySummary::first();

        if (!$summary) {
            $summary = DailySummary::create([
                'total_hutang' => 0,
                'total_transfer' => $request->total_transfer
            ]);
        } else {
            $summary->update([
                'total_transfer' => $request->total_transfer
            ]);
        }

        return response()->json(['success' => true]);
    }
    public function updateDiskon(Request $request)
    {
        $request->validate([
            'total_diskon' => 'required|numeric|min:0'
        ]);

        $summary = DailySummary::latest()->first();

        if (!$summary) {
            $summary = DailySummary::create([
                'total_hutang' => 0,
                'total_transfer' => 0,
                'total_diskon' => $request->total_diskon
            ]);
        } else {
            $summary->update([
                'total_diskon' => $request->total_diskon
            ]);
        }

        return back();
    }

    public function riwayatKas()
    {
        $histories = CashHistory::latest()->get();

        return view('sales.riwayat-kas', compact('histories'));
    }
    

    public function history()
    {

        $sales = TransactionItem::with('transaction')
            ->orderBy('id','desc')
            ->paginate(100);

        return view('sales.history', compact('sales'));

    }


}

