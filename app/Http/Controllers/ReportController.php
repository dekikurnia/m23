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

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        if (!empty($tanggalMulai)) {
            $stocks = DB::select('SELECT p.nama AS nama_provider, i.nama as nama_item, 
            ifnull(fs.stok_toko + ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_retail, 0) - ifnull(bsds.kuantitas_grosir, 0), 0) as stok_awal,
            ifnull(mid.kuantitas_pindah,0) as kuantitas_pindah, 
            ifnull(sds.kuantitas_retail,0) 
            as kuantitas_retail, ifnull(sds.kuantitas_grosir,0) as kuantitas_grosir,
            ifnull((fs.stok_toko + ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_retail, 0) - ifnull(bsds.kuantitas_grosir, 0)) + ifnull(mid.kuantitas_pindah, 0) - ifnull(sds.kuantitas_retail, 0) - ifnull(sds.kuantitas_grosir, 0), 0) as stok_akhir
            FROM items AS i 
            JOIN providers AS p ON  i.provider_id = p.id 
            JOIN first_stocks AS fs ON  i.id = fs.item_id 
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal <  "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS bmid ON (i.id = bmid.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN sales.tanggal < "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'"
            AND jenis="Grosir" then sale_details.kuantitas else 0 end) as kuantitas_grosir,
            SUM(CASE WHEN sales.tanggal < "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" AND jenis="Retail" then sale_details.kuantitas else 0 end) as kuantitas_retail FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS bsds ON (i.id = bsds.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS mid ON (i.id = mid.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN sales.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'"
            AND jenis="Grosir" then sale_details.kuantitas else 0 end) as kuantitas_grosir,
            SUM(CASE WHEN sales.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" AND jenis="Retail" then sale_details.kuantitas else 0 end) as kuantitas_retail FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS sds ON (i.id = sds.item_id)
            ORDER BY p.nama ASC, i.nama ASC');
        } else {
            $tanggal = Carbon::today();
            $stocks =  DB::select('SELECT p.nama AS nama_provider, i.nama as nama_item, 
            ifnull(fs.stok_toko + ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_retail, 0) - ifnull(bsds.kuantitas_grosir, 0), 0) as stok_awal,
            ifnull(mid.kuantitas_pindah,0) as kuantitas_pindah, 
            ifnull(sds.kuantitas_retail,0) 
            as kuantitas_retail, ifnull(sds.kuantitas_grosir,0) as kuantitas_grosir,
            ifnull((fs.stok_toko + ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_retail, 0) - ifnull(bsds.kuantitas_grosir, 0)) + ifnull(mid.kuantitas_pindah, 0) - ifnull(sds.kuantitas_retail, 0) - ifnull(sds.kuantitas_grosir, 0), 0) as stok_akhir
            FROM items AS i 
            JOIN providers AS p ON  i.provider_id = p.id 
            JOIN first_stocks AS fs ON  i.id = fs.item_id 
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal <  "'.$tanggal.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS bmid ON (i.id = bmid.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN sales.tanggal < "'.$tanggal.'"
            AND jenis="Grosir" then sale_details.kuantitas else 0 end) as kuantitas_grosir,
            SUM(CASE WHEN sales.tanggal < "'.$tanggal.'" AND jenis="Retail" then sale_details.kuantitas else 0 end) as kuantitas_retail FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS bsds ON (i.id = bsds.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal = "'.$tanggal.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS mid ON (i.id = mid.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN sales.tanggal ="'.$tanggal.'"
            AND jenis="Grosir" then sale_details.kuantitas else 0 end) as kuantitas_grosir,
            SUM(CASE WHEN sales.tanggal = "'.$tanggal.'" AND jenis="Retail" then sale_details.kuantitas else 0 end) as kuantitas_retail FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS sds ON (i.id = sds.item_id)
            ORDER BY p.nama ASC, i.nama ASC');
        }
        return view('reports.store-stock', ['stocks' => $stocks]);
    }

    public function getWarehouseStockReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        if (!empty($tanggalMulai)) {
            $stocks = DB::select('SELECT p.nama AS nama_provider, i.nama as nama_item, 
            ifnull(fs.stok_gudang + ifnull(bpch.kuantitas_pembelian, 0) - ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_gudang, 0), 0) as stok_awal,
            ifnull(pch.kuantitas_pembelian,0) as kuantitas_pembelian,
            ifnull(mid.kuantitas_pindah,0) as kuantitas_pindah, 
            ifnull(sds.kuantitas_gudang,0) 
            as kuantitas_gudang,
            ifnull((fs.stok_gudang + ifnull(bpch.kuantitas_pembelian, 0) - ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_gudang, 0)) + ifnull(pch.kuantitas_pembelian, 0) - ifnull(mid.kuantitas_pindah, 0) - ifnull(sds.kuantitas_gudang, 0), 0) as stok_akhir
            FROM items AS i 
            JOIN providers AS p ON  i.provider_id = p.id 
            JOIN first_stocks AS fs ON  i.id = fs.item_id 
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN purchases.tanggal < "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            purchase_details.kuantitas else 0 end) as kuantitas_pembelian
            FROM purchase_details JOIN purchases ON purchase_details.purchase_id = purchases.id
            GROUP BY item_id) AS bpch ON (i.id = bpch.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal < "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS bmid ON (i.id = bmid.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN sales.tanggal < "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'"
            AND jenis="Gudang" then sale_details.kuantitas else 0 end) as kuantitas_gudang FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS bsds ON (i.id = bsds.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN purchases.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            purchase_details.kuantitas else 0 end) as kuantitas_pembelian
            FROM purchase_details JOIN purchases ON purchase_details.purchase_id = purchases.id
            GROUP BY item_id) AS pch ON (i.id = pch.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS mid ON (i.id = mid.item_id)
            LEFT JOIN  
            (SELECT item_id, SUM(CASE WHEN sales.tanggal BETWEEN "'.$tanggalMulai.'" AND "'.$tanggalAkhir.'"
            AND jenis="Gudang" then sale_details.kuantitas else 0 end) as kuantitas_gudang FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS sds ON (i.id = sds.item_id)
            ORDER BY p.nama ASC, i.nama ASC');
        } else {
            $tanggal = Carbon::today();
            $stocks = DB::select('SELECT p.nama AS nama_provider, i.nama as nama_item, 
            ifnull(fs.stok_gudang + ifnull(bpch.kuantitas_pembelian, 0) - ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_gudang, 0), 0) as stok_awal,
            ifnull(pch.kuantitas_pembelian,0) as kuantitas_pembelian, 
            ifnull(mid.kuantitas_pindah,0) as kuantitas_pindah, 
            ifnull(sds.kuantitas_gudang,0) 
            as kuantitas_gudang,
            ifnull((fs.stok_gudang + ifnull(bpch.kuantitas_pembelian, 0) - ifnull(bmid.kuantitas_pindah, 0) - ifnull(bsds.kuantitas_gudang, 0)) + ifnull(pch.kuantitas_pembelian, 0) - ifnull(mid.kuantitas_pindah, 0) - ifnull(sds.kuantitas_gudang, 0), 0) as stok_akhir
            FROM items AS i 
            JOIN providers AS p ON  i.provider_id = p.id 
            JOIN first_stocks AS fs ON  i.id = fs.item_id 
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN purchases.tanggal < "'.$tanggal.'" then
            purchase_details.kuantitas else 0 end) as kuantitas_pembelian
            FROM purchase_details JOIN purchases ON purchase_details.purchase_id = purchases.id
            GROUP BY item_id) AS bpch ON (i.id = bpch.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal < "'.$tanggal.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS bmid ON (i.id = bmid.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN sales.tanggal < "'.$tanggal.'"
            AND jenis="Gudang" then sale_details.kuantitas else 0 end) as kuantitas_gudang FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS bsds ON (i.id = bsds.item_id)
            LEFT JOIN
            (SELECT item_id, SUM(CASE WHEN purchases.tanggal = "'.$tanggal.'" then
            purchase_details.kuantitas else 0 end) as kuantitas_pembelian
            FROM purchase_details JOIN purchases ON purchase_details.purchase_id = purchases.id
            GROUP BY item_id) AS pch ON (i.id = pch.item_id)
            LEFT JOIN 
            (SELECT item_id, SUM(CASE WHEN move_items.tanggal = "'.$tanggal.'" then
            move_item_details.kuantitas else 0 end) as kuantitas_pindah
            FROM move_item_details JOIN move_items ON move_item_details.move_item_id = move_items.id
            GROUP BY item_id) AS mid ON (i.id = mid.item_id)
            LEFT JOIN  
            (SELECT item_id, SUM(CASE WHEN sales.tanggal = "'.$tanggal.'"
            AND jenis="Gudang" then sale_details.kuantitas else 0 end) as kuantitas_gudang FROM sale_details 
            JOIN sales ON sale_details.sale_id = sales.id GROUP BY item_id) AS sds ON (i.id = sds.item_id)
            ORDER BY p.nama ASC, i.nama ASC');
        }
        return view('reports.warehouse-stock', ['stocks' => $stocks]);
    }

    public function getWholesaleSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        if (!empty($tanggalMulai)) {
            $sales = Sale::with('saleDetails', 'customer')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Grosir')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } else {
            $sales = Sale::with('saleDetails', 'customer')
            ->where('tanggal', Carbon::today())
            ->where('jenis', '=', 'Grosir')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }
        
        return view('reports.wholesale-summary', ['sales' => $sales]);
    }

    public function getRetailSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        if (!empty($tanggalMulai)) {
            $sales = Sale::with('saleDetails')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Retail')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } else {
            $sales = Sale::with('saleDetails')
            ->where('tanggal', Carbon::today())
            ->where('jenis', '=', 'Retail')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }
        
        return view('reports.retail-summary', ['sales' => $sales]);
    }

    public function getWarehouseSummaryReport(Request $request)
    {

        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');

        if (!empty($tanggalMulai)) {
            $sales = Sale::with('saleDetails', 'customer')
            ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
            ->where('jenis', '=', 'Gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        } else {
            $sales = Sale::with('saleDetails', 'customer')
            ->where('tanggal', Carbon::today())
            ->where('jenis', '=', 'Gudang')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        }
       
        return view('reports.warehouse-summary', ['sales' => $sales]);
    }

    public function getStoreSaleReport(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        if (!empty($tanggalMulai)) {
            $sales = DB::table('items')
                ->leftJoin('sale_details', 'sale_details.item_id', 'items.id')
                ->leftJoin('sales', function ($join) use ($tanggalMulai, $tanggalAkhir) {
                    $join->on('sale_details.sale_id', '=', 'sales.id')->whereBetween('sales.tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59']);
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
                ->leftJoin('sale_details', 'sale_details.item_id', 'items.id')
                ->leftJoin('sales', function ($join) use ($tanggal) {
                    $join->on('sale_details.sale_id', '=', 'sales.id')->where('sales.tanggal', $tanggal);
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
                ->leftJoin('sale_details', 'sale_details.item_id', 'items.id')
                ->leftJoin('sales', function ($join) use ($tanggalMulai, $tanggalAkhir) {
                    $join->on('sale_details.sale_id', '=', 'sales.id')->whereBetween('sales.tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59']);
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
                ->leftJoin('sale_details', 'sale_details.item_id', 'items.id')
                ->leftJoin('sales', function ($join) use ($tanggal) {
                    $join->on('sale_details.sale_id', '=', 'sales.id')->where('sales.tanggal', $tanggal);
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