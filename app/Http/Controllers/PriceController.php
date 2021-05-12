<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Item;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('price.index');
    }

    public function getHargaPerdana()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->select('items.id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'items.harga', 'items.updated_at')
                ->having('nama_kategori', '=', 'Perdana')
                ->orderBy('nama_provider');

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    return '<a href="/price/' . $data->id . '/edit" class="btn btn-primary btn-sm">Ubah</a>';
                })
                ->editColumn('harga', function ($data) {
                    return number_format($data->harga, 0, ',', '.');
                })
                ->editColumn('updated_at', function ($data) {
                    return Carbon::parse($data->updated_at)->translatedFormat('d F Y');
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getHargaVoucher()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->select('items.id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'items.harga', 'items.updated_at')
                ->having('nama_kategori', '=', 'Voucher')
                ->orderBy('nama_provider');

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    return '<a href="/price' . $data->id . '/edit" class="btn btn-primary btn-sm">Ubah</a>';
                })
                ->editColumn('harga', function ($data) {
                    return  number_format($data->harga, 0, ',', '.');
                })
                ->editColumn('updated_at', function ($data) {
                    return Carbon::parse($data->updated_at)->translatedFormat('d F Y');
                })
                ->rawColumns(['action'])
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
        $item = Item::findOrFail($id);
        return view('price.edit', ['item' => $item]);
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
        $item = Item::findOrFail($id);
        $harga = $request->get('harga');
        $item->harga = str_replace(".", "", $harga);
        $item->save();
        return redirect()->route('price.index')->with('status-edit', 'Harga jual berhasil diperbarui');
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
