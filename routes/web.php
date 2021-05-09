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

Route::get('/harga-perdana', [PriceController::class, 'getHargaPerdana']);
Route::get('/harga-voucher', [PriceController::class, 'getHargaVoucher']);

Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases');
Route::get('/ajax/suppliers/search', [PurchaseController::class, 'searchSuppliers'])->name('search-suppliers');
Route::get('/ajax/list-items', [PurchaseController::class, 'listItems'])->name('list-items');
Route::resource("purchases", PurchaseController::class);
