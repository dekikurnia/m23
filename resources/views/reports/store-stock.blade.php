@extends('layouts.app')
@section('title') Laporan Stok Toko @endsection
@section('content')
<div style="margin-top:10px;" class="container-fluid">
   <form>
        <div class="row justify-content-center">
            <div class="card">
                <div class="card-header">{{ __('Filter Laporan Stok Toko') }}</div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="form-group">
                            <label>Barang</label>
                            <select style="width: 100%;" name="items_filter[]" multiple id="items_filter" class="form-control form-control-sm">
                            </select>
                        </div>
                         <div class="form-group">
                            <label>Kategori Barang</label>
                            <select class="form-control form-control-sm" name="category_filter" id="category_filter">
                                <option value="">--Pilih Kategori--</option>
                                @foreach($categories as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
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
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h5 align="center">
                <b>LAPORAN STOK TOKO</b>
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
                        <th style="text-align: center">Retail</th>
                        <th style="text-align: center">Grosir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $stock)
                    <tr>
                        <td>{{$stock->nama_provider}}</td>
                        <td style="width: 20%">{{$stock->nama_item}}</td>
                        <td style="text-align: right;">{{$stock->stok_awal}}</td>
                        <td style="text-align: right;">{{ $stock->kuantitas_pindah}}</td>
                        <td style="text-align: right;">{{ $stock->kuantitas_retail}}</td>
                        <td style="text-align: right;">{{ $stock->kuantitas_grosir}}</td>
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

        $('#items_filter').select2({
            ajax: {
                url: "{{ route('items.search') }}",
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.nama_item
                            }
                        })
                    }
                }
            }
        });

        $('#refresh').click(function () {
            $('#tanggal_mulai').val('');
            $('#tanggal_akhir').val('');
            $('#item_filter').val('');
        });

    });
</script>
@stop