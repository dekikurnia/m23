<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Http\Requests\CreateItemRequest;
use App\Models\Stock;
use App\Models\FirstStock;
use App\Models\Provider;
use App\Models\Category;
use DataTables;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->ajax()) {
                $data = DB::table('items')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->join('providers', 'providers.id', '=', 'items.provider_id')
                    ->join('stocks', 'items.id', '=', 'stocks.item_id')
                    ->select('items.id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_gudang', 'stocks.stok_toko')
                    ->when($request->category != '' , function($db)use($request){
                        $db->where('items.category_id', $request->category);
                    })
                    ->orderBy('nama_provider', 'asc');
            
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    return '<a href="/items/'. $data->id .'/edit" class="btn btn-primary btn-sm">Ubah</a>';
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        $category = Category::all();
        $provider = Provider::all();
        return view('items.index', compact('category', 'provider'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Category::all();
        $provider = Provider::all();
        return view('items.create', compact('category', 'provider'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateItemRequest $request)
    {
        $newItem = new Item;
        $newStock = new Stock;
        $newFirstStock = new FirstStock;

        $newItem->provider_id = $request->get('provider_id');
        $newItem->nama = $request->get('nama');
        $newItem->category_id = $request->get('category_id');

        /*
        $newStock->stok_gudang = $request->get('stok_gudang');
        $newStock->stok_toko = $request->get('stok_toko');

        $newFirstStock->stok_gudang = $request->get('stok_gudang');
        $newFirstStock->stok_toko = $request->get('stok_toko');
        */

        $newStock->stok_gudang = 0;
        $newStock->stok_toko = 0;

        $newFirstStock->stok_gudang = 0;
        $newFirstStock->stok_toko = 0;

        DB::transaction(function() use ($newItem, $newStock, $newFirstStock) {
            $newItem->save();
            $newItem->stock()->save($newStock);
            $newItem->firstStock()->save($newFirstStock);
        });
        return redirect()->route('items.index')->with('status-create', 'Tambah barang baru berhasil');
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
        $category = Category::all();

        $provider = Provider::all();

        $item = Item::findOrFail($id);
        return view('items.edit', compact('item','category', 'provider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateItemRequest $request, $id)
    {
        $item = Item::findOrFail($id);
        $stock = Stock::with('item')->find($id);
        $firstStock = FirstStock::with('item')->find($id);

        $item->provider_id = $request->get('provider_id');
        $item->nama = $request->get('nama');
        $item->category_id = $request->get('category_id');

        $stock->stok_gudang  = $request->get('stok_gudang');
        $stock->stok_toko = $request->get('stok_toko');
        
        $firstStock->stok_gudang  = $request->get('stok_gudang');
        $firstStock->stok_toko = $request->get('stok_toko');
        
        DB::transaction(function() use ($item, $stock, $firstStock) {
            $item->save();
            $item->stock()->save($stock);
            $item->firstStock()->save($firstStock);
        });
        return redirect()->route('items.index')->with('status-edit', 'Ubah barang berhasil');
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
