<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Sale;
use App\Models\Customer;
use Carbon\Carbon;

class SaleController extends Controller
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
                $sales = DB::table('sales')
                    ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                    ->join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                    ->join('users', 'sales.user_id', '=', 'users.id')
                    ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.pajak', 'sales.jenis', 'sales.keterangan', 'customers.nama as nama_customer', 'users.username as nama_pengguna', 'sale_details.*')
                    ->selectRaw('SUM(sale_details.kuantitas * sale_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga))) as total_ppn')
                    ->groupBy('sale_details.sale_id')
                    ->orderBy('sales.tanggal', 'desc')
                    ->orderBy('sales.invoice', 'desc')
                    ->whereBetween('tanggal', array($request->tanggal_mulai, $request->tanggal_akhir))
                    ->when($request->customer != '', function ($db) use ($request) {
                        $db->join('customers as c', 'sales.customer_id', '=', 'c.id')->where('sales.customer_id', $request->customer);
                    })
                    ->when($request->jenis != '', function ($db) use ($request) {
                        $db->where('sales.jenis', $request->jenis);
                    })
                    ->when($request->pajak != '', function ($db) use ($request) {
                        $db->where('sales.pajak', $request->pajak);
                    })
                    ->when($request->customer != '' && $request->jenis != '' && $request->pajak != '', function ($db) use ($request) {
                        $db->where('sales.customer_id', $request->customer)
                           ->where('sales.jenis', $request->jenis)
                           ->where('sales.pajak', $request->pajak);
                    });;
            } else {
                $sales = DB::table('sales')
                    ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
                    ->join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                    ->join('users', 'sales.user_id', '=', 'users.id')
                    ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.pajak', 'sales.jenis', 'sales.keterangan', 'customers.nama as nama_customer', 'users.username as nama_pengguna', 'sale_details.*')
                    ->selectRaw('SUM(sale_details.kuantitas * sale_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga))) as total_ppn')
                    ->groupBy('sale_details.sale_id')
                    ->orderBy('sales.created_at', 'desc')
                    ->where('tanggal', Carbon::today());
                //->whereNull('sales.customer_id');
            }
            return datatables()->of($sales)
                ->addColumn('action', function ($sales) {
                    return '<a href="/sales/' . $sales->idSale . '" class="btn btn-sm"
                style="background-color:transparent;">
                <i class="fa fa-eye"></i></a>';
                })
                ->editColumn('total', function ($sales) {
                    if ($sales->pajak == "PPN") return number_format($sales->total_ppn, 0, ',', '.');
                    if ($sales->pajak == "Non PPN") return number_format($sales->total_non_ppn, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        $customers = Customer::all();
        return view('sales.index', compact('customers'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale = Sale::with('customer')->findOrFail($id);
        $saleDetails = DB::table('sale_details')
            ->join('items', 'sale_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.pajak', 'sales.keterangan', 'sale_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('sale_details.kuantitas * sale_details.harga as sub_total')
            // ->selectRaw('((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga)) as total_ppn')
            ->where('sale_id', $id)
            ->get();

        if ($sale->jenis == "Retail") return view('retail_sales.show', compact('sale', 'saleDetails'));
        if ($sale->jenis == "Grosir") return view('wholesales.show', compact('sale', 'saleDetails'));
        if ($sale->jenis == "Gudang") return view('warehouses.show', compact('sale', 'saleDetails'));
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

    public function getSalesDebt(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->tanggal_mulai)) {
                $sales = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                    ->join('users', 'sales.user_id', '=', 'users.id')
                    ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.jenis', 'sales.pajak', 'sales.jatuh_tempo', 'sales.tanggal_lunas', 'sales.is_lunas', 'customers.nama as nama_customer', 'users.username as nama_pengguna', 'sale_details.*')
                    ->selectRaw('SUM(sale_details.kuantitas * sale_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga))) as total_ppn')
                    ->groupBy('sale_details.sale_id')
                    ->orderBy('sales.created_at', 'desc')
                    ->where('sales.cara_bayar', '=', 'Kredit')
                    ->whereBetween('tanggal', array($request->tanggal_mulai, $request->tanggal_akhir));
            } else {
                $sales = DB::table('sales')
                    ->join('customers', 'sales.customer_id', '=', 'customers.id')
                    ->join('sale_details', 'sale_details.sale_id', '=', 'sales.id')
                    ->join('users', 'sales.user_id', '=', 'users.id')
                    ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.jenis', 'sales.pajak', 'sales.jatuh_tempo', 'sales.tanggal_lunas', 'sales.is_lunas', 'customers.nama as nama_customer', 'users.username as nama_pengguna', 'sale_details.*')
                    ->selectRaw('SUM(sale_details.kuantitas * sale_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga))) as total_ppn')
                    ->groupBy('sale_details.sale_id')
                    ->orderBy('sales.created_at', 'desc')
                    ->where('sales.cara_bayar', '=', 'Kredit');
            }
            return datatables()->of($sales)
                ->addColumn('action', function ($sales) {
                    return '<a href="debt/' . $sales->idSale . '/edit" class="btn btn-sm"
                style="background-color:transparent;">
                <i class="fa fa-eye"></i></a>';
                })
                ->addColumn('status_color', ' ')
                ->editColumn('is_lunas', function ($sales) {
                    if ($sales->is_lunas == 1) return 'LUNAS';
                    if ($sales->is_lunas == 0) return 'BELUM LUNAS';
                })
                ->editColumn('total', function ($sales) {
                    if ($sales->pajak == "PPN") return number_format($sales->total_ppn, 0, ',', '.');
                    if ($sales->pajak == "Non PPN") return number_format($sales->total_non_ppn, 0, ',', '.');
                })
                ->editColumn('status_color', function ($sales) {
                    if ($sales->is_lunas == 0 && Carbon::today() >= $sales->jatuh_tempo) return 'red';
                    return $sales->is_lunas && Sale::STATUS_COLOR[$sales->is_lunas] ? Sale::STATUS_COLOR[$sales->is_lunas] : Sale::STATUS_COLOR[$sales->is_lunas];
                })
                ->editColumn('tanggal', function ($sales) {
                    return Carbon::parse($sales->tanggal)->translatedFormat('d-F-Y');
                })
                ->editColumn('jatuh_tempo', function ($sales) {
                    return Carbon::parse($sales->jatuh_tempo)->translatedFormat('d-F-Y');
                })
                ->editColumn('tanggal_lunas', function ($sales) {
                    return $sales->tanggal_lunas ? Carbon::parse($sales->tanggal_lunas)->translatedFormat('d-F-Y') : null;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('sales.debt');
    }

    public function editDebt($id)
    {
        $sale = Sale::with('customer')->findOrFail($id);

        $saleDetails = DB::table('sale_details')
            ->join('items', 'sale_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->select('sales.id as idSale', 'sales.tanggal', 'sales.invoice', 'sales.pajak', 'sales.keterangan', 'sale_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('sale_details.kuantitas * sale_details.harga as sub_total')
            // ->selectRaw('((sale_details.kuantitas * sale_details.harga * 0.1) + (sale_details.kuantitas * sale_details.harga)) as total_ppn')
            ->where('sale_id', $id)
            ->get();

        return view('sales.edit-debt', compact('sale', 'saleDetails'));
    }

    public function updateDebt(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal_lunas' => 'required'
            ],
            [
                'tanggal_lunas.required' => 'Tanggal pelunasan wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $sale = Sale::findOrFail($id);

        $sale->tanggal_lunas = $request->get('tanggal_lunas');
        $sale->is_lunas = true;
        $sale->save();

        return response()->json(array('status' => 'success', 'msg' => 'Piutang penjualan berhasil diubah.'), 200);
    }
}
