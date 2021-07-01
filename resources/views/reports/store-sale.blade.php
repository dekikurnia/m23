@extends('layouts.app')
@section('title') Laporan Penjualan Toko berdasarkan Barang @endsection
@section('content')
<div class="container-fluid">
    <h4 align="center">
        LAPORAN PENJUALAN TOKO BERDASARKAN BARANG<br>
    </h4>
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
                <button type="button" name="refresh" id="refresh" class="btn btn-danger mb-2">Hapus Tanggal</button>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th class="align-middle" rowspan="2">Provider</th>
                        <th class="align-middle" rowspan="2">Barang</th>
                        <th class="align-middle" colspan="2" style="text-align: center">Retail</th>
                        <th class="align-middle" colspan="2" style="text-align: center">Grosir</th>
                        <th class="align-middle" colspan="2" style="text-align: center">Total</th>
                    </tr>
                    <tr>
                        <th style="text-align: center; width: 10%">Kuantitas</th>
                        <th style="text-align: center; width: 15%">Total</th>
                        <th style="text-align: center; width: 10%">Kuantitas</th>
                        <th style="text-align: center; width: 15%">Total</th>
                        <th style="text-align: center; width: 10%">Kuantitas</th>
                        <th style="text-align: center; width: 15%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                    <tr>
                        <td>{{$sale->nama_provider}}</td>
                        <td>{{$sale->nama_item}}</td>
                        <td style="text-align: right;">{{$sale->kuantitas_retail}}</td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
                        <td style="text-align: right;"></td>
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
        function reverseFormatNumber(val, locale) {
            var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
            var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
            var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
            reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
            return Number.isNaN(reversedVal) ? 0 : reversedVal;
        }

        $(function () {
            var grandTotalNon = 0;
            var grandTotalPPN = 0;
            $(".row-non .total-non").each(function (index, value) {
                currentRow = parseFloat($(this).text().replace(/\./g, ""))
                grandTotalNon += currentRow
            });
            $(".row-ppn .total-ppn").each(function (index, value) {
                currentRow = parseFloat($(this).text().replace(/\./g, ""))
                grandTotalPPN += currentRow
            });
            $(".grand-total").text((grandTotalNon+grandTotalPPN).toLocaleString("id-ID"))
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