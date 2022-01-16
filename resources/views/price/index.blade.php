@extends('layouts.app')
@section('title') Tabel Harga @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h4 align="center">
                DAFTAR HARGA M23 CELLULAR<br />
                JLN. RAYA CIOMAS NO. 27D (DEPAN SD RIMBA)<br />
                GROSIR VOUCHER DAN PERDANA
            </h4>
            @if(session('status-edit'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-edit')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-perdana-tab" data-toggle="tab" href="#nav-perdana"
                        role="tab" aria-controls="nav-perdana" aria-selected="true">Harga Perdana</a>
                    <a class="nav-item nav-link" id="nav-voucher-tab" data-toggle="tab" href="#nav-voucher" role="tab"
                        aria-controls="nav-voucher" aria-selected="false">Harga Voucher</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-perdana" role="tabpanel"
                    aria-labelledby="nav-perdana-tab">
                    <p>
                        <table class="table table-striped table-sm" id="tabel_perdana">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%"><b>Provider</b></th>
                                    <th style="width: 25%"><b>Nama Barang</b></th>
                                    <th style="width: 15%"><b>Harga Jual</b></th>
                                    <th style="width: 15%"><b>Tanggal Update</b></th>
                                    <th style="width: 5%"><b>Aksi</b></th>
                                </tr>
                            </thead>
                        </table>
                </div>
                <div class="tab-pane fade" id="nav-voucher" role="tabpanel" aria-labelledby="nav-voucher-tab">
                    <p>
                        <table style="width: 100% !important" class="table table-striped table-sm" id="tabel_voucher">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%"><b>Provider</b></th>
                                    <th style="width: 30%"><b>Nama Barang</b></th>
                                    <th style="width: 15%"><b>Harga Jual</b></th>
                                    <th style="width: 15%"><b>Tanggal Update</b></th>
                                    <th style="width: 5%"><b>Aksi</b></th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tambah-edit-modal" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-judul"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-tambah-edit" name="form-tambah-edit" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-12">

                            <input type="hidden" name="id" id="id">

                            <div class="form-group">
                                <label for="name" class="col-sm-12 control-label">Harga Jual</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="harga_jual" name="harga_jual" value=""
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-offset-2 col-sm-12">
                                <button type="submit" class="btn btn-primary btn-block" id="tombol-simpan"
                                    value="create">Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function() {
    $('#tabel_perdana').DataTable({
        pageLength : 25,
        processing: true,
        serverSide: true,
        ordering : false,
        ajax: `{{ route('price.perdana') }}`,
        columns: [
            { data: 'nama_provider', name: 'providers.nama' },
            { data: 'nama', name: 'nama' },
            { data: 'harga', name: 'harga' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action' }
        ]
    });
});

$(function() {
    var voucher = $('#tabel_voucher').DataTable({
        pageLength : 25,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: `{{ route('price.voucher') }}`,
        columns: [
            { data: 'nama_provider', name: 'providers.nama' },
            { data: 'nama', name: 'nama' },
            { data: 'harga', name: 'harga' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action' }
        ]
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.hash == '#nav-voucher') {
            voucher.columns.adjust().draw()
        }
    })
});

</script>
@stop