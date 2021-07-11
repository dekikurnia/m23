@extends('layouts.app')
@section('title') Laporan Penjualan Gudang (Summary) @endsection
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
            <b>LAPORAN PENJUALAN GUDANG (SUMMARY)</b>
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
                <th style="width: 7%">Tanggal</th>
                <th style="width: 16%">Invoice</th>
                <th style="width: 15%">Customer</th>
                <th style="width: 10%">Pajak</th>
                <th style="width: 10%">Cara Bayar</th>
                <th style="width: 15%">Keterangan</th>
                <th style="width: 10%">Status</th>
                <th style="width: 20%">Tanggal Lunas</th>
            </tr>
            <tr class="info">
                <th></th>
                <th style="text-align: right; width: 18%">Provider</th>
                <th style="text-align: right; width: 15%">Nama Barang</th>
                <th style="width: 10%"></th>
                <th style="width: 10%"></th>
                <th style="text-align: right; width: 15%">Kuantitas</th>
                <th style="text-align: right; width: 10%">Harga Jual</th>
                <th style="text-align: right; width: 20%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
            <tr class="table-active">
                <td>{{$sale->tanggal}}</td>
                <td style="width: 16%">{{$sale->invoice}}</td>
                <td style="width: 15%">{{$sale->customer->nama}}</td>
                <td style="width: 10%">{{$sale->pajak}}</td>
                <td style="width: 10%">{{$sale->cara_bayar}}</td>
                <td style="width: 15%">{{$sale->keterangan}}</td>
                @if($sale->is_lunas == 0)
                <td style="width: 10%">BELUM LUNAS</td>
                @else
                <td style="width: 10%">LUNAS</td>
                @endif
                <td style="width: 20%">{{$sale->tanggal_lunas}}</td>
            </tr>
            <td colspan="9">
                <div style="display: none">
                    {{ $subTotal = 0 }}
                    {{ $ppn = 0 }}
                    {{ $total = 0 }}
                </div>
                @foreach ($sale->saleDetails as $saleDetail)
                <tr class="table-light bordered">
                    <td></td>
                    <td style="text-align: right; width: 16%; font-size: 12px;">{{ !empty($saleDetail->item->provider) ? $saleDetail->item->provider->nama:''}}</td>
                    <td style="text-align: right; width: 15%; font-size: 12px;">{{ !empty($saleDetail->item) ? $saleDetail->item->nama:''}}</td>
                    <td style="text-align: center; width: 10%"></td>
                    <td style="width: 10%"></td>
                    <td style="text-align: right; width: 10%; font-size: 12px;">{{ number_format($saleDetail->kuantitas, 0, ',', '.') }}</td>
                    <td style="text-align: right; width: 10%; font-size: 12px;">{{ number_format($saleDetail->harga, 0, ',', '.') }}
                    <td style="text-align: right; width: 20%; font-size: 12px;">
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
                    <td></td>
                    <td></td>
                    <td style="text-align: right; width: 10%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px;">
                        TOTAL : </td>
                    <td style="text-align: right; width: 20%; font-weight:bold; font-size: 12px;" class="total-non">
                        {{ number_format($subTotal, 0, ',', '.') }} 
                    </td>
                </tr>
                @else
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; width: 10%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px;">
                        SUB TOTAL : </td>
                    <td style="text-align: right; width: 20%; font-weight:bold; font-size: 12px;" class="total-non">
                        {{ number_format($subTotal, 0, ',', '.') }} 
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; width: 10%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px;">PPN :</td>
                    <td style="text-align: right; width: 20%; font-weight:bold; font-size: 12px;" class="ppn">{{ number_format($ppn, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-ppn">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; width: 10%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold; font-size: 12px;">TOTAL :</td>
                    <td style="text-align: right; width: 20%; font-weight:bold; font-size: 12px;" class="total-ppn">{{ number_format($total, 0, ',', '.') }}</td>
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
                <td></td>
                <td></td>
                <td style="text-align: right; width: 15%"></td>
                <td style="text-align: right; width: 15%; font-weight:bold; font-size: 14px;">GRAND TOTAL :</td>
                <td style="text-align: right; width: 20%; font-weight:bold; font-size: 14px;" class="grand-total"></td>
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