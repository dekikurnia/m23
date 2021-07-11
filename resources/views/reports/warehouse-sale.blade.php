@extends('layouts.app')
@section('title') Laporan Penjualan Gudang berdasarkan Barang @endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
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
    </div>
    <hr class="my-3">
    <div class="col-md-12">
        <h5 align="center">
            <b>LAPORAN PENJUALAN GUDANG BERDASARKAN BARANG</b>
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
                    <th class="align-middle" colspan="2" style="text-align: center">Gudang</th>
                </tr>
                <tr>
                    <th style="text-align: center; width: 10%">Kuantitas</th>
                    <th style="text-align: center; width: 15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                <tr>
                    <td>{{$sale->nama_provider}}</td>
                    <td>{{$sale->nama_item}}</td>
                    <td style="text-align: right;">{{ number_format($sale->kuantitas_gudang, 0, ',', '.') }}</td>
                    <td style="text-align: right;" class="harga-gudang">
                        {{ number_format($sale->harga_gudang, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right; font-weight:bold; font-size: 14px;">Total</td>
                    <td style="text-align: right; font-weight:bold; font-size: 14px;" class="total"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function () {

        $(function () {
            var total = 0;
            $(".harga-gudang").each(function (index, value) {
                currentRow = parseFloat($(this).text().replace(/\./g, ""))
                total += currentRow
            });
            $(".total").text((total).toLocaleString("id-ID"))
        });


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