@extends('layouts.app')
@section('title') Hutang Pembelian @endsection
@section('content')
<div class="container-fluid">
    <h2 align="center">
        HUTANG PEMBELIAN<br>
    </h2>
    <hr class="my-3">
    <br>
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
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-create')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <br>
            <div style="float:left; margin-right : 10px">*Keterangan :</div>
            <div style="width:30px; background-color:red; color:white;float:left">&nbsp;</div>
            <div style="float:left; margin-left : 10px">(sudah melewati tanggal jatuh tempo)</div>
            <div class="clear"></div>
        </div>
        <div class="col-md-12">
            <br>
            <div class="card">
                <div class="card-header">{{ __('Hutang Pembelian') }}</div>
                <div class="card-body">
                    <table class="table-bordered" id="purchases-table">
                        <thead>
                            <tr>
                                <th style="width: 10%;">
                                    <b>Tanggal</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Invoice</b></th>
                                <th style="width: 10%; text-align: right;">
                                    <b>Total</b></th>
                                <th style="width: 10%; text-align: right;">
                                    <b>Pajak</b></th>
                                <th style="width: 15%; text-align: right;">
                                    <b>Jatuh Tempo</b></th>
                                <th style="width: 15%; text-align: center;"><b>Supplier</b></th>
                                <th style="width: 15%"><b>Status</b></th>
                                <th style="width: 10%; text-align: right;"><b>Tanggal Lunas</b></th>
                                <th style="width: 2%"><b></b></th>
                            </tr>
                            <tr>
                                <th style="width: 10%; background-color:black"></th>
                                <th style="width: 15%; vertical-align: middle; background-color:black"></th>
                                <th style="width: 10%; vertical-align: middle; background-color:black"></th>
                                <th style="width: 10%; text-align: right;">
                                    <select class="form-control form-control-sm" name="pajak_filter" id="pajak_filter">
                                        <option value=""></option>
                                        <option value="Non PPN">Non PPN</option>
                                        <option value="PPN">PPN</option>
                                    </select>
                                </th>
                                <th style="width: 15%; text-align: right; background-color:black"></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <select name="supplier_filter" id="supplier_filter"
                                        class="form-control form-control-sm">
                                        <option value=""></option>
                                        @foreach($suppliers as $row)
                                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th style="width: 15%">
                                    <select class="form-control form-control-sm" name="status_filter"
                                        id="status_filter">
                                        <option value=""></option>
                                        <option value="1">LUNAS</option>
                                        <option value="0">BELUM LUNAS</option>
                                    </select>
                                </th>
                                <th style="width: 10%; text-align: right; background-color:black"></th>
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

    function fetch_data(tanggal_mulai = '', tanggal_akhir = '', pajak = '', supplier = '', is_lunas = '') {
        $('#purchases-table').DataTable({
            autoWidth: false, 
            pageLength: 300,
            lengthMenu: [100, 200, 300, 400, 500],
            processing: true,
            serverSide: true,
            ordering : false,
            searching : false,
            ajax: {
                url: "{{ route('purchases.debt') }}",
                data: {
                    tanggal_mulai: tanggal_mulai,
                    tanggal_akhir: tanggal_akhir,
                    pajak: pajak,
                    supplier: supplier,
                    is_lunas: is_lunas
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
                    data: 'total',
                    name: 'total',
                    className: "text-right"
                },
                {
                    data: 'pajak',
                    name: 'pajak',
                    className: "text-right"
                },
                {
                    data: 'jatuh_tempo',
                    name: 'jatuh_tempo',
                    className: "text-right"
                },
                {
                    data: 'nama_supplier',
                    name: 'suppliers.nama_supplier'
                },
                {
                    data: 'is_lunas',
                    name: 'is_lunas'
                },
                {
                    data: 'tanggal_lunas',
                    name: 'tanggal_lunas',
                    className: "text-right"
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],

            createdRow: (row, data, dataIndex, cells) => {
                $(cells[6]).css('background-color', data.status_color)
            }
        });
    }

    $('#filter').click(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var pajak = $('#pajak_filter').val();
        var supplier = $('#supplier_filter').val();
        var is_lunas = $('#status_filter').val();
        if (tanggal_mulai != '' && tanggal_akhir  != '') {
            $('#purchases-table').DataTable().destroy();
            fetch_data(tanggal_mulai, tanggal_akhir, pajak, supplier, is_lunas);
        } else {
            alert('Isi kedua filter tanggal mulai dan tanggal akhir');
        }
    });

    $('#supplier_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var pajak = $('#pajak_filter').val();
        var jatuh_tempo = $('#tempo_filter').val();
        var is_lunas = $('#status_filter').val();
        $('#purchases-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, pajak, supplier, is_lunas);
    });

    $('#pajak_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        var jatuh_tempo = $('#tempo_filter').val();
        var is_lunas = $('#status_filter').val();
        $('#purchases-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, pajak, supplier, is_lunas);
    });

    $('#status_filter').change(function () {
        var tanggal_mulai = $('#tanggal_mulai').val();
        var tanggal_akhir = $('#tanggal_akhir').val();
        var supplier = $('#supplier_filter').val();
        var jenis = $('#jenis_filter').val();
        var pajak = $('#pajak_filter').val();
        var jatuh_tempo = $('#tempo_filter').val();
        var is_lunas = $('#status_filter').val();
        $('#purchases-table').DataTable().destroy();
        fetch_data(tanggal_mulai, tanggal_akhir, pajak, supplier, is_lunas);
    });

    $('#refresh').click(function () {
        $('#tanggal_mulai').val('');
        $('#tanggal_akhir').val('');
        $('#supplier_filter')[0].selectedIndex = 0;
        $('#pajak_filter')[0].selectedIndex = 0;
        $('#status_filter')[0].selectedIndex = 0;
        $('#purchases-table').DataTable().destroy();
        fetch_data();
    });

});
</script>
@stop