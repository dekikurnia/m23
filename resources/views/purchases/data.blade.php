@extends('layouts.app')
@section('title') Data Pembelian @endsection
@section('content')
<div class="container-fluid">
    <h2 align="center">
        DATA PEMBELIAN<br>
    </h2>
    <hr class="my-3">
    <div class="row justify-content-center">
        <div class="row justify-content-center input-daterange">
            <form class="form-inline">
                <input type="text" placeholder="Tanggal Mulai" class="form-control mb-2 mr-sm-2" id="tanggal_mulai"
                    name="tanggal_mulai" autocomplete="off">
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
                <div class="card-header">{{ __('Data Pembelian') }}</div>
                <div class="card-body">
                    <table class="table table-striped table-sm" id="purchases-table">
                        <thead>
                            <tr>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Tanggal</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Invoice</b></th>
                                <th style="width: 20%; vertical-align: middle;">
                                    <b>Supplier</b></th>
                                <th style="width: 10%"><b>Pajak</b></th>
                                <th style="width: 10%; text-align: right;">
                                    <b>Total</b></th>
                                <th style="width: 25%; vertical-align: middle;" class="text-center">
                                    <b>Keterangan</b></th>
                                <th style="width: 2%"><b></b></th>
                            </tr>
                            <tr>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b></b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b></b></th>
                                <th style="width: 20%;">
                                    <select name="supplier_filter" id="supplier_filter"
                                        class="form-control form-control-sm">
                                        <option value=""></option>
                                        @foreach($supplier as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 12%">
                                    <select class="form-control form-control-sm" name="pajak_filter" id="pajak_filter">
                                        <option value=""></option>
                                        <option value="Non PPN">Non PPN</option>
                                        <option value="PPN">PPN</option>
                                    </select>
                                </th>
                                <th style="width: 10%; text-align: right;">
                                    <b></b></th>
                                <th style="width: 25%; vertical-align: middle;" class="text-center">
                                    <b></b></th>
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

    function fetch_data(tanggal_mulai = '', tanggal_akhir = '', supplier = '', pajak = '') {
        $('#purchases-table').DataTable({
            autoWidth: false, 
            pageLength: 300,
            lengthMenu: [100, 200, 300, 400, 500],
            processing: true,
            serverSide: true,
            ordering : false,
            searching : false,
            retrieve: true,
            ajax: {
                url: "{{ route('purchases.data') }}",
                data: {
                    tanggal_mulai: tanggal_mulai,
                    tanggal_akhir: tanggal_akhir,
                    supplier: supplier,
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
                    data: 'nama_supplier',
                    name: 'suppliers.nama'
                }, 
                {
                    data: function (data, type, dataToSet) {
                        return data.pajak + " | " + data.pajak2;
                    }
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
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    $('#filter').click(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var pajak = $('#pajak_filter').val();
        if (tanggal_mulai != '' && tanggal_akhir  != '') {
            $('#purchases-table').DataTable().destroy();
            fetch_data(tanggal_mulai, tanggal_akhir, supplier, pajak);
        } else {
            alert('Isi kedua filter tanggal mulai dan tanggal akhir');
        }
    });

    $('#supplier_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var pajak = $('#pajak_filter').val();
        $('#purchases-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, supplier, pajak);
    });

    $('#pajak_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var pajak = $('#pajak_filter').val();
        $('#purchases-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, supplier, pajak);
    });

    $('#refresh').click(function () {
        $('#tanggal_mulai').val('');
        $('#tanggal_akhir').val('');
        $('#supplier_filter')[0].selectedIndex = 0;
        $('#pajak_filter')[0].selectedIndex = 0;
        $('#purchases-table').DataTable().destroy();
        fetch_data();
    });

});
</script>
@stop