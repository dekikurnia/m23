<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getStoreStockReport(Request $request)
    {

        return view('reports.store-stock');
    }

    public function getWholesaleSummaryReport(Request $request)
    {

        return view('reports.wholesale-summary');
    }
}
}
