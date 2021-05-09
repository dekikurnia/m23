<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreatePurchaseRequest;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AWAL = 'M23';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = Purchase::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $noInvoice = sprintf("%09s", abs($noUrutAkhir + 1)) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $noInvoice = sprintf("%09s", $no) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('purchases.index', ['noInvoice' => $noInvoice]);
    }

    public function searchSuppliers(Request $request)
    {
        $keyword = $request->get('q');
        $suppliers = Supplier::where(
            "nama",
            "LIKE",
            "%$keyword%"
        )->get();
        return $suppliers;
    }

    public function listItems(Request $request)
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->join('stocks', 'items.id', '=', 'stocks.item_id')
                ->select('items.id', 'providers.id as provider_id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori')
                ->orderBy('nama_provider', 'asc')
                ->orderBy('items.nama', 'asc');

            return datatables()->of($data)
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

        $purchase = Purchase::create([
            'invoice' => $request->invoice,
            'tanggal' => $request->tanggal,
            'supplier_id' => $request->supplier_id,
            'cara_bayar' => $request->cara_bayar,
            'pajak' => $request->pajak,
            'jatuh_tempo' => $request->jatuh_tempo,
            'total' => $request->total_pembelian,
            'keterangan' => $request->keterangan,
            'user_id' => $request->user()->id,
        ]);

        foreach ($request->input('purchase_id', []) as $row) {
            $new_purchase_detail = new PurchaseDetail;
            $new_purchase_detail->purchase_id = $purchase->id;
            $new_purchase_detail->item_id = $request->item_id[$row];
            $new_purchase_detail->kuantitas = $request->kuantitas[$row];
            $new_purchase_detail->harga = $request->harga[$row];
            $new_purchase_detail->save();
        }

        return response()->json([
            'route' => route('purchases.index')
        ]);
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
        //
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
        //
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
