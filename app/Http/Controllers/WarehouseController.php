<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleDetail;
use App\Models\Item;
use App\Models\Stock;
use DB;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AWAL = 'SLE-W';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = Sale::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $noInvoice = sprintf("%012s", abs($noUrutAkhir + 1)) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $noInvoice = sprintf("%012s", $no) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('warehouses.index', ['noInvoice' => $noInvoice]);
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
                'kuantitas'     => 'required',
                'harga'         => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal penjualan gudang wajib diisi.',
                'customer_id.required'  => 'Customer wajib diisi.',
                'cara_bayar.in'         => 'Cara bayar wajib diisi.',
                'item_id.required'      => 'Barang belum dipilih.',
                'kuantitas.required'    => 'Kuantitas wajib diisi.',
                'harga.required'        => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $warehouses = new Sale;
        $warehouses->invoice = $request->invoice;
        $warehouses->tanggal = $request->tanggal;
        $warehouses->customer_id = $request->customer_id;
        $warehouses->cara_bayar = $request->cara_bayar;
        $warehouses->pajak = $request->pajak;
        $warehouses->jatuh_tempo = $request->jatuh_tempo;
        if ($warehouses->cara_bayar == 'Kredit') {
            $warehouses->is_lunas = false;
        } else {
            $warehouses->is_lunas = true;
        }
        $warehouses->jenis = "Gudang";
        $warehouses->keterangan = $request->keterangan;
        $warehouses->user_id = $request->user()->id;
        $warehouses->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $warehouseDetails= new SaleDetail;
            $items = new Item;

            $warehouseDetails->sale_id = $warehouses->id;
            $warehouseDetails->item_id = $request->item_id[$row];
            $warehouseDetails->kuantitas = $request->kuantitas[$row];
            $warehouseDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $warehouseDetails->item_id)->first();
            $newStocks->stok_gudang = $newStocks->stok_gudang - $warehouseDetails->kuantitas;
            $items->id = $warehouseDetails->item_id;

            DB::transaction(function () use ($warehouseDetails, $newStocks, $items) {
                $warehouseDetails->save();
                $items->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Entry penjualan gudang berhasil ditambahkan.'), 200);
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

        return view('warehouses.edit', compact('sale', 'saleDetails', 'customer'));
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
                $stock->stok_gudang = $stock->stok_gudang + $saleDetail->kuantitas;
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
                'kuantitas'     => 'required',
                'harga'         => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal penjualan gudang wajib diisi.',
                'customer_id.required'  => 'Customer wajib diisi.',
                'cara_bayar.in'         => 'Cara bayar wajib diisi.',
                'item_id.required'      => 'Barang belum dipilih.',
                'kuantitas.required'    => 'Kuantitas wajib diisi.',
                'harga.required'        => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $warehouses = new Sale;
        $warehouses->invoice = $request->invoice;
        $warehouses->tanggal = $request->tanggal;
        $warehouses->customer_id = $request->customer_id;
        $warehouses->cara_bayar = $request->cara_bayar;
        $warehouses->pajak = $request->pajak;
        $warehouses->jatuh_tempo = $request->jatuh_tempo;
        if ($warehouses->cara_bayar == 'Kredit') {
            $warehouses->is_lunas = false;
        } else {
            $warehouses->is_lunas = true;
        }
        $warehouses->jenis = "Gudang";
        $warehouses->keterangan = $request->keterangan;
        $warehouses->user_id = $request->user()->id;
        $warehouses->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $warehouseDetails= new SaleDetail;
            $items = new Item;

            $warehouseDetails->sale_id = $warehouses->id;
            $warehouseDetails->item_id = $request->item_id[$row];
            $warehouseDetails->kuantitas = $request->kuantitas[$row];
            $warehouseDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $warehouseDetails->item_id)->first();
            $newStocks->stok_gudang = $newStocks->stok_gudang - $warehouseDetails->kuantitas;
            $items->id = $warehouseDetails->item_id;

            DB::transaction(function () use ($warehouseDetails, $newStocks, $items) {
                $warehouseDetails->save();
                $items->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Update penjualan gudang berhasil ditambahkan.'), 200);
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
