<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use App\Models\Stock;
use App\Models\Item;
use Carbon\Carbon;
use PdfReport;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AWAL = 'PCH';
        $bulanRomawi = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $noUrutAkhir = Purchase::max('id');
        $no = 1;
        if ($noUrutAkhir) {
            $noInvoice = sprintf("%012s", abs($noUrutAkhir + 1)) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        } else {
            $noInvoice = sprintf("%012s", $no) . '/' . $AWAL . '/' . $bulanRomawi[date('n')] . '/' . date('Y');
        }
        return view('purchases.index', ['noInvoice' => $noInvoice]);
    }

    public function suppliersSearch(Request $request)
    {
        $keyword = $request->get('q');
        $suppliers = Supplier::where(
            "nama",
            "LIKE",
            "%$keyword%"
        )->get();
        return $suppliers;
    }

    public function itemsList()
    {
        if (request()->ajax()) {
            $data = DB::table('items')
                ->join('categories', 'categories.id', '=', 'items.category_id')
                ->join('providers', 'providers.id', '=', 'items.provider_id')
                ->join('stocks', 'items.id', '=', 'stocks.item_id')
                ->select('items.id', 'providers.id as provider_id', 'providers.nama as nama_provider', 'items.nama', 'categories.nama as nama_kategori', 'stocks.stok_gudang')
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
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal'          => 'required',
                'supplier_id'   => 'required',
                'cara_bayar'   => 'required|in:Kas,Kredit,Transfer',
                'item_id'   => 'required',
                'kuantitas'   => 'required',
                'harga'   => 'required'
            ],
            [
                'tanggal.required'       => 'Tanggal pembelian wajib diisi.',
                'supplier_id.required'   => 'Supplier wajib diisi.',
                'cara_bayar.in'   => 'Cara bayar wajib diisi.',
                'item_id.required'   => 'Barang belum dipilih.',
                'kuantitas.required'   => 'Kuantitas wajib diisi.',
                'harga.required'   => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $purchases = new Purchase;
        $purchases->invoice = $request->invoice;
        $purchases->tanggal = $request->tanggal;
        $purchases->supplier_id = $request->supplier_id;
        $purchases->cara_bayar = $request->cara_bayar;
        $purchases->pajak = $request->pajak;
        $purchases->jatuh_tempo = $request->jatuh_tempo;
        if ($purchases->cara_bayar == 'Kredit') {
            $purchases->is_lunas = false;
        } else {
            $purchases->is_lunas = true;
        }
        $purchases->keterangan = $request->keterangan;
        $purchases->user_id = $request->user()->id;
        $purchases->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $newPurchaseDetails = new PurchaseDetail;
            $updateItems = new Item;

            $newPurchaseDetails->purchase_id = $purchases->id;
            $newPurchaseDetails->item_id = $request->item_id[$row];
            $newPurchaseDetails->kuantitas = $request->kuantitas[$row];
            $newPurchaseDetails->harga = $request->harga[$row];

            $newStocks = Stock::where('item_id', $newPurchaseDetails->item_id)->first();
            $newStocks->stok_gudang = $newStocks->stok_gudang + $newPurchaseDetails->kuantitas;
            $updateItems->id = $newPurchaseDetails->item_id;

            DB::transaction(function () use ($newPurchaseDetails, $newStocks, $updateItems) {
                $newPurchaseDetails->save();
                $updateItems->stock()->save($newStocks);
            });
        }

        return response()->json(array('status' => 'success', 'msg' => 'Entry pembelian berhasil ditambahkan.'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);

        $purchaseDetails = DB::table('purchase_details')
            ->join('items', 'purchase_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'purchase_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('purchase_details.kuantitas * purchase_details.harga as sub_total')
            // ->selectRaw('((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga)) as total_ppn')
            ->where('purchase_id', $id)
            ->get();

        return view('purchases.show', compact('purchase', 'purchaseDetails'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        $supplier = Supplier::all();

        $purchaseDetails = DB::table('purchase_details')
            ->join('items', 'purchase_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'purchase_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('purchase_details.kuantitas * purchase_details.harga as sub_total')
            // ->selectRaw('((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga)) as total_ppn')
            ->orderBy('items.nama', 'asc')
            ->where('purchase_id', $id)
            ->get();

        return view('purchases.edit', compact('purchase', 'purchaseDetails', 'supplier'));
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
        //cari data purchase yang sudah ada, lalu hapus
        //kembalikan stok ke awal
        $purchase = Purchase::findOrFail($id);
        $purchaseDetails = PurchaseDetail::where('purchase_id', $purchase->id)->get();

        foreach ($purchaseDetails as $purchaseDetail) {
            $items = Item::where('id', $purchaseDetail->item_id)->get();
            $stocks = Stock::where('item_id', $purchaseDetail->item_id)->get();
            foreach ($stocks as $stock) {
                $stock->stok_gudang = $stock->stok_gudang - $purchaseDetail->kuantitas;
                foreach ($items as $item) {
                    $item->stock()->save($stock);
                    $purchase->delete();
                }
            }
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal'          => 'required',
                'supplier_id'   => 'required',
                'cara_bayar'   => 'required|in:Kas,Kredit,Transfer',
                'item_id'   => 'required',
                'kuantitas'   => 'required',
                'harga'   => 'required'
            ],
            [
                'tanggal.required'       => 'Tanggal pembelian wajib diisi.',
                'supplier_id.required'   => 'Supplier wajib diisi.',
                'cara_bayar.in'   => 'Cara bayar wajib diisi.',
                'item_id.required'   => 'Barang belum dipilih.',
                'kuantitas.required'   => 'Kuantitas wajib diisi.',
                'harga.required'   => 'Harga beli wajib diisi.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $purchases = new Purchase;
        $purchases->invoice = $request->invoice;
        $purchases->tanggal = $request->tanggal;
        $purchases->supplier_id = $request->supplier_id;
        $purchases->cara_bayar = $request->cara_bayar;
        $purchases->pajak = $request->pajak;
        $purchases->jatuh_tempo = $request->jatuh_tempo;
        if ($purchases->cara_bayar == 'Kredit') {
            $purchases->is_lunas = false;
        } else {
            $purchases->is_lunas = true;
        }
        $purchases->keterangan = $request->keterangan;
        $purchases->user_id = $request->user()->id;
        $purchases->save();

        $items = $request->item_id;
        foreach ($items as $row => $key) {
            $updatePurchaseDetails = new PurchaseDetail;
            $updateItems = new Item;

            $updatePurchaseDetails->purchase_id = $purchases->id;
            $updatePurchaseDetails->item_id = $request->item_id[$row];
            $updatePurchaseDetails->kuantitas = $request->kuantitas[$row];
            $updatePurchaseDetails->harga = $request->harga[$row];

            $updateStocks = Stock::where('item_id', $updatePurchaseDetails->item_id)->first();
            $updateStocks->stok_gudang = $updateStocks->stok_gudang + $updatePurchaseDetails->kuantitas;
            $updateItems->id = $updatePurchaseDetails->item_id;

            DB::transaction(function () use ($updatePurchaseDetails, $updateStocks, $updateItems) {
                $updatePurchaseDetails->save();
                $updateItems->stock()->save($updateStocks);
            });
        }
        return response()->json(array('status' => 'success', 'msg' => 'Data pembelian berhasil diubah.'), 200);
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

    public function getPurchasesData(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->tanggal_mulai)) {
                $purchases = DB::table('purchases')
                ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->join('purchase_details', 'purchase_details.purchase_id', '=', 'purchases.id')
                ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'suppliers.nama as nama_supplier', 'purchase_details.*')
                ->selectRaw('SUM(purchase_details.kuantitas * purchase_details.harga) as total_non_ppn')
                ->selectRaw('SUM(((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga))) as total_ppn')
                ->groupBy('purchase_details.purchase_id')
                ->orderBy('purchases.tanggal', 'desc')
                    ->orderBy('purchases.invoice', 'desc')
                    ->whereBetween('tanggal', array($request->tanggal_mulai, $request->tanggal_akhir))
                    ->when($request->supplier != '', function ($db) use ($request) {
                        $db->where('purchases.supplier_id', $request->supplier);
                    })
                    ->when($request->pajak != '', function ($db) use ($request) {
                        $db->where('purchases.pajak', $request->pajak);
                    })
                    ->when($request->supplier != '' && $request->pajak != '', function ($db) use ($request) {
                        $db->where('purchases.supplier_id', $request->supplier)
                            ->where('purchases.pajak', $request->pajak);
                    });
            } else {
                $purchases = DB::table('purchases')
                ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->join('purchase_details', 'purchase_details.purchase_id', '=', 'purchases.id')
                ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'suppliers.nama as nama_supplier', 'purchase_details.*')
                ->selectRaw('SUM(purchase_details.kuantitas * purchase_details.harga) as total_non_ppn')
                ->selectRaw('SUM(((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga))) as total_ppn')
                ->groupBy('purchase_details.purchase_id')
                ->orderBy('purchases.tanggal', 'desc')
                    ->orderBy('purchases.invoice', 'desc')
                    ->when($request->supplier != '', function ($db) use ($request) {
                        $db->where('purchases.supplier_id', $request->supplier);
                    })
                    ->when($request->pajak != '', function ($db) use ($request) {
                        $db->where('purchases.pajak', $request->pajak);
                    })
                    ->when($request->supplier != '' && $request->pajak != '', function ($db) use ($request) {
                        $db->where('purchases.supplier_id', $request->supplier)
                            ->where('purchases.pajak', $request->pajak);
                    });
            }
            return datatables()->of($purchases)
                ->addColumn('action', function ($purchases) {
                    return '<a href="/purchases/' . $purchases->idPurchase . '" class="btn btn-sm"
                style="background-color:transparent;">
                <i class="fa fa-eye"></i></a>';
                })
                ->editColumn('total', function ($purchases) {
                    if ($purchases->pajak == "PPN") return number_format($purchases->total_ppn, 0, ',', '.');
                    if ($purchases->pajak == "Non PPN") return number_format($purchases->total_non_ppn, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        $supplier = Supplier::all();
        return view('purchases.data', compact('supplier'));
    }

    public function getPurchasesDebt(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->tanggal_mulai)) {
                $purchases = DB::table('purchases')
                    ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                    ->join('purchase_details', 'purchase_details.purchase_id', '=', 'purchases.id')
                    ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.jatuh_tempo', 'purchases.tanggal_lunas', 'purchases.is_lunas', 'suppliers.nama as nama_supplier', 'purchase_details.*')
                    ->selectRaw('SUM(purchase_details.kuantitas * purchase_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga))) as total_ppn')
                    ->groupBy('purchase_details.purchase_id')
                    ->orderBy('purchases.created_at', 'desc')
                    ->where('purchases.cara_bayar', '=', 'Kredit')
                    ->whereBetween('tanggal', array($request->tanggal_mulai, $request->tanggal_akhir));
            } else {
                $purchases = DB::table('purchases')
                    ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                    ->join('purchase_details', 'purchase_details.purchase_id', '=', 'purchases.id')
                    ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.jatuh_tempo', 'purchases.tanggal_lunas', 'purchases.is_lunas', 'suppliers.nama as nama_supplier', 'purchase_details.*')
                    ->selectRaw('SUM(purchase_details.kuantitas * purchase_details.harga) as total_non_ppn')
                    ->selectRaw('SUM(((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga))) as total_ppn')
                    ->groupBy('purchase_details.purchase_id')
                    ->orderBy('purchases.created_at', 'desc')
                    ->where('purchases.cara_bayar', '=', 'Kredit');
            }
            return datatables()->of($purchases)
                ->addColumn('action', function ($purchases) {
                    return '<a href="debt/' . $purchases->idPurchase . '/edit" class="btn btn-sm"
                style="background-color:transparent;">
                <i class="fa fa-eye"></i></a>';
                })
                ->addColumn('status_color', ' ')
                ->editColumn('is_lunas', function ($purchases) {
                    if ($purchases->is_lunas == 1) return 'LUNAS';
                    if ($purchases->is_lunas == 0) return 'BELUM LUNAS';
                })
                ->editColumn('total', function ($purchases) {
                    if ($purchases->pajak == "PPN") return number_format($purchases->total_ppn, 0, ',', '.');
                    if ($purchases->pajak == "Non PPN") return number_format($purchases->total_non_ppn, 0, ',', '.');
                })
                ->editColumn('status_color', function ($purchases) {
                    if ($purchases->is_lunas == 0 && Carbon::today() >= $purchases->jatuh_tempo) return 'red';
                    return $purchases->is_lunas && Purchase::STATUS_COLOR[$purchases->is_lunas] ? Purchase::STATUS_COLOR[$purchases->is_lunas] : Purchase::STATUS_COLOR[$purchases->is_lunas];
                })
                ->editColumn('tanggal', function ($purchases) {
                    return Carbon::parse($purchases->tanggal)->translatedFormat('d-F-Y');
                })
                ->editColumn('jatuh_tempo', function ($purchases) {
                    return Carbon::parse($purchases->jatuh_tempo)->translatedFormat('d-F-Y');
                })
                ->editColumn('tanggal_lunas', function ($purchases) {
                    return $purchases->tanggal_lunas ? Carbon::parse($purchases->tanggal_lunas)->translatedFormat('d-F-Y') : null;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('purchases.debt');
    }

    public function editDebt($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);

        $purchaseDetails = DB::table('purchase_details')
            ->join('items', 'purchase_details.item_id', '=', 'items.id')
            ->join('providers', 'items.provider_id', '=', 'providers.id')
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'purchase_details.*', 'items.nama', 'providers.nama as nama_provider')
            ->selectRaw('purchase_details.kuantitas * purchase_details.harga as sub_total')
            // ->selectRaw('((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga)) as total_ppn')
            ->where('purchase_id', $id)
            ->get();

        return view('purchases.edit-debt', compact('purchase', 'purchaseDetails'));
    }

    public function updateDebt(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'tanggal_lunas'   => 'required'
            ],
            [
                'tanggal_lunas.required'       => 'Tanggal pelunasan wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(array('status' => 'error', 'msg' => $validator->errors()->all()), 500);
        }

        $purchase = Purchase::findOrFail($id);

        $purchase->tanggal_lunas = $request->get('tanggal_lunas');
        $purchase->is_lunas = true;
        $purchase->save();

        return response()->json(array('status' => 'success', 'msg' => 'Hutang pembelian berhasil diubah.'), 200);
    }

    public function getPurchasesReport(Request $request)
    {
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalAkhir = $request->get('tanggal_akhir');
        $supplier = $request->get('supplier_filter');

        if (!empty($tanggalMulai)) {
            $purchases = Purchase::with('purchaseDetails', 'supplier')
                ->whereBetween('tanggal', [$tanggalMulai . ' 00:00:00', $tanggalAkhir . ' 23:59:59'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $purchases = Purchase::with('purchaseDetails', 'supplier')
                ->where('tanggal', Carbon::today())
                ->orderBy('created_at', 'desc')
                ->get();
        }
        $suppliers = Supplier::all();
        return view('purchases.report', compact('purchases', 'suppliers'));
    }

    /*
    public function export(Request $request)
    {
        $tanggalMulai = '2021-05-19';
        $tanggalAkhir = '2021-05-19';

        $judul = 'Laporan Pembelian'; 

        $meta = [
            'Filter' => $tanggalMulai . ' hingga ' . $tanggalAkhir
        ];

        $purchases = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->join('purchase_details', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->select('purchases.id as idPurchase', 'purchases.tanggal', 'purchases.invoice', 'purchases.pajak', 'purchases.keterangan', 'suppliers.nama as nama_supplier', 'purchase_details.*')
            ->selectRaw('SUM(purchase_details.kuantitas * purchase_details.harga) as total_non_ppn')
            ->selectRaw('SUM(((purchase_details.kuantitas * purchase_details.harga * 0.1) + (purchase_details.kuantitas * purchase_details.harga))) as total_ppn')
            ->groupBy('purchase_details.purchase_id')
            ->orderBy('purchases.tanggal', 'desc')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]);

        $columns = [
            'Tanggal' => 'tanggal',
            'Invoice' => 'invoice',
            'Supplier' => 'nama_supplier',
            'Pajak' => 'pajak',
            'Total' => function ($result) {
                return $result->pajak == "PPN" ? number_format($result->total_ppn, 0, ',', '') : number_format($result->total_non_ppn, 0, ',', '');
            },
            'Keterangan' => 'keterangan'
        ];

        return PdfReport::of($judul, $meta, $purchases, $columns)
            ->setOrientation('landscape')
            ->showNumColumn(false)
            ->editColumn('Tanggal', [
                'displayAs' => function ($result) {
                    return $result->tanggal;
                }
            ])
            ->editColumn('Total', [
                'class' => 'right bold',
                'displayAs' => function ($result) {
                    return $result->pajak == "PPN" ? number_format((int) $result->total_ppn, 0, '', '.') : number_format((int) $result->total_non_ppn, 0, '', '.');
                }
            ])
            ->editColumn('Keterangan', [
                'class' => 'center'
            ])
            ->showTotal([
                'Total' => 'point'
            ])
            ->groupBy('Invoice')
            ->stream();
    }
    */
}
