<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getStoreStockReport(Request $request)
    {

        return view('reports.store-stock');
    }

    public function getWholesaleSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $sales = Sale::with('saleDetails', 'customer')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Grosir')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        return view('reports.wholesale-summary', ['sales' => $sales]);
    }

    public function getRetailSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $sales = Sale::with('saleDetails')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Retail')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        return view('reports.retail-summary', ['sales' => $sales]);
    }

    public function getWarehouseSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        $sales = Sale::with('saleDetails', 'customer')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Gudang')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        return view('reports.warehouse-summary', ['sales' => $sales]);
    }

    public function getStoreSaleReport(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        if (!empty($tanggalMulai)) {
            $sales = DB::table('items')
                ->leftJoin('sale_details','sale_details.item_id','items.id')
                ->leftJoin('sales', function($join)use($tanggalMulai, $tanggalAkhir){
                    $join->on('sale_details.sale_id', '=', 'sales.id')->
                    whereBetween('sales.tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59']);
                })
                ->join('providers', 'items.provider_id', '=', 'providers.id')
                ->select(
                    'providers.nama AS nama_provider',
                    'items.nama AS nama_item',
                    DB::raw('SUM(CASE WHEN jenis="Grosir" then kuantitas else 0 end) as kuantitas_grosir'),
                    DB::raw('SUM(CASE WHEN jenis="Grosir" then (sale_details.harga * sale_details.kuantitas )else 0 end)as harga_grosir'),
                    DB::raw('SUM(CASE WHEN jenis="Retail" then kuantitas else 0 end) as kuantitas_retail'),
                    DB::raw('SUM(CASE WHEN jenis="Retail" then (sale_details.harga * sale_details.kuantitas) else 0 end ) as harga_retail'),
                )
                ->groupBy('items.id')
                ->orderBy('providers.nama')
                ->orderBy('items.nama')
                ->get();
        } else {
            $tanggal = Carbon::today();
            $sales = DB::table('items')
                ->leftJoin('sale_details','sale_details.item_id','items.id')
                ->leftJoin('sales', function($join)use($tanggal){
                    $join->on('sale_details.sale_id', '=', 'sales.id')->
                    where('sales.tanggal',$tanggal);
                })
                ->join('providers', 'items.provider_id', '=', 'providers.id')
                ->select(
                    'providers.nama AS nama_provider',
                    'items.nama AS nama_item',
                    DB::raw('SUM(CASE WHEN jenis="Grosir" then kuantitas else 0 end) as kuantitas_grosir'),
                    DB::raw('SUM(CASE WHEN jenis="Grosir" then (sale_details.harga * sale_details.kuantitas )else 0 end)as harga_grosir'),
                    DB::raw('SUM(CASE WHEN jenis="Retail" then kuantitas else 0 end) as kuantitas_retail'),
                    DB::raw('SUM(CASE WHEN jenis="Retail" then (sale_details.harga * sale_details.kuantitas) else 0 end ) as harga_retail')
                )
                ->groupBy('items.id')
                ->orderBy('providers.nama')
                ->orderBy('items.nama')
                ->get();
        }
        return view('reports.store-sale', ['sales' => $sales]);
    }

    public function getWarehouseSaleReport(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        if (!empty($tanggalMulai)) {
            $sales = DB::table('items')
                ->leftJoin('sale_details','sale_details.item_id','items.id')
                ->leftJoin('sales', function($join)use($tanggalMulai, $tanggalAkhir){
                    $join->on('sale_details.sale_id', '=', 'sales.id')->
                    whereBetween('sales.tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59']);
                })
                ->join('providers', 'items.provider_id', '=', 'providers.id')
                ->select(
                    'providers.nama AS nama_provider',
                    'items.nama AS nama_item',
                    DB::raw('SUM(CASE WHEN jenis="Gudang" then kuantitas else 0 end) as kuantitas_gudang'),
                    DB::raw('SUM(CASE WHEN jenis="Gudang" then (sale_details.harga * sale_details.kuantitas )else 0 end)as harga_gudang'),
                )
                ->groupBy('items.id')
                ->orderBy('providers.nama')
                ->orderBy('items.nama')
                ->get();
        } else {
            $tanggal = Carbon::today();
            $sales = DB::table('items')
                ->leftJoin('sale_details','sale_details.item_id','items.id')
                ->leftJoin('sales', function($join)use($tanggal){
                    $join->on('sale_details.sale_id', '=', 'sales.id')->
                    where('sales.tanggal',$tanggal);
                })
                ->join('providers', 'items.provider_id', '=', 'providers.id')
                ->select(
                    'providers.nama AS nama_provider',
                    'items.nama AS nama_item',
                    DB::raw('SUM(CASE WHEN jenis="Gudang" then kuantitas else 0 end) as kuantitas_gudang'),
                    DB::raw('SUM(CASE WHEN jenis="Gudang" then (sale_details.harga * sale_details.kuantitas)else 0 end)as harga_gudang'),
                )
                ->groupBy('items.id')
                ->orderBy('providers.nama')
                ->orderBy('items.nama')
                ->get();
        }
        return view('reports.warehouse-sale', ['sales' => $sales]);
    }
}
