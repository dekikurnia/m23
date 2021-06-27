@extends('layouts.app')
@section('title') Laporan Stok Toko @endsection
@section('content')
<div class="container-fluid">
    <h2 align="center">
        LAPORAN STOK TOKO<br>
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
                <div class="card-header">{{ __('Laporan Stok Toko') }}</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="align-middle" rowspan="2" style="text-align: center">Provider</th>
                                <th class="align-middle" rowspan="2" style="text-align: center">Barang</th>
                                <th class="align-middle" rowspan="2" style="text-align: center">Stok Awal</th>
                                <th class="align-middle" rowspan="2" style="text-align: center">Masuk Barang</th>
                                <th class="align-middle" colspan="2" style="text-align: center">Keluar Barang</th>
                                <th class="align-middle" rowspan="2" style="text-align: center">Stok Akhir</th>
                            </tr>
                            <tr>
                                <th style="text-align: center">Retail</th>
                                <th style="text-align: center">Grosir</th>
                             </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection