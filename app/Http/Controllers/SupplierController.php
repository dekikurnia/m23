<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateSupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::paginate(10);
        $filterKeyword = $request->get('keyword');
        if ($filterKeyword) {
            $suppliers = Supplier::where(
                'nama',
                'LIKE',
                "%$filterKeyword%"
            )->paginate(50);
        }
        return view('suppliers.index', ['suppliers' => $suppliers]);
    }/*  */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("suppliers.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSupplierRequest $request)
    {
        $new_supplier = new Supplier;

        $new_supplier->kode = $request->get('kode');
        $new_supplier->nama = $request->get('nama');
        $new_supplier->alamat = $request->get('alamat');
        $new_supplier->telepon = $request->get('telepon');

        $new_supplier->save();
        return redirect()->route('suppliers.index')->with('status-create', 'Tambah supplier berhasil');
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
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', ['supplier' => $supplier]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateSupplierRequest $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->kode = $request->get('kode');
        $supplier->nama = $request->get('nama');
        $supplier->alamat = $request->get('alamat');
        $supplier->telepon = $request->get('telepon');

        $supplier->save();
        return redirect()->route('suppliers.index')->with('status-edit', 'Supplier berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('status-delete', 'Supplier berhasil dihapus');
    }
}
