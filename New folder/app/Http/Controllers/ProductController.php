<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Models\Restock;


class ProductController extends Controller
{
        /**
         * Display list of products
         */
    public function index(Request $request)
    {
        $search = $request->search;
        $perPage = $request->get('per_page', 100); // ← tambah ini

        $products = Product::when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->orderBy('id', 'desc')
                ->paginate($perPage) 
                ->withQueryString();

        $totalInventoryValue = Product::sum(DB::raw('cost_price * stock'));

        return view('products.index', compact('products', 'totalInventoryValue', 'perPage'));
    }


        /**
         * Import products from Excel
         */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new \App\Imports\ProductsImport, $request->file('file'));

        return redirect()->route('products.index')
            ->with('success', 'Data berhasil diimport!');
    }
    public function deleteAll()
    {
        \App\Models\Product::truncate();

        return redirect()->route('products.index')
            ->with('success', 'Semua data produk berhasil dihapus!');
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_pokok' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        Product::updateOrCreate(
            ['code' => $request->code],
            [
                'name' => $request->name,
                'category_id' => $request->category_id,
                'cost_price' => $request->cost_price,
                'sell_price' => $request->sell_price,
                'stock' => $request->stock,
            ]
        );

        return redirect()->route('products.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }
    public function overview()
    {
        $totalProduk = Product::count();
        $totalStok = Product::sum('stock');
        $totalNilai = Product::sum(DB::raw('cost_price * stock'));
        $stokHabis = Product::where('stock', '<=', 0)->count();

        return view('overview', compact(
            'totalProduk',
            'totalStok',
            'totalNilai',
            'stokHabis'
        ));
    }
    public function sales()
    {
        return view('sales.index');
    }
    public function importSales(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file, 'r');

        if ($handle !== false) {

            // Lewati header
            $header = fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {

                // Karena seluruh baris dibungkus tanda kutip
                // maka kita pecah manual dengan ;
                $row = explode(';', $data[0]);

                DB::table('sales')->insert([
                    'no' => $row[0] ?? null,
                    'category' => $row[1] ?? null,
                    'code' => $row[2] ?? null,
                    'name' => str_replace('"', '', $row[3] ?? null),
                    'stock_awal' => !empty($row[4]) ? (int)$row[4] : 0,
                    'terjual' => !empty($row[5]) ? (int)$row[5] : 0,
                    'stock_akhir' => !empty($row[6]) ? (int)$row[6] : 0,
                    'total' => !empty($row[7]) ? (int)$row[7] : 0,
                    'harga_pokok' => !empty($row[8]) ? (int)$row[8] : 0,
                    'total_harga_jual' => !empty($row[9]) ? (int)$row[9] : 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            fclose($handle);
        }

        return redirect()->back()->with('success', 'Data penjualan berhasil diimport!');
    }

    public function katalog(Request $request)
    {
        $categories = Category::all();

        $products = Product::with('category')
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%');
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category_id', $request->category);
            })
            ->orderBy('id','desc')
            ->paginate(25)
            ->withQueryString();

        if ($request->ajax()) {
            return view('katalog.partials.product_list', compact('products', 'categories'));
        }

        return view('katalog.index', compact('products', 'categories'));
    }


    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'cost_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->only([
            'category_id',
            'name',
            'cost_price',
            'sell_price',
            'stock'
        ]);

        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('products','public');

            $data['image'] = $path;
        }
        $product->update($data);

        return redirect()->route('katalog')
            ->with('success','Produk berhasil diperbarui!');
    }

public function restock(Request $request, Product $product)
{
    $request->validate([
        'qty' => 'required|integer|min:1',
        'cost_price' => 'nullable|numeric',
        'sell_price' => 'nullable|numeric',
        'supplier' => 'nullable|string|max:255'
    ]);

    DB::transaction(function() use ($request,$product){

        // update stok produk
        $product->stock += $request->qty;

        if($request->cost_price){
            $product->cost_price = $request->cost_price;
        }

        if($request->sell_price){
            $product->sell_price = $request->sell_price;
        }

        $product->save();

        // simpan riwayat restok
        Restock::create([
            'product_id' => $product->id,
            'qty' => $request->qty,
            'cost_price' => $request->cost_price,
            'sell_price' => $request->sell_price,
            'supplier' => $request->supplier
        ]);

    });

    return back()->with('success','Restok berhasil');
}

}
