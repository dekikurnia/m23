@extends('layouts.app')
@section('title') Detail Pembelian @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Detail Pembelian') }}</div>
                <div class="card-body">
                    <table class="table borderless table-sm">
                        <tr>
                            <th style="width: 15%">Nomor Invoice</th>
                            <td style="width: 2%">:</td>
                            <td>{{$purchase->invoice}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tanggal</th>
                            <td style="width: 2%">:</td>
                            <td>{{ \Carbon\Carbon::parse($purchase->tanggal)->translatedFormat('d/m/Y')}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Supplier</th>
                            <td style="width: 2%">:</td>
                            <td>{{$purchase->supplier->nama}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Cara Bayar</th>
                            <td style="width: 2%">:</td>
                            <td>{{$purchase->cara_bayar}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Pajak</th>
                            <td style="width: 2%">:</td>
                            <td id="row-pajak">{{$purchase->pajak}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Jatuh Tempo</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($purchase->jatuh_tempo))
                                -
                                @else
                                {{ \Carbon\Carbon::parse($purchase->jatuh_tempo)->format('d/m/Y')}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tanggal Lunas</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($purchase->tanggal_lunas))
                                -
                                @else
                                {{ \Carbon\Carbon::parse($purchase->tanggal_lunas)->format('d/m/Y')}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Keterangan</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($purchase->keterangan))
                                -
                                @else
                                {{ $purchase->keterangan }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr class="my-3">
            <table class="table borderless table-sm" id="purchases-table">
                <thead class="thead-light">
                    <tr class="titlerow">
                        <th style="text-align: center; width: 15%"><b>Provider</b></th>
                        <th style="text-align: center; width: 30%"><b>Nama Barang</b></th>
                        <th style="text-align: right; width: 10%"><b>Kuantitas</b></th>
                        <th style="text-align: right; width: 10"><b>Harga Beli</b></th>
                        <th style="text-align: right; width: 10%"><b>Sub Total</b></th>
                    </tr>
                </thead>
                <tfoot>
                    <div style="display: none">
                        {{ $total = 0 }}
                    </div>
                    @foreach ($purchaseDetails as $purchaseDetail)
                    <tr style="background-color:#FFFFFF">
                        <td style="width: 15%">{{$purchaseDetail->nama_provider}}</td>
                        <td style="width: 30%">{{ $purchaseDetail->nama }}</td>
                        <td style="text-align: right; width: 10%">{{ $purchaseDetail->kuantitas }}</td>
                        <td style="text-align: right; width: 10%">
                            {{ number_format($purchaseDetail->harga, 0, ',', '.') }}</td>
                        <td style="text-align: right; width: 10%" class="sub_total">
                            {{ number_format($purchaseDetail->sub_total, 0, ',', '.') }}</td>
                        <!--<td></td>-->
                        <div style="display: none">{{$total += $purchaseDetail->sub_total}}</div>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 30%"></td>
                        <td style="width: 5%"></td>
                        <td style="text-align: right;font-weight: bold; width: 10%"></td>
                        <td style="text-align: right;font-weight: bold; width: 5%">
                            <span id="grandotal"></span>
                        </td>
                        <!--<td></td>-->
                    </tr>
                    <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 30%"></td>
                        <td style="width: 5%"></td>
                        <td style="text-align: right;font-weight: bold; width: 10%">Total :</td>
                        <td style="text-align: right;font-weight: bold; width: 5%">
                            <span id="total">{{ number_format($total, 0, ',', '.') }}</span>
                        </td>
                        <!--<td></td>-->
                    </tr>
                    @if($purchase->pajak == 'PPN')
                    <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 30%"></td>
                        <td style="width: 5%"></td>
                        <td style="text-align: right;font-weight: bold; width: 10%">PPN :</td>

                        <td style="text-align: right;font-weight: bold; width: 5%">
                            <span id="ppn">0 </span>
                        </td>
                        <!--<td></td>-->
                    </tr>
                    @endif
                    <tr>
                        <td style="width: 15%"></td>
                        <td style="width: 30%"></td>
                        <td style="width: 5%"></td>
                        <td style="text-align: right;font-weight: bold; width: 10%">Grand Total :</td>

                        <td style="text-align: right;font-weight: bold; width: 5%">
                            <span id="grand-total">0</span>
                        </td>
                        <!--<td></td>-->
                    </tr>
                </tfoot>
            </table>
            <a href="/purchases/data" class="btn btn-dark">Kembali</a>
            <a class="btn btn-info text-white" href="{{route('purchases.edit',
            [$purchase->id])}}">Ubah</a>
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

    var optionValue = $("#row-pajak").text() == "PPN";
    var total = $("#total").text();

    if (optionValue) {
        var ppn = (reverseFormatNumber(total, 'id-ID')) * 0.1;
        $("#ppn").text(ppn.toLocaleString("id-ID"));

        var grandTotal = ppn + parseInt(reverseFormatNumber(total, 'id-ID'));
        $("#grand-total").text(grandTotal.toLocaleString("id-ID"));
    } else {
        $("#grand-total").text(total);
    }
});
</script>
@stop