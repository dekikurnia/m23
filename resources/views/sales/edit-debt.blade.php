@extends('layouts.app')
@section('title') Ubah Piutang Penjualan @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="alert alert-danger" style="display:none"></div>
            <div class="alert alert-success print-success-msg" style="display:none">
                <ul></ul>
            </div>
            <div class="card">
                <div class="card-header">{{ __('Ubah Piutang Penjualan') }}</div>
                <div class="card-body">
                    <table class="table borderless table-sm">
                        <tr>
                            <th style="width: 15%">Nomor Invoice</th>
                            <td style="width: 2%">:</td>
                            <td>{{$sale->invoice}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tanggal</th>
                            <td style="width: 2%">:</td>
                            <td>{{ \Carbon\Carbon::parse($sale->tanggal)->format('d-F-Y')}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Customer</th>
                            <td style="width: 2%">:</td>
                            <td>{{$sale->customer->nama}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tipe Penjualan</th>
                            <td style="width: 2%">:</td>
                            <td>{{$sale->jenis}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Cara Bayar</th>
                            <td style="width: 2%">:</td>
                            <td>{{$sale->cara_bayar}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Pajak</th>
                            <td style="width: 2%">:</td>
                            <td id="row-pajak">{{$sale->pajak}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Jatuh Tempo</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($sale->jatuh_tempo))
                                -
                                @else
                                {{ \Carbon\Carbon::parse($sale->jatuh_tempo)->format('d-m-Y')}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tanggal Lunas</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($sale->tanggal_lunas))
                                -
                                @else
                                {{ \Carbon\Carbon::parse($sale->tanggal_lunas)->format('d-m-Y')}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Keterangan</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($sale->keterangan))
                                -
                                @else
                                {{ $sale->keterangan }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <hr class="my-3">
            <table class="table borderless table-sm" id="sales-table">
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
                    @foreach ($saleDetails as $saleDetail)
                    <tr>
                        <td style="width: 15%">{{$saleDetail->nama_provider}}</td>
                        <td style="width: 30%">{{ $saleDetail->nama }}</td>
                        <td style="text-align: right; width: 10%">{{ $saleDetail->kuantitas }}</td>
                        <td style="text-align: right; width: 10%">
                            {{ number_format($saleDetail->harga, 0, ',', '.') }}</td>
                        <td style="text-align: right; width: 10%" class="sub_total">
                            {{ number_format($saleDetail->sub_total, 0, ',', '.') }}</td>
                        <!--<td></td>-->
                        <div style="display: none">{{$total += $saleDetail->sub_total}}</div>
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
                    @if($sale->pajak == 'PPN')
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
            <hr class="my-3">
            <form id="update-debt">
                <div class="form-inline">
                    <div class="form-group">
                        <label>Tanggal Pelunasan</label>
                        <input value="" type="text" name="tanggal_lunas" class="form-control mx-sm-3 datepicker"
                            autocomplete="off">
                        @if ($errors->has('tanggal_lunas'))
                        <span class="text-danger">{{ $errors->first('tanggal_lunas') }}</span>
                        @endif
                    </div>
                </div>
                <br>
                <input class="btn btn-primary" id="save" type="submit" value="Bayar" />
                @if($sale->jenis == 'Grosir')
                <a class="btn btn-success text-white" href="{{route('wholesales.edit',
                [$sale->id])}}">Ubah</a>
                @endif
                @if($sale->jenis == 'Gudang')
                <a class="btn btn-success text-white" href="{{route('warehouses.edit',
                [$sale->id])}}">Ubah</a>
                @endif
                <a href="/sales/debt" class="btn btn-dark">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function () {
    $(".datepicker").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'top'
    });

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

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#save').on('click', function (e) {
    e.preventDefault();
    var dataString = $("#update-debt").serialize();
    $.ajax({
        type: 'json',
        method: 'PUT',
        url: `{{ route('sales.update-debt', [$sale->id]) }}`,
        data: dataString,
        success: function (data) {
            Swal.fire({
                icon: 'success',
                title: "Sukses",
                text: data.msg
            }).then(function () {
                window.location.href = "/sales/debt";
            });
        },

        error: function (data) {
            $('.alert-danger').empty();
            $.each(data.responseJSON.msg, function (key, value) {
                $('.alert-danger').show();
                $('.alert-danger').append(value);
                $(window).scrollTop(0);

                $('.alert-danger').delay(4000).slideUp(200, function () {
                    $(this).alert('');
                });
            });
        }
    });
});
</script>
@stop