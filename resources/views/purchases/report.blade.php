@extends('layouts.app')
@section('title') Laporan Pembelian @endsection
@section('content')
<div style="margin-top:10px;" class="container-fluid">
    <div style="margin-top:20px;" class="row justify-content-center">
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
                <!-- <button type="button" name="pdf" id="pdf" class="btn btn-success mb-2">Export PDF</button> !-->
            </form>
        </div>
    </div>
    <hr class="my-3">
    <div class="col-md-12">
        <h5 align="center">
            <b>LAPORAN PEMBELIAN</b>
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
    <br />
    <table class="table table-sm">
        <thead>
            <tr>
                <th></th>
                <th>Tanggal</th>
                <th style="text-align: center">Invoice</th>
                <th style="text-align: center; width: 20%">Supplier</th>
                <th>Pajak</th>
                <th>Status</th>
                <th>Tanggal Lunas</th>
            </tr>
            <tr class="info">
                <th></th>
                <th style="text-align: right; width: 15%">Provider</th>
                <th style="text-align: right; width: 20%">Nama Barang</th>
                <th></th>
                <th style="text-align: right; width: 15%">Kuantitas</th>
                <th style="text-align: right; width: 15%">Harga Beli</th>
                <th style="text-align: right; width: 15%">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
            <tr class="table-active">
                <td></td>
                <td>{{$purchase->tanggal}}</td>
                <td>{{$purchase->invoice}}</td>
                <td style="text-align: center; width: 20%">{{$purchase->supplier->nama}}</td>
                <td>{{$purchase->pajak}}</td>
                @if($purchase->is_lunas == 0)
                <td>BELUM LUNAS</td>
                @else
                <td>LUNAS</td>
                @endif
                <td>{{$purchase->tanggal_lunas}}</td>
            </tr>
            <td colspan="7">
                <div style="display: none">
                    {{ $subTotal = 0 }}
                    {{ $ppn = 0 }}
                    {{ $total = 0 }}
                </div>
                @foreach ($purchase->purchaseDetails as $purchaseDetail)
                <tr class="table-light bordered">
                    <td>
                    <td style="text-align: right; width: 15%">{{ $purchaseDetail->item->provider->nama}}</td>
                    <td style="text-align: right; width: 15%">{{ $purchaseDetail->item->nama}}</td>
                    <td>
                    <td style="text-align: right; width: 15%">
                        {{ number_format($purchaseDetail->kuantitas, 0, ',', '.') }}</td>
                    <td style="text-align: right; width: 15%">
                        {{ number_format($purchaseDetail->harga, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; width: 15%">
                        {{ number_format(($purchaseDetail->kuantitas * $purchaseDetail->harga), 0, ',', '.')}}</td>
                    <div style="display: none">
                        {{$subTotal += ($purchaseDetail->kuantitas * $purchaseDetail->harga)}}
                    </div>
                    <div style="display: none">{{$ppn = ($subTotal* 0.1)}}</div>
                    <div style="display: none">{{$total = ($ppn + $subTotal)}}</div>
                </tr>
                @endforeach
                @if($purchase->pajak == 'Non PPN')
                <tr class="row-non">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold">
                        TOTAL : </td>
                    <td style="text-align: right; width: 15%; font-weight:bold;" class="total-non">
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
                    <td style="text-align: right; width: 15%; font-weight:bold">
                        SUB TOTAL : </td>
                    <td style="text-align: right; width: 15%; font-weight:bold;" class="total-non">
                        {{ number_format($subTotal, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold">PPN :</td>
                    <td style="text-align: right; width: 15%; font-weight:bold;" class="ppn">
                        {{ number_format($ppn, 0, ',', '.') }}</td>
                </tr>
                <tr class="row-ppn">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                    <td style="text-align: right; width: 15%"></td>
                    <td style="text-align: right; width: 15%; font-weight:bold">TOTAL :</td>
                    <td style="text-align: right; width: 15%; font-weight:bold;" class="total-ppn">
                        {{ number_format($total, 0, ',', '.') }}</td>
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
                <td style="text-align: right; width: 15%; font-weight:bold; font-size: 16px;">GRAND TOTAL :</td>
                <td style="text-align: right; width: 15%; font-weight:bold; font-size: 16px;" class="grand-total">
                </td>
            </tr>
        </tbody>
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