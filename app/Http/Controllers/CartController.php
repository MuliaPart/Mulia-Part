<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CashierSale;
use App\Models\TransactionItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    public function add(Request $request)
    {

        $product = Product::find($request->product_id);

        if($product->stock <= 0){
            return response()->json([
                'error' => 'Stok produk kosong'
            ], 400);
        }
        if(!$product){

            return response()->json([
                'error' => 'Produk tidak ditemukan'
            ],404);

        }

        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])){

            $cart[$product->id]['qty']++;

        }else{

            $cart[$product->id] = [

                'id'         => $product->id,
                'code'       => $product->code,
                'name'       => $product->name,

                'category'   => $product->category->name ?? '-',

                'cost_price' => $product->cost_price,
                'sell_price' => $product->sell_price,

                'price'      => $product->sell_price,
                'image'      => $product->image,

                'qty'        => 1,
                'discount'   => 0

            ];

        }

        session()->put('cart',$cart);

        return response()->json([
            'success' => true
        ]);

    }

    public function index(Request $request)
    {
        $products = Product::when($request->search,function($q) use ($request){

            $q->where('name','like','%'.$request->search.'%')
            ->orWhere('code','like','%'.$request->search.'%');

        })->orderBy('name')->limit(40)->get();

        return view('kasir.index', compact('products'));
    }
    public function addByCode(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $inputCode = trim($request->code);

        \Log::info('SCAN CODE: ' . $inputCode);

        // 🔥 HANYA CARI BERDASARKAN CODE (TIDAK NGACO)
        $product = Product::where('code', $inputCode)->first();

        if ($product->stock <= 0) {
            return response()->json([
                'error' => 'Stok produk kosong: '.$product->name
            ], 400);
        }
        if (!$product) {
            return response()->json([
                'error' => 'Produk tidak ditemukan: ' . $inputCode
            ], 404);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty']++;
        } else {
            $cart[$product->id] = [
                'id'         => $product->id,
                'code'       => $product->code,
                'name'       => $product->name,
                'category'   => $product->category->name ?? '-',
                'cost_price' => $product->cost_price,
                'sell_price' => $product->sell_price,
                'price'      => $product->sell_price,
                'image'      => $product->image,
                'qty'        => 1,
                'discount'   => 0
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'cart'    => array_values($cart)
        ]);
    }
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {

            unset($cart[$id]);

            session()->put('cart', $cart);
        }

        return back();
    }
    public function clear()
    {
        session()->forget('cart');
        session()->forget('selected_product');

        return back();
    }
  public function updateQty(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'qty' => 'required|numeric|min:1'
        ]);

        $cart = session()->get('cart', []);

        if(isset($cart[$request->id])){

            $cart[$request->id]['qty'] = $request->qty;

            session()->put('cart', $cart);

        }

        return response()->json([
            'success' => true,
            'cart' => array_values(session()->get('cart', []))
        ]);

    }


    public function deleteAll()
    {

        TransactionItem::truncate();

        return redirect()
            ->route('sales.history')
            ->with('success', 'Semua riwayat kasir berhasil dihapus');

    }

    public function bayar(Request $request)
    {
        try {

            $cart = session()->get('cart', []);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang kosong'
                ]);
            }

            DB::beginTransaction();

            $transaction = Transaction::create([
                'invoice' => 'MC' . date('YmdHis'),
                'customer_name' => $request->customer_name ?? 'Umum',
                'customer_type' => $request->customer_type ?? 'non_member',
                'payment_method' => $request->payment_method,
                'total_items' => count($cart),
                'total_price' => 0,
                'note' => $request->note
            ]);

            $total = 0;

            foreach ($cart as $item) {

                // 🔥 AMBIL PRODUK
                $product = Product::find($item['id']);

                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan: ' . ($item['name'] ?? '-'));
                }

                // 🔥 CEK STOK (ANTI MINUS)
                if ($product->stock < $item['qty']) {
                    throw new \Exception('Stok tidak cukup untuk: ' . $product->name);
                }

                $sell_price = $item['sell_price'] ?? $item['price'] ?? 0;
                $cost_price = $item['cost_price'] ?? 0;
                $category   = $item['category'] ?? '-';
                $discount   = $item['discount'] ?? 0;

                // 🔥 HANDLE DISKON %
                if (is_string($discount) && str_contains($discount, '%')) {
                    $percent = (int) str_replace('%', '', $discount);
                    $discount = ($sell_price * $item['qty']) * $percent / 100;
                }

                $subtotal = ($sell_price * $item['qty']) - $discount;

                // 🔥 SIMPAN ITEM
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'category'       => $category,
                    'code'           => $item['code'] ?? '-',
                    'name'           => $item['name'] ?? '-',
                    'cost_price'     => $cost_price,
                    'sell_price'     => $sell_price,
                    'qty'            => $item['qty'],
                    'discount'       => $discount,
                    'total_price'    => $subtotal,
                ]);

                // 🔥 KURANGI STOK
                $product->decrement('stock', $item['qty']);

                $total += $subtotal;
            }

            // 🔥 DISKON TOTAL
            $totalDiscount = $request->total_discount ?? 0;

            if (is_string($totalDiscount) && str_contains($totalDiscount, '%')) {
                $percent = (int) str_replace('%', '', $totalDiscount);
                $totalDiscount = $total * $percent / 100;
            }

            $total = $total - $totalDiscount;

            // 🔥 UPDATE TOTAL TRANSAKSI
            $transaction->update([
                'total_price' => $total
            ]);

            DB::commit();

            session()->forget('cart');

            return response()->json([
                'success' => true,
                'invoice' => $transaction->invoice,
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error('ERROR BAYAR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function updateDiscount(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'discount' => 'nullable'
        ]);

        $cart = session()->get('cart', []);

        if(isset($cart[$request->id])){

            $cart[$request->id]['discount'] = $request->discount;

            session()->put('cart',$cart);

        }

        return response()->json([
            'success'=>true
        ]);

    }
    public function getCart()
    {

        $cart = session()->get('cart', []);

        return response()->json([
            'cart' => array_values($cart)
        ]);

    }
    public function getProductByCode(Request $request)
    {

        $product = Product::where('code', trim($request->code))->first();

        if(!$product){
            return response()->json([
                'error' => 'Barang tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'product' => $product
        ]);

    }
        public function nota($id)
    {
        $trx = \App\Models\Transaction::with('items')->findOrFail($id);

        return view('kasir.nota', compact('trx'));
    }
}

