@extends('layouts.app')
@section('title') Detail Pindah Barang @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Detail Pindah Barang') }}</div>
                <div class="card-body">
                    <table class="table borderless table-sm">
                        <tr>
                            <th style="width: 15%">Nomor Invoice</th>
                            <td style="width: 2%">:</td>
                            <td>{{$moveItem->nomor}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Tanggal</th>
                            <td style="width: 2%">:</td>
                            <td>{{ \Carbon\Carbon::parse($moveItem->tanggal)->translatedFormat('d/m/Y')}}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Keterangan</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if (is_null($moveItem->keterangan))
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
            <table class="table table-stripped table-sm" id="purchases-table">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 15%"><b>Provider</b></th>
                        <th style="width: 30%"><b>Nama Barang</b></th>
                        <th style="width: 10%"><b>Kuantitas</b></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($moveItemDetails as $moveItemDetail)
                    <tr style="background-color:#FFFFFF">
                        <td>{{$moveItemDetail->nama_provider}}</td>
                        <td>{{$moveItemDetail->nama}}</td>
                        <td>{{$moveItemDetail->kuantitas}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="/move-items" class="btn btn-dark">Kembali</a>
            <a class="btn btn-info text-white" href="{{route('move-items.edit',
            [$moveItem->id])}}">Ubah</a>
        </div>
    </div>
</div>
@endsection
