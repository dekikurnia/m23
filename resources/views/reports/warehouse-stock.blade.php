@extends('layouts.app')
@section('title') Laporan Stok Gudang @endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center input-daterange">
        <form class="form-inline">
            <input type="text" value="{{Request::get('tanggal_mulai')}}" placeholder="Tanggal Mulai"
                class="form-control mb-2 mr-sm-2" id="tanggal_mulai" name="tanggal_mulai" autocomplete="off">
            <div class="input-group mb-2 mr-sm-2">
                <input type="text" value="{{Request::get('tanggal_akhir')}}" placeholder="Tanggal Akhir"
                    class="form-control" id="tanggal_akhir" name="tanggal_akhir" autocomplete="off">
            </div>
            <button type="submit" id="filter" class="btn btn-primary mb-2">Tampilkan
                Tanggal</button>&nbsp;
            <button type="submit" id="refresh" class="btn btn-danger mb-2">Hapus
                Tanggal</button>&nbsp;
        </form>
    </div>
    <hr class="my-3">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h5 align="center">
                <b>LAPORAN STOK GUDANG</b>
            </h5>
        </div>
        <div class="col-md-12">
            <p align="center">
                <b>
                    @if( empty(Request::get('tanggal_mulai')))
                    {{ Carbon\Carbon::today()->format('d F Y')}} - {{Carbon\Carbon::today()->format('d F Y')}}
                    @else
                    {{date('d F Y', strtotime(Request::get('tanggal_mulai')))}} -
                    {{date('d F Y', strtotime(Request::get('tanggal_akhir')))}} 
                    @endif
                </b>
            </p>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="align-middle" rowspan="2">Provider</th>
                        <th class="align-middle" rowspan="2">Barang</th>
                        <th class="align-middle" rowspan="2" style="text-align: center">Stok Awal</th>
                        <th class="align-middle" rowspan="2" style="text-align: center">Masuk Barang</th>
                        <th class="align-middle" colspan="2" style="text-align: center">Keluar Barang</th>
                        <th class="align-middle" rowspan="2" style="text-align: center">Stok Akhir</th>
                    </tr>
                    <tr>
                        <th style="text-align: center">Pindah ke Toko</th>
                        <th style="text-align: center">Langsung Jual</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $stock)
                    <tr>
                        <td>{{$stock->nama_provider}}</td>
                        <td style="width: 20%">{{$stock->nama_item}}</td>
                        <td style="text-align: right;">{{$stock->stok_awal}}</td>
                        <td style="text-align: right;">{{$stock->kuantitas_pembelian}}</td>
                        <td style="text-align: right;">{{$stock->kuantitas_pindah}}</td>
                        <td style="text-align: right;">{{$stock->kuantitas_gudang}}</td>
                        <td style="text-align: right;">{{$stock->stok_akhir}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function () {
        $(".input-daterange").datepicker({
            todayBtn: 'linked',
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        $('#filter').click(function () {
            var tanggal_mulai = $('#tanggal_mulai').val();
            var tanggal_akhir = $('#tanggal_akhir').val();
            if (tanggal_mulai == '' && tanggal_akhir == '') {
                alert('Isi kedua filter tanggal mulai dan tanggal akhir');
            }
        });

        $('#refresh').click(function () {
            $('#tanggal_mulai').val('');
            $('#tanggal_akhir').val('');
        });

    });
</script>
@stop