@extends('layouts.app')
@section('title') Data Penjualan @endsection
@section('content')
<div class="container-fluid">
    <h2 align="center">
        DATA PENJUALAN<br>
    </h2>
    <hr class="my-3">
    <div class="row justify-content-center">
        <div class="row justify-content-center input-daterange">
            <form class="form-inline">
                <input type="text" placeholder="Tanggal Mulai" class="form-control mb-2 mr-sm-2"
                    id="tanggal_mulai" name="tanggal_mulai" autocomplete="off">
                <div class="input-group mb-2 mr-sm-2">
                    <input type="text" placeholder="Tanggal Akhir" class="form-control" id="tanggal_akhir"
                        name="tanggal_akhir" autocomplete="off">
                </div>
                <button type="button" name="filter" id="filter" class="btn btn-primary mb-2">Tampilkan</button>&nbsp;
                <button type="button" name="refresh" id="refresh" class="btn btn-danger mb-2">Reset</button>
            </form>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Data Penjualan') }}</div>
                <div class="card-body">
                    <table class="table table-striped table-sm" id="sales-table">
                        <thead>
                            <tr>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Tanggal</b></th>
                                <th style="width: 20%; vertical-align: middle;">
                                    <b>Invoice</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Tipe Penjualan</b></th>
                                <th style="width: 15%"><b>Customer</b></th>
                                <th style="width: 10%"><b>Pajak</b></th>
                                <th style="width: 10%; text-align: right;">
                                    <b>Total</b></th>
                                <th style="width: 25%; vertical-align: middle;" class="text-center">
                                    <b>Keterangan</b></th>
                                    <th style="width: 10%; vertical-align: middle;" class="text-center">
                                        <b>User</b></th>
                                <th style="width: 2%"><b></b></th>
                            </tr>
                            <tr>
                                <th style="width: 10%; vertical-align: middle;"></th>
                                <th style="width: 20%; vertical-align: middle;"></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <select class="form-control form-control-sm" name="jenis_filter" id="jenis_filter">
                                        <option value=""></option>
                                        <option value="Retail">Retail</option>
                                        <option value="Grosir">Grosir</option>
                                        <option value="Gudang">Gudang</option>
                                    </select></th>
                                <th style="width: 15%">
                                    <select name="customer_filter" id="customer_filter"
                                        class="form-control form-control-sm">
                                        <option value=""></option>
                                        @foreach($customers as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 10%">
                                    <select class="form-control form-control-sm" name="pajak_filter" id="pajak_filter">
                                        <option value=""></option>
                                        <option value="Non PPN">Non PPN</option>
                                        <option value="PPN">PPN</option>
                                    </select>
                                </th>
                                <th style="width: 10%; vertical-align: middle;"></th>
                                <th style="width: 25%; vertical-align: middle;" class="text-center"></th>
                                    <th style="width: 10%; vertical-align: middle;" class="text-center"></th>
                                <th style="width: 2%"><b></b></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function () {
    $(".input-daterange").datepicker({
        todayBtn:'linked',
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom'
    });

    fetch_data();

    function fetch_data(tanggal_mulai = '', tanggal_akhir = '', customer = '', jenis = '', pajak = '') {
        $('#sales-table').DataTable({
            autoWidth: false, 
            pageLength: 100,
            lengthMenu: [100, 200, 300],
            processing: true,
            serverSide: true,
            ordering : false,
            searching : false,
            ajax: {
                url: "{{ route('sales.index') }}",
                data: {
                    tanggal_mulai: tanggal_mulai,
                    tanggal_akhir: tanggal_akhir,
                    customer: customer,
                    jenis: jenis,
                    pajak: pajak
                }
            },
            columns: [{
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'invoice',
                    name: 'invoice'
                },
                {
                    data: 'jenis',
                    name: 'jenis'
                },
                {
                    data: 'nama_customer',
                    name: 'customers.nama'
                },
                {
                    data: 'pajak',
                    name: 'pajak'
                },
                {
                    data: 'total',
                    name: 'total',
                    className: "text-right"
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'nama_pengguna',
                    name: 'users.username'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    $('#filter').click(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var customer = $('#customer_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        if (tanggal_mulai != '' && tanggal_akhir  != '') {
            $('#sales-table').DataTable().destroy();
            fetch_data(tanggal_mulai, tanggal_akhir, customer, jenis, pajak);
        } else {
            alert('Isi kedua filter tanggal mulai dan tanggal akhir');
        }
    });

    $('#customer_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var customer = $('#customer_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        $('#sales-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, customer, jenis, pajak);
    });

    $('#jenis_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var customer = $('#customer_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        $('#sales-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, customer, jenis, pajak);;
    });

    $('#pajak_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var customer = $('#customer_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        $('#sales-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, customer, jenis, pajak);;
    });

    $('#refresh').click(function () {
        $('#tanggal_mulai').val('');
        $('#tanggal_akhir').val('');
        $('#customer_filter')[0].selectedIndex = 0;
        $('#jenis_filter')[0].selectedIndex = 0;
        $('#pajak_filter')[0].selectedIndex = 0;
        $('#sales-table').DataTable().destroy();
        fetch_data();
    });

});
</script>
@stop