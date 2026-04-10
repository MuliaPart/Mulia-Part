<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\SalesHistoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/overview', [ProductController::class, 'overview'])
    ->name('overview');
    Route::get('/sales', [SalesController::class, 'index'])
        ->name('sales.index');
    Route::get('/riwayat-produk', [SalesController::class, 'riwayat'])
        ->name('sales.riwayat');
    Route::get('/riwayat-kas', [SalesController::class, 'riwayatKas'])
        ->name('sales.riwayatKas');
    Route::get('/katalog', [ProductController::class, 'katalog'])
    ->name('katalog');
    Route::get('/kasir', [CartController::class, 'index'])->name('kasir');
    Route::get('/clear-cart', function () {session()->forget('cart'); return 'cart cleared';});
    Route::get('/sales-history', [SalesHistoryController::class,'index'])
        ->name('sales.history');
    Route::get('/cart/get',[CartController::class,'getCart'])
        ->name('cart.get');
    Route::get('/test-kasir', function () { return view('test-kasir');});




});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/products/import', [ProductController::class, 'import'])
        ->name('products.import');
    Route::post('/sales/import', [SalesController::class, 'import'])
        ->name('sales.import');
    Route::delete('/products/delete-all', [App\Http\Controllers\ProductController::class, 'deleteAll'])
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
    
    




});


require __DIR__.'/auth.php';
