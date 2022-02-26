<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleDetail;
use App\Models\Item;
use App\Models\Stock;
use DB;

class WholesaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AWAL = 'SLE-G';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = Sale::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $noInvoice = sprintf("%012s", abs($noUrutAkhir + 1)) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $noInvoice = sprintf("%012s", $no) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('wholesales.index', ['noInvoice' => $noInvoice]);
    }

    public function itemsList()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->join('stocks', 'items.id', '=', 'stocks.item_id')
                ->select('items.id', 'providers.id as provider_id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_toko as stok_toko')
                ->orderBy('nama_provider', 'asc')
                ->orderBy('items.nama', 'asc');
        }
    }

    public function customersSearch(Request $request)
    {
        $keyword = $request->get('q');
        $customers = Customer::where(
            "nama",
            "LIKE",
            "%$keyword%"
        )->get();
        return $customers;
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
                'tanggal'       => 'required',
                'customer_id'   => 'required',
                'cara_bayar'    => 'required|in:Kas,Kredit,Transfer',
                'item_id'       => 'required',
                'kuantitas[]'     => 'required',
                'harga[]'         => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal penjualan grosir wajib diisi.',
                'customer_id.required'  => 'Customer wajib diisi.',
                'cara_bayar.in'         => 'Cara bayar wajib diisi.',
                'item_id.required'      => 'Barang belum dipilih.',
                'kuantitas[].required'    => 'Kuantitas wajib diisi.',
                'harga[].required'        => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $wholesales = new Sale;
        $wholesales->invoice = $request->invoice;
        $wholesales->tanggal = $request->tanggal;
        $wholesales->customer_id = $request->customer_id;
        $wholesales->cara_bayar = $request->cara_bayar;
        $wholesales->pajak = $request->pajak;
        $wholesales->jatuh_tempo = $request->jatuh_tempo;
        if ($wholesales->cara_bayar == 'Kredit') {
            $wholesales->is_lunas = false;
        } else {
            $wholesales->is_lunas = true;
        }
        $wholesales->jenis = "Grosir";
        $wholesales->keterangan = $request->keterangan;
        $wholesales->user_id = $request->user()->id;
        $wholesales->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $wholesaleDetails = new SaleDetail;
            $updateItems = new Item;

            $wholesaleDetails->sale_id = $wholesales->id;
            $wholesaleDetails->item_id = $request->item_id[$row];
            $wholesaleDetails->kuantitas = $request->kuantitas[$row];
            $wholesaleDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $wholesaleDetails->item_id)->first();
            $newStocks->stok_toko = ($newStocks->stok_toko) - ($wholesaleDetails->kuantitas);
            $updateItems->id = $wholesaleDetails->item_id;
            try {
                DB::transaction(function () use ($wholesaleDetails, $newStocks, $updateItems) {
                    $wholesaleDetails->save();
                    $updateItems->stock()->save($newStocks);
                });
            } catch (\Throwable $e) {
                return $e->getMessage();
            }
        }

        return response()->json(array('status' => 'success', 'msg' => 'Entry penjualan grosir berhasil ditambahkan.'), 200);
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
        $customer = Customer::all();

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

        return view('wholesales.edit', compact('sale', 'saleDetails', 'customer'));
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
        $sale = Sale::findOrFail($id);
        $saleDetails = SaleDetail::where('sale_id', $sale->id)->get();

        foreach ($saleDetails as $saleDetail) {
            $items = Item::where('id', $saleDetail->item_id)->get();
            $stocks = Stock::where('item_id', $saleDetail->item_id)->get();
            foreach ($stocks as $stock) {
                $stock->stok_toko = ($stock->stok_toko) + ($saleDetail->kuantitas);
                foreach ($items as $item) {
                    $item->stock()->save($stock);
                    $sale->delete();
                }
            }
        }
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal'       => 'required',
                'customer_id'   => 'required',
                'cara_bayar'    => 'required|in:Kas,Kredit,Transfer',
                'item_id'       => 'required',
                'kuantitas[]'     => 'required',
                'harga[]'         => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal penjualan grosir wajib diisi.',
                'customer_id.required'  => 'Customer wajib diisi.',
                'cara_bayar.in'         => 'Cara bayar wajib diisi.',
                'item_id.required'      => 'Barang belum dipilih.',
                'kuantitas[].required'    => 'Kuantitas wajib diisi.',
                'harga[].required'        => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $wholesales = new Sale;
        $wholesales->invoice = $request->invoice;
        $wholesales->tanggal = $request->tanggal;
        $wholesales->customer_id = $request->customer_id;
        $wholesales->cara_bayar = $request->cara_bayar;
        $wholesales->pajak = $request->pajak;
        $wholesales->jatuh_tempo = $request->jatuh_tempo;
        if ($wholesales->cara_bayar == 'Kredit') {
            $wholesales->is_lunas = false;
        } else {
            $wholesales->is_lunas = true;
        }
        $wholesales->jenis = "Grosir";
        $wholesales->keterangan = $request->keterangan;
        $wholesales->user_id = $request->user()->id;
        $wholesales->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $wholesaleDetails = new SaleDetail;
            $items = new Item;

            $wholesaleDetails->sale_id = $wholesales->id;
            $wholesaleDetails->item_id = $request->item_id[$row];
            $wholesaleDetails->kuantitas = $request->kuantitas[$row];
            $wholesaleDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $wholesaleDetails->item_id)->first();
            $newStocks->stok_toko = ($newStocks->stok_toko) - ($wholesaleDetails->kuantitas);
            $items->id = $wholesaleDetails->item_id;

            DB::transaction(function () use ($wholesaleDetails, $newStocks, $items) {
                $wholesaleDetails->save();
                $items->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Update penjualan grosir berhasil ditambahkan.'), 200);
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
