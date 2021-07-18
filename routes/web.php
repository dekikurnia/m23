<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MoveItemController;
use App\Http\Controllers\RetailSaleController;
use App\Http\Controllers\WholesaleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ChangeProfileController;

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

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['permission:menu-master']], function () {
        Route::resource("suppliers", SupplierController::class);
        Route::resource("customers", CustomerController::class);
        Route::resource("items", ItemController::class);
        Route::resource("price", PriceController::class);
    });

    Route::group(['middleware' => ['permission:menu-pindah-barang']], function () {
        Route::resource('move-items', MoveItemController::class);
    });

    Route::group(['middleware' => ['permission:menu-pembelian']], function () {
        Route::group(['prefix' => 'purchases'], function () {
            Route::get('data', [PurchaseController::class, 'getPurchasesData'])->name('purchases.data');
            Route::get('debt', [PurchaseController::class, 'getPurchasesDebt'])->name('purchases.debt');
            Route::get('debt/{purchase}/edit', [PurchaseController::class, 'editDebt']);
            Route::put('debt/update/{purchase}', [PurchaseController::class, 'updateDebt'])->name('purchases.update-debt');;
            Route::get('report', [PurchaseController::class, 'getPurchasesReport'])->name('purchases.report');
            Route::get('export', [PurchaseController::class, 'export']);
        });
        Route::resource('purchases', PurchaseController::class);
    });

    Route::group(['middleware' => ['permission:menu-penjualan-gudang']], function () {
        Route::resource('warehouses', WarehouseController::class);
    });

    Route::group(['middleware' => ['permission:menu-penjualan-retail']], function () {
        Route::resource('retail-sales', RetailSaleController::class);
    });

    Route::group(['middleware' => ['permission:menu-penjualan-grosir']], function () {
        Route::resource('wholesales', WholesaleController::class);
    });

    Route::group(['middleware' => ['permission:menu-piutang-penjualan']], function () {
        Route::group(['prefix' => 'sales'], function () {
            Route::get('debt', [SaleController::class, 'getSalesDebt'])->name('sales.debt');
            Route::get('debt/{sale}/edit', [SaleController::class, 'editDebt']);
            Route::put('debt/update/{sale}', [SaleController::class, 'updateDebt'])->name('sales.update-debt');
        });
    });

    Route::group(['middleware' => ['permission:menu-laporan']], function () {
        Route::group(['prefix' => 'reports'], function () {
            Route::get('store-stock', [ReportController::class, 'getStoreStockReport'])->name('reports.store-stock');
            Route::get('warehouse-stock', [ReportController::class, 'getWarehouseStockReport'])->name('reports.warehouse-stock');
            Route::get('wholesale-summary', [ReportController::class, 'getWholesaleSummaryReport'])->name('reports.wholesale-summary');
            Route::get('retail-summary', [ReportController::class, 'getRetailSummaryReport'])->name('reports.retail-summary');
            Route::get('warehouse-summary', [ReportController::class, 'getWarehouseSummaryReport'])->name('reports.warehouse-summary');
            Route::get('store-sale', [ReportController::class, 'getStoreSaleReport'])->name('reports.store-sale');
            Route::get('warehouse-sale', [ReportController::class, 'getWarehouseSaleReport'])->name('reports.warehouse-sale');
        });
    });

    Route::group(['middleware' => ['permission:menu-manajemen-pengguna']], function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
    });

    Route::get('/harga-perdana', [PriceController::class, 'getHargaPerdana'])->name('price.perdana');
    Route::get('/harga-voucher', [PriceController::class, 'getHargaVoucher'])->name('price.voucher');
    Route::get('/ajax/suppliers/search', [PurchaseController::class, 'suppliersSearch'])->name('suppliers.search');
    Route::get('/ajax/wholesales/search', [WholesaleController::class, 'customersSearch'])->name('customers.search');
    Route::get('/purchases/items/list', [PurchaseController::class, 'itemsList'])->name('purchases.items-list');
    Route::get('/moves/items/list', [MoveItemController::class, 'itemsList'])->name('moves.items-list');
    Route::get('/retail/items/list', [RetailSaleController::class, 'itemsList'])->name('retail.items-list');

    Route::resource('sales', SaleController::class);

    Route::get('change-password', [ChangePasswordController::class, 'index'])->name('change-password.index');
    Route::post('change-password', [ChangePasswordController::class, 'store'])->name('change-password.store');
    Route::get('change-profile', [ChangeProfileController::class, 'index'])->name('change-profile.index');
    Route::post('change-profile', [ChangeProfileController::class, 'store'])->name('change-profile.store');
});
