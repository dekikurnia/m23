<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Http\Requests\CreateItemRequest;
use App\Models\Stock;
use App\Models\Provider;
use App\Models\Category;

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
            if ($request->category) {
                $data = DB::table('items')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->join('providers', 'providers.id', '=', 'items.provider_id')
                    ->join('stocks', 'items.id', '=', 'stocks.item_id')
                    ->select('items.id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_gudang', 'stocks.stok_toko')
                    ->where('items.category_id', $request->category)
                    ->orderBy('nama_provider', 'asc');
            } else {
                $data = DB::table('items')
                    ->join('categories', 'categories.id', '=', 'items.category_id')
                    ->join('providers', 'providers.id', '=', 'items.provider_id')
                    ->join('stocks', 'items.id', '=', 'stocks.item_id')
                    ->select('items.id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_gudang', 'stocks.stok_toko')
                    ->orderBy('nama_provider', 'asc');
            }
            return datatables()->of($data)
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
        $new_item = new Item;
        $new_stock = new Stock;

        $new_item->provider_id = $request->get('provider_id');
        $new_item->nama = $request->get('nama');
        $new_item->category_id = $request->get('category_id');

        $new_stock->stok_gudang = 0;
        $new_stock->stok_toko = 0;

        DB::transaction(function() use ($new_item, $new_stock) {
            $new_item->save();
            $new_item->stock()->save($new_stock);
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

        $item->provider_id = $request->get('provider_id');
        $item->nama = $request->get('nama');
        $item->category_id = $request->get('category_id');

        $item->save();
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
