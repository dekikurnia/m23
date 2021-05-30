<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource("suppliers", SupplierController::class);
Route::resource("customers", CustomerController::class);
Route::resource("items", ItemController::class);
Route::resource("price", PriceController::class);

Route::get('/harga-perdana', [PriceController::class, 'getHargaPerdana'])->name('price.perdana');
Route::get('/harga-voucher', [PriceController::class, 'getHargaVoucher'])->name('price.voucher');

Route::get('/ajax/suppliers/search', [PurchaseController::class, 'suppliersSearch'])->name('suppliers.search');
Route::get('/ajax/items/list', [PurchaseController::class, 'itemsList'])->name('items.list');

Route::group(['prefix'=>'purchases'],function(){
    Route::get('data', [PurchaseController::class, 'getPurchasesData'])->name('purchases.data');
    Route::get('debt', [PurchaseController::class, 'getPurchasesDebt'])->name('purchases.debt');
    Route::get('debt/{purchase}/edit', [PurchaseController::class, 'editDebt']);
    Route::put('debt/update/{purchase}', [PurchaseController::class, 'updateDebt'])->name('purchases.update-debt');;
    Route::get('report', [PurchaseController::class, 'getPurchasesReport'])->name('purchases.report');
    Route::get('export', [PurchaseController::class, 'export']);
});

Route::resource( 'purchases', PurchaseController::class);