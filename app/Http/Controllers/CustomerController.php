<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::paginate(10);
        $filterKeyword = $request->get('keyword');
        if ($filterKeyword) {
            $customers = Customer::where(
                'nama',
                'LIKE',
                "%$filterKeyword%"
            )->paginate(50);
        }
        return view('customers.index', ['customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("customers.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCustomerRequest $request)
    {
        $newCustomer = new Customer;

        $newCustomer->kode = $request->get('kode');
        $newCustomer->nama = $request->get('nama');
        $newCustomer->alamat = $request->get('alamat');
        $newCustomer->telepon = $request->get('telepon');

        $newCustomer->save();
        return redirect()->route('customers.index')->with('status-create', 'Tambah customer berhasil');
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
        $customer = Customer::findOrFail($id);
        return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCustomerRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $customer->kode = $request->get('kode');
        $customer->nama = $request->get('nama');
        $customer->alamat = $request->get('alamat');
        $customer->telepon = $request->get('telepon');

        $customer->save();
        return redirect()->route('customers.index')->with('status-edit', 'Customer berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('status-delete', 'Customer berhasil dihapus');
    }
}
