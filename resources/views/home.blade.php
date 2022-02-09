@extends('layouts.app')
@section('title') Dashboard @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-xl-3">
                            <div class="card bg-c-blue order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Penjualan Retail</h6>
                                    <h2 class="text-right"><i class="fa fa-cart-plus f-left"></i><span>{{ count($retailSales) }}</span></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-3">
                            <div class="card bg-c-green order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Penjualan Grosir</h6>
                                    <h2 class="text-right"><i class="fa fa-tags f-left"></i><span>{{ count($wholesales) }}</span></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-3">
                            <div class="card bg-c-yellow order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Penjualan Gudang</h6>
                                    <h2 class="text-right"><i class="fa fa-store f-left"></i><span>{{ count($warehouseSales) }}</span></h2>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-3">
                            <div class="card bg-c-pink order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Pembelian</h6>
                                    <h2 class="text-right"><i class="fa fa-credit-card f-left"></i><span>{{ count($purchases) }}</span></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
 var salesData = <?php echo json_encode($salesData)?>;
    Highcharts.chart('salesChart', {
        title: {
            text: 'Grafik Penjualan Per Bulan'
        },
        subtitle: {
            text: 'M23 RELOAD'
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep',
                'Okt', 'Nov', 'Des'
            ]
        },
        yAxis: {
            title: {
                text: 'Angka Penjualan'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        plotOptions: {
            series: {
                allowPointSelect: true
            }
        },
        series: [{
            name: 'Data Penjualan',
            data: salesData
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    });          
</script>
@stop