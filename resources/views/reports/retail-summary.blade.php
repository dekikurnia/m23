@extends('layouts.app')
@section('title') Laporan Penjualan Retail @endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="row justify-content-center input-daterange">
            <form class="form-inline">
                <input type="text"  value="{{Request::get('tanggal_mulai')}}" placeholder="Tanggal Mulai" class="form-control mb-2 mr-sm-2" id="tanggal_mulai"
                    name="tanggal_mulai" autocomplete="off">
                <div class="input-group mb-2 mr-sm-2">
                    <input type="text" value="{{Request::get('tanggal_akhir')}}" placeholder="Tanggal Akhir" class="form-control" id="tanggal_akhir"
                        name="tanggal_akhir" autocomplete="off">
                </div>
                <button type="submit" id="filter" class="btn btn-primary mb-2">Tampilkan
                    Tanggal</button>&nbsp;
                <button type="submit" id="refresh" class="btn btn-danger mb-2">Hapus
                    Tanggal</button>&nbsp;
                <!-- <button type="button" name="pdf" id="pdf" class="btn btn-success mb-2">Export PDF</button> !-->
            </form>
        </div>
    </div>
    <hr class="my-3">
    <div class="col-md-12">
        <h5 align="center">
            <b>LAPORAN PENJUALAN RETAIL (SUMMARY)</b>
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
    <br/>
    <table class="table table-sm">
        <thead>
            <tr>
                <th></th>
                <th>Tanggal</th>
                <th style="text-align: center">Invoice</th>
                <th style="text-align: center; width: 20%"></th>
                <th></th>
                <th>Keterangan</th>
                <th></th>
            </tr>
            <tr class="info">
                <th></th>
                <th style="text-align: right; width: 15%">Provider</th>
                <th style="text-align: right; width: 20%">Nama Barang</th>
                <th></th>
                <th style="text-align: right; width: 15%">Kuantitas</th>
                <th style="text-align: right; width: 15%">Harga Jual</th>
                <th style="text-align: right; width: 15%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr class="table-active">
                <td></td>
                <td>{{$sale->tanggal}}</td>
                <td>{{$sale->invoice}}</td>
                <td style="text-align: center; width: 20%"></td>
                <td></td>
                <td>{{$sale->keterangan}}</td>
                <td></td>
            </tr>
            <td colspan="7">
                <div style="display: none">
                    {{ $subTotal = 0 }}
                    {{ $ppn = 0 }}
                    {{ $total = 0 }}
                </div>
                @foreach ($sale->saleDetails as $saleDetail)
                <tr class="table-light bordered">
                    <td>
                    <td style="text-align: right; width: 15%; font-size: 12px">{{ !empty($saleDetail->item->provider) ? $saleDetail->item->provider->nama:''}}</td>
                    <td style="text-align: right; width: 15%; font-size: 12px">{{ !empty($saleDetail->item) ? $saleDetail->item->nama:''}}</td>
                    <td>
                    <td style="text-align: right; width: 15%; font-size: 12px">{{ number_format($saleDetail->kuantitas, 0, ',', '.') }}</td>
                    <td style="text-align: right; width: 15%; font-size: 12px">{{ number_format($saleDetail->harga, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; width: 15%; font-size: 12px">
                        {{ number_format(($saleDetail->kuantitas * $saleDetail->harga), 0, ',', '.')}}</td>
                    <div style="display: none">{{$subTotal += ($saleDetail->kuantitas * $saleDetail->harga)}}</div>
                    <div style="display: none">{{$ppn = ($subTotal* 0.1)}}</div>
                    <div style="display: none">{{$total = ($ppn + $subTotal)}}</div>
                </tr>
                @endforeach
                @if($sale->pajak == 'Non PPN')
                <tr class="row-non">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px">
                        TOTAL : </td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px" class="total-non">
                        {{ number_format($subTotal, 0, ',', '.') }} 
                    </td>
                </tr>
                @else
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px">
                        SUB TOTAL : </td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px" class="total-non">
                        {{ number_format($subTotal, 0, ',', '.') }} 
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px">PPN :</td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px" class="ppn">{{ number_format($ppn, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-ppn">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px">TOTAL :</td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px" class="total-ppn">{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                </tr>
                <tr>
                </tr>
            </td>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>
                <td style="text-align: right; width: 15%"></td>
                <td style="text-align: right; width: 15%; font-weight:bold; font-size: 14px;">GRAND TOTAL :</td>
                <td style="text-align: right; width: 15%; font-weight:bold; font-size: 14px;" class="grand-total"></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colSpan="10">
                    {{$sales->appends(Request::all())->links()}}
                </td>
            </tr>
        </tfoot>
    </table>
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