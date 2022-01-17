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
                <button type="button" name="refresh" id="refresh" class="btn btn-danger mb-2">Hapus Tanggal</button>
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
                    <table class="table-striped" id="purchases-table">
                        <thead>
                            <tr>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Tanggal</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Invoice</b></th>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Total</b></th>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Pajak</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Jatuh Tempo</b></th>
                                <th style="width: 15%"><b>Supplier</b></th>
                                <th style="width: 15%"><b>Status</b></th>
                                <th style="width: 10%"><b>Tanggal Lunas</b></th>
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

    function fetch_data(tanggal_mulai = '', tanggal_akhir = '') {
        $('#purchases-table').DataTable({
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
                    tanggal_akhir: tanggal_akhir
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
                    name: 'total'
                },
                {
                    data: 'pajak',
                    name: 'pajak'
                },
                {
                    data: 'jatuh_tempo',
                    name: 'jatuh_tempo',
                    className: "text-center"
                },
                {
                    data: 'nama_supplier',
                    name: 'suppliers.nama_supplier',
                },
                {
                    data: 'is_lunas',
                    name: 'is_lunas'
                },
                {
                    data: 'tanggal_lunas',
                    name: 'tanggal_lunas',
                    className: "text-center"
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
        if (tanggal_mulai != '' && tanggal_akhir  != '') {
            $('#purchases-table').DataTable().destroy();
            fetch_data(tanggal_mulai, tanggal_akhir);
        } else {
            alert('Isi kedua filter tanggal mulai dan tanggal akhir');
        }
    });

    $('#refresh').click(function () {
        $('#tanggal_mulai').val('');
        $('#tanggal_akhir').val('');
        $('#purchases-table').DataTable().destroy();
        fetch_data();
    });

});
</script>
@stop