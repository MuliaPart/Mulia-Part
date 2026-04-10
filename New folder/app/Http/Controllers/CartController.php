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

        $product = Product::where('code', $request->code)->first();

        if (!$product) {

            return response()->json([
                'error' => 'Produk tidak ditemukan'
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
            'success' => true
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
        
        $cart = session()->get('cart', []);

        if(empty($cart)){
            return back()->with('error','Keranjang kosong');
        }

        DB::beginTransaction();

        try{

            $transaction = Transaction::create([
                'invoice' => 'INV'.date('YmdHis'),
                'customer_name' => $request->customer_name ?? 'Umum',
                'customer_type' => $request->customer_type ?? 'non_member',
                'payment_method' => $request->payment_method,
                'total_items' => count($cart),
                'total_price' => 0
            ]);

            $total = 0;

            foreach($cart as $item){

                $sell_price = $item['sell_price'] ?? $item['price'] ?? 0;
                $cost_price = $item['cost_price'] ?? 0;
                $category   = $item['category'] ?? '-';

                // diskon per item
                $discount = $item['discount'] ?? 0;

                if(str_contains($discount,'%')){

                    $percent = (int) str_replace('%','',$discount);

                    $discount = ($sell_price * $item['qty']) * $percent / 100;

                }

                $subtotal = ($sell_price * $item['qty']) - $discount;

                TransactionItem::create([

                    'transaction_id' => $transaction->id,
                    'category'   => $category,
                    'code'       => $item['code'] ?? '-',
                    'name'       => $item['name'] ?? '-',

                    'cost_price' => $cost_price,
                    'sell_price' => $sell_price,

                    'qty'        => $item['qty'],
                    'discount'   => $discount,

                    'total_price'=> $subtotal

                ]);

                $total += $subtotal;
            }
            /* DISKON TOTAL */
            $total = $total - ($request->total_discount ?? 0);

            $transaction->update([
                'total_price' => $total
            ]);

            DB::commit();

            session()->forget('cart');

            return redirect()->route('test-kasir')
                ->with('success','Transaksi berhasil');

        }
        catch(\Exception $e){

            DB::rollBack();

            return redirect()->route('kasir')
                ->with('error', $e->getMessage());

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

        $product = Product::where('code', $request->code)->first();

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
}