<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoveItem;
use App\Models\MoveItemDetail;
use App\Models\Item;
use App\Models\Stock;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;

class MoveItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->tanggal_mulai)) {
                $moveItems = MoveItem::orderBy('move_items.tanggal', 'desc')
                    ->whereBetween('tanggal', array($request->tanggal_mulai, $request->tanggal_akhir));
            } else {
                $moveItems = MoveItem::orderBy('move_items.tanggal', 'desc');
            }
            return datatables()->of($moveItems)
                ->addColumn('action', function ($moveItems) {
                    return '<a href="/move-items/' . $moveItems->id . '" class="btn btn-sm"
                style="background-color:transparent;">
                <i class="fa fa-eye"></i></a>';
                })
                ->editColumn('tanggal', function ($moveItems) {
                    return Carbon::parse($moveItems->tanggal)->translatedFormat('d/m/Y');
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('move_items.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $AWAL = 'PB';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = MoveItem::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $nomor = sprintf($AWAL . '/' . "%012s", abs($noUrutAkhir + 1)) . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $nomor = sprintf($AWAL . '/' . "%012s", $no) . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('move_items.create', ['nomor' => $nomor]);
    }

    public function itemsList()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->join('stocks', 'items.id', '=', 'stocks.item_id')
                ->select('items.id', 'providers.id as provider_id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_gudang as stok_gudang')
                ->orderBy('nama_provider', 'asc')
                ->orderBy('items.nama', 'asc');

            return datatables()->of($data)
                ->addIndexColumn()
                ->make(true);
        }
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
                'tanggal'   => 'required|unique:move_items',
                'kuantitas.*' => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal pindah barang wajib diisi.',
                'tanggal.unique'      => 'Sudah ada data ditanggal tersebut. <br>Silahkan diupdate data pindah barang pada tanggal tersebut.',
                'kuantitas.*.required'    => 'Kuantitas wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $moveItems = new MoveItem;
        $moveItems->nomor = $request->nomor;
        $moveItems->tanggal = $request->tanggal;
        $moveItems->keterangan = $request->keterangan;
        $moveItems->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $newMoveItemDetails = new MoveItemDetail;
            $items = new Item;

            $newMoveItemDetails->move_item_id = $moveItems->id;
            $newMoveItemDetails->item_id = $request->item_id[$row];
            $newMoveItemDetails->kuantitas = $request->kuantitas[$row];

            $newStocks = Stock::where('item_id', $newMoveItemDetails->item_id)->first();
            $newStocks->stok_gudang = ($newStocks->stok_gudang) - ($newMoveItemDetails->kuantitas);
            $newStocks->stok_toko = ($newStocks->stok_toko) + ($newMoveItemDetails->kuantitas);
            $items->id = $newMoveItemDetails->item_id;

            DB::transaction(function () use ($newMoveItemDetails, $newStocks, $items) {
                $newMoveItemDetails->save();
                $items->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Entry pindah barang berhasil ditambahkan.'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $moveItem = MoveItem::findOrFail($id);
        $moveItemDetails = DB::table('move_item_details')
            ->join('items', 'move_item_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('move_items', 'move_item_details.move_item_id', '=', 'move_items.id')
            ->select('move_items.id as idMoveItem', 'move_items.tanggal', 'move_items.nomor', 'move_items.keterangan', 'move_item_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->where('move_item_id', $id)
            ->get();

        return view('move_items.show', compact('moveItem', 'moveItemDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $moveItem = MoveItem::findOrFail($id);

        $moveItemDetails = DB::table('move_item_details')
            ->join('items', 'move_item_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('stocks', 'stocks.item_id', '=', 'items.id')
            ->select('move_item_details.*', 'items.nama', 'providers.nama as nama_provider', 'stocks.stok_gudang')
            ->orderBy('items.nama', 'asc')
            ->where('move_item_id', $id)
            ->get();

        return view('move_items.edit', compact('moveItem', 'moveItemDetails'));
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
                'kuantitas.*' => 'required'
            ],
            [
                'tanggal.required'      => 'Tanggal pindah barang wajib diisi.',
                'kuantitas.*.required'    => 'Kuantitas wajib diisi.',
                'kuantitas.*.max'    => 'Kuantitas melebihi stok gudang.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        } else {
            //cari data pindah barang yang sudah ada, lalu hapus
            //kembalikan stok ke awal
            $moveItem = MoveItem::findOrFail($id);
            $moveItemDetails = MoveItemDetail::where('move_item_id', $moveItem->id)->get();

            foreach ($moveItemDetails as $moveItemDetail) {
                $items = Item::where('id', $moveItemDetail->item_id)->get();
                $stocks = Stock::where('item_id', $moveItemDetail->item_id)->get();
                foreach ($stocks as $stock) {
                    $stock->stok_gudang = $stock->stok_gudang + $moveItemDetail->kuantitas;
                    $stock->stok_toko = $stock->stok_toko - $moveItemDetail->kuantitas;
                    foreach ($items as $item) {
                        $item->stock()->save($stock);
                        $moveItem->delete();
                    }
                }
            }
        }

        $moveItems = new MoveItem;
        $moveItems->nomor = $request->nomor;
        $moveItems->tanggal = $request->tanggal;
        $moveItems->keterangan = $request->keterangan;
        $moveItems->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $updateMoveItemDetails = new MoveItemDetail;
            $items = new Item;

            $updateMoveItemDetails->move_item_id = $moveItems->id;
            $updateMoveItemDetails->item_id = $request->item_id[$row];
            $updateMoveItemDetails->kuantitas = $request->kuantitas[$row];

            $updateStocks = Stock::where('item_id', $updateMoveItemDetails->item_id)->first();
            $updateStocks->stok_gudang = $updateStocks->stok_gudang - $updateMoveItemDetails->kuantitas;
            $updateStocks->stok_toko = $updateStocks->stok_toko + $updateMoveItemDetails->kuantitas;
            $items->id = $updateMoveItemDetails->item_id;

            DB::transaction(function () use ($updateMoveItemDetails, $updateStocks, $items) {
                $updateMoveItemDetails->save();
                $items->stock()->save($updateStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Ubah pindah barang berhasil.'), 200);
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
