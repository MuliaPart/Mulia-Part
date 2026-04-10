<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\RiwayatKasirController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\RiwayatKasController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/overview', [ProductController::class, 'overview'])
        ->name('overview');
    Route::get('/sales', [SalesController::class, 'index'])
        ->name('sales.index');
    Route::get('/riwayat-produk', [SalesController::class, 'riwayat'])
        ->name('sales.riwayat');
    Route::get('/riwayat-kas', [RiwayatKasController::class, 'index'])
        ->name('riwayat.kas');
    Route::get('/katalog', [ProductController::class, 'katalog'])
        ->name('katalog');
    Route::get('/kasir', [CartController::class, 'index'])->name('kasir');
    Route::get('/clear-cart', function () {session()->forget('cart'); return 'cart cleared';});
    Route::get('/riwayat-kasir', [RiwayatKasirController::class,'index'])
        ->name('riwayat.kasir');
    Route::get('/cart/get',[CartController::class,'getCart'])
        ->name('cart.get');
    Route::get('/test-kasir', function () { return view('test-kasir');})
        ->name('test-kasir');
    Route::get('/riwayat-transaksi', [RiwayatTransaksiController::class,'index'])
        ->name('riwayat.transaksi');
    Route::get('/laporan-bulanan', [LaporanController::class, 'bulanan'])
        ->name('laporan.bulanan');
    Route::get('/transaction/{id}', function($id){
            return \App\Models\Transaction::with('items')->findOrFail($id);
        });
    Route::get('/print-nota/{id}', function($id){
        $trx = \App\Models\Transaction::with('items')->findOrFail($id);
        return view('print.nota', compact('trx'));
        });
    Route::get('/transaction/{id}', function($id){
            $trx = \App\Models\Transaction::with('items')->findOrFail($id);
            return response()->json($trx);
        });
    Route::get('/nota/{id}', [CartController::class, 'nota']);
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/generate-code/{category}', [ProductController::class, 'generateCode']);



});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/products/import', [ProductController::class, 'import'])
        ->name('products.import');
    Route::post('/sales/import', [SalesController::class, 'import'])
        ->name('sales.import');
    Route::post('/products/delete-all', [ProductController::class, 'deleteAll'])
        ->name('products.deleteAll');
    Route::delete('/sales/delete-all', [SalesController::class, 'deleteAll'])
        ->name('sales.deleteAll');
    Route::delete('/riwayat-produk/delete-all', [SalesController::class, 'deleteAllRiwayat'])
         ->name('sales.riwayat.deleteAll');
    Route::delete('/riwayat-kas/delete-all', [SalesController::class, 'deleteAllRiwayatKas'])
         ->name('sales.riwayatKas.deleteAll');
    Route::post('/sales/simpan-riwayat', [SalesController::class, 'simpanKeRiwayat']
        )->name('sales.simpan.riwayat');
    Route::post('/update-hutang', [SalesController::class, 'updateHutang']);
    Route::post('/update-transfer', [SalesController::class, 'updateTransfer']);
    Route::post('/update-diskon', [SalesController::class, 'updateDiskon'])
        ->name('daily-summary.updateDiskon');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/products/{product}/restock',[ProductController::class,'restock'])
        ->name('products.restock');
    Route::post('/cart/add-code', [CartController::class, 'addByCode'])
        ->name('cart.add.code');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::post('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');
    Route::post('/cart/update-qty', [CartController::class, 'updateQty'])
        ->name('cart.update.qty');
    Route::post('/kasir/bayar', [CartController::class,'bayar'])
        ->name('kasir.bayar');
    Route::post('/cart/update-discount',[CartController::class,'updateDiscount'])
        ->name('cart.update.discount');
    Route::post('/test-product', [CartController::class,'getProductByCode'])
    ->name('test.product');
    Route::post('/riwayat-transaksi/delete-by-date', [RiwayatTransaksiController::class, 'deleteByDate'])
        ->name('riwayat.transaksi.deleteByDate');
    Route::delete('/riwayat-transaksi/{id}', [RiwayatTransaksiController::class, 'delete'])
    ->name('riwayat.transaksi.delete');
    Route::delete('/restock/{id}', [RestockController::class, 'destroy'])->name('restock.delete');
    Route::delete('/transaction-item/{id}', [TransactionItemController::class, 'destroy'])->name('transaction.item.delete');
    
    




});


require __DIR__.'/auth.php';
