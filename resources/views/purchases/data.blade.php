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
                <input type="text" placeholder="Tanggal Mulai" class="form-control mb-2 mr-sm-2"
                    id="tanggal_mulai" name="tanggal_mulai" autocomplete="off">
                <div class="input-group mb-2 mr-sm-2">
                    <input type="text" placeholder="Tanggal Akhir" class="form-control" id="tanggal_akhir"
                        name="tanggal_akhir" autocomplete="off">
                </div>
                <button type="button" name="filter" id="filter" class="btn btn-primary mb-2">Tampilkan</button>&nbsp;
                <button type="button" name="refresh" id="refresh" class="btn btn-danger mb-2">Hapus Tanggal</button>
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
                                <th style="width: 20%; vertical-align: middle;">
                                    <b>Invoice</b></th>
                                <th style="width: 15%; vertical-align: middle;">
                                    <b>Supplier</b></th>
                                <th style="width: 10%"><b>Pajak</b></th>
                                <th style="width: 10%; vertical-align: middle;">
                                    <b>Total</b></th>
                                <th style="width: 25%; vertical-align: middle;" class="text-center">
                                    <b>Keterangan</b></th>
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
            pageLength: 25,
            processing: true,
            serverSide: true,
            ordering : false,
            searching : false,
            ajax: {
                url: "{{ route('purchases.data') }}",
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
                    data: 'nama_supplier',
                    name: 'suppliers.nama'
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
                    data: 'action',
                    name: 'action'
                }
            ]
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