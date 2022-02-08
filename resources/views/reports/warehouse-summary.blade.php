@extends('layouts.app')
@section('title') Laporan Penjualan Gudang (Summary) @endsection
@section('content')
<div class="container-fluid">
    <form>
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header text-center">{{ __('Filter Laporan Penjualan Gudang (Summary)') }}</div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="form-group">
                            <label>Customer</label>
                            <select class="form-control form-control-sm" name="customer_filter" id="customer_filter">
                                <option value="">--Pilih Customer--</option>
                                @foreach($customers as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Cara Bayar</label>
                            <select class="form-control form-control-sm" name="bayar_filter" id="bayar_filter">
                                <option value="">--Semua Tipe--</option>
                                <option value="Kas">Kas</option>
                                <option value="Kredit">Kredit</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pajak</label>
                            <select class="form-control form-control-sm" name="pajak_filter" id="pajak_filter">
                                <option value="">--Pilih Pajak--</option>
                                <option value="Non PPN">Non PPN</option>
                                <option value="PPN">PPN</option>
                            </select>
                        </div>
                        <div class="input-daterange">
                            <form>
                                <div class="form-row">
                                    <div class="col">
                                        <input type="text" placeholder="Tanggal Mulai" class="form-control form-control-sm mb-2 mr-sm-2"
                                            id="tanggal_mulai" name="tanggal_mulai" autocomplete="off">
                                    </div>
                                    <div class="col">
                                        <input type="text" placeholder="Tanggal Akhir" class="form-control form-control-sm"
                                            id="tanggal_akhir" name="tanggal_akhir" autocomplete="off">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <button type="submit" id="filter" class="btn btn-primary mb-2">Tampilkan</button>&nbsp;
                        <button type="submit" id="refresh" class="btn btn-danger mb-2">Reset</button>&nbsp;
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        <!--
        <tfoot>
            <tr>
                <td colSpan="10">
                    {{-- $sales->appends(Request::all())->links() --}}
                </td>
            </tr>
        </tfoot>
        -->
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

        $('#refresh').click(function () {
            $('#tanggal_mulai').val('');
            $('#tanggal_akhir').val('');
            $('#customer_filter').val('');
            $('#bayar_filter').val('');
            $('#pajak_filter').val('');
        });

    });
</script>
@stop