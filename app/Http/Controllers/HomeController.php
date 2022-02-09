<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $salesData = Sale::select(\DB::raw("COUNT(id) as count"))
            ->whereYear('tanggal', date('Y'))
            ->groupBy(\DB::raw("Month(tanggal)"))
            ->pluck('count');

        $retailSales = Sale::select("*")
            ->where("jenis", "Retail")
            ->get();
        
        $wholesales = Sale::select("*")
            ->where("jenis", "Grosir")
            ->get();
        
        $warehouseSales = Sale::select("*")
            ->where("jenis", "Gudang")
            ->get();
        
        $purchases = Purchase::all();
        
        return view('home', compact('salesData', 'retailSales', 'wholesales', 'warehouseSales', 'purchases'));
    }
}
