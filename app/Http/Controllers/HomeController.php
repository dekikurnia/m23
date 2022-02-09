<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;

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
        $salesData = Sale::select(\DB::raw("COUNT(*) as count"))
                    ->whereYear('tanggal', date('Y'))
                    ->groupBy(\DB::raw("Month(tanggal)"))
                    ->pluck('count');
        return view('home', compact('salesData'));
    }
}
