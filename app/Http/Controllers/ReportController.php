<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Item;
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
            ->where('jenis', '=', 'grosir')
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
            ->where('jenis', '=', 'retail')
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
            ->where('jenis', '=', 'gudang')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        return view('reports.warehouse-summary', ['sales' => $sales]);
    }

    public function getStoreSaleReport(Request $request)
    {
        if (!empty($request->tanggal_mulai)) {
            $sales = Sale::with('saleDetails')
                ->whereBetween('tanggal', [$request->tanggal_mulai . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59'])
                ->orderBy('tanggal', 'desc');
        } else {
            $sales = DB::table('sales')
                ->join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                ->leftJoin('items', 'sale_details.item_id', '=', 'items.id')
                ->join('providers', 'items.provider_id', '=', 'providers.id')
                ->select(
                    'providers.nama AS nama_provider',
                    'items.nama AS nama_item',
                    DB::raw('sum(sale_details.kuantitas) AS kuantitas_retail')
                )
                ->where('jenis', 'Retail')
                ->whereDate('tanggal', '2021-06-29')
                ->groupBy('item_id')
                ->orderBy('nama_item')
                ->get();
        }
        return view('reports.store-sale', ['sales' => $sales]);
    }
}
