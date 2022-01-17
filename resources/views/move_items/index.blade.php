@extends('layouts.app')
@section('title') Daftar Pindah Barang @endsection
@section('content')
<div class="container-fluid">
    <h2 align="center">
        DAFTAR PINDAH BARANG<br>
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
                <div class="card-header">{{ __('Daftar Pindah Barang') }}</div>
                <div class="card-body">
                    <a href="{{route('move-items.create')}}" class="btn btn-primary">Tambah</a>
                    <p>
                    <table class="table table-striped table-sm" id="move-items-table">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;">
                                    <b>Tanggal</b></th>
                                <th style="vertical-align: middle;">
                                    <b>Nomor</b></th>
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
        $('#move-items-table').DataTable({
            pageLength: 300,
            lengthMenu: [100, 200, 300, 400, 500],
            processing: true,
            serverSide: true,
            ordering : false,
            searching : false,
            ajax: {
                url: "{{ route('move-items.index') }}",
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
                    data: 'nomor',
                    name: 'nomor'
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
            $('#move-items-table').DataTable().destroy();
            fetch_data(tanggal_mulai, tanggal_akhir);
        } else {
            alert('Isi kedua filter tanggal mulai dan tanggal akhir');
        }
    });

    $('#refresh').click(function () {
        $('#tanggal_mulai').val('');
        $('#tanggal_akhir').val('');
        $('#move-items-table').DataTable().destroy();
        fetch_data();
    });

});
</script>
@stop