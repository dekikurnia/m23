<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Customer;
use DB;

class RetailSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AWAL = 'SLE';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = Sale::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $noInvoice = sprintf("%012s", abs($noUrutAkhir + 1)) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $noInvoice = sprintf("%012s", $no) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('retail_sales.index', ['noInvoice' => $noInvoice]);
    }

    public function itemsList()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->join('stocks', 'items.id', '=', 'stocks.item_id')
                ->select('items.id', 'providers.id as provider_id', 'providers.nama as nama_provider', 'items.nama', 'items.harga', 'categories.nama as nama_kategori', 'stocks.stok_toko as stok_toko')
                ->orderBy('nama_provider', 'asc')
                ->orderBy('items.nama', 'asc');

            return datatables()->of($data)
                ->editColumn('harga', function ($data) {
                    return number_format($data->harga, 0, ',', '.');
                })
                ->addIndexColumn()
                ->make(true);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal'   => 'required',
                'kuantitas.*' => 'required',
            ],
            [
                'tanggal.required'       => 'Tanggal penjualan retail wajib diisi.',
                'kuantitas.*.required'   => 'Kuantitas wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $retail = new Sale;
        $retail->invoice = $request->invoice;
        $retail->tanggal = $request->tanggal;
        $retail->pajak = $request->pajak;
        $retail->cara_bayar = "Kas";
        $retail->jenis = "Retail";
        $retail->is_lunas = 1;
        $retail->keterangan = $request->keterangan;
        $retail->user_id = $request->user()->id;
        $retail->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $retailDetails = new SaleDetail;
            $updateItems = new Item;

            $retailDetails->sale_id = $retail->id;
            $retailDetails->item_id = $request->item_id[$row];
            $retailDetails->kuantitas = $request->kuantitas[$row];
            $retailDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $retailDetails->item_id)->first();
            $newStocks->stok_toko = ($newStocks->stok_toko) - ($retailDetails->kuantitas);
            $updateItems->id = $retailDetails->item_id;

            DB::transaction(function () use ($retailDetails, $newStocks, $updateItems) {
                $retailDetails->save();
                $updateItems->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Entry penjualan retail berhasil ditambahkan.'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sale = Sale::with('customer')->findOrFail($id);

        $saleDetails = DB::table('sale_details')
            ->join('items', 'sale_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.pajak', 'sales.keterangan', 'sale_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('sale_details.kuantitas * sale_details.harga as sub_total')
            // ->selectRaw('((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga)) as total_ppn')
            ->orderBy('items.nama', 'asc')
            ->where('sale_id', $id)
            ->get();

        return view('retail_sales.edit', compact('sale', 'saleDetails'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal'   => 'required',
                'kuantitas.*' => 'required',
                'harga.*'     => 'required'
            ],
            [
                'tanggal.required'       => 'Tanggal penjualan retail wajib diisi.',
                'kuantitas.*.required'   => 'Kuantitas wajib diisi.',
                'harga.*.required'   => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        } else {
            //cari data sale yang sudah ada, lalu hapus
            //kembalikan stok ke awal
            $sale = Sale::findOrFail($id);
            $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();

            foreach ($saleDetails as $saleDetail) {
                $items = Item::where('id', $saleDetail->item_id)->get();
                $stocks = Stock::where('item_id', $saleDetail->item_id)->get();
                foreach ($stocks as $stock) {
                    $stock->stok_toko = $stock->stok_toko + $saleDetail->kuantitas;
                    foreach ($items as $item) {
                        $item->stock()->save($stock);
                        $sale->delete();
                    }
                }
            }
        }

        $retailSales = new Sale;
        $retailSales->invoice = $request->invoice;
        $retailSales->tanggal = $request->tanggal;
        $retailSales->pajak = $request->pajak;
        $retailSales->cara_bayar = "Kas";
        $retailSales->jenis = "Retail";
        $retailSales->is_lunas = 1;
        $retailSales->keterangan = $request->keterangan;
        $retailSales->user_id = $request->user()->id;
        $retailSales->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $retailDetails = new SaleDetail;
            $updateItems = new Item;

            $retailDetails->sale_id = $retailSales->id;
            $retailDetails->item_id = $request->item_id[$row];
            $retailDetails->kuantitas = $request->kuantitas[$row];
            $retailDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $retailDetails->item_id)->first();
            $newStocks->stok_toko = ($newStocks->stok_toko) - ($retailDetails->kuantitas);
            $updateItems->id = $retailDetails->item_id;

            DB::transaction(function () use ($retailDetails, $newStocks, $updateItems) {
                $retailDetails->save();
                $updateItems->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Update penjualan retail berhasil ditambahkan.'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
