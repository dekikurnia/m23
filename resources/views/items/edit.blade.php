@extends('layouts.app')
@section('title') Ubah Barang @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah Barang</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-header">{{ __('Ubah Barang') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('items.update', [$item->id])}}" method="POST">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div style="display: none" class="form-group">
                        <label for="provider_id">Provider</label>
                        <select id="type" name="provider_id" class="form-control provider">
                            <option value="" selected>Pilih Provider</option>
                            @foreach($provider as $row)
                            <option value="{{ $row->id }}" {{ $row->id == $item->provider_id ? 'selected' : '' }}>
                                {{ $row->nama }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('provider_id'))
                        <span class="text-danger">{{ $errors->first('provider_id') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Barang</label>
                        <input value="{{$item->nama}}" class="form-control" placeholder="Nama Barang" type="text"
                            name="nama" id="nama" />
                        @if ($errors->has('nama'))
                        <span class="text-danger">{{ $errors->first('nama') }}</span>
                        @endif
                    </div>
                    <div style="display: none" class="form-group">
                        <label for="category_id">Kategori</label>
                        <select id="type" name="category_id" class="form-control">
                            <option value="" selected>Pilih Kategori</option>
                            @foreach($category as $row)
                            <option value="{{ $row->id }}" {{ $row->id == $item->category_id ? 'selected' : '' }}>
                                {{ $row->nama }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('category_id'))
                        <span class="text-danger">{{ $errors->first('category_id') }}</span>
                        @endif
                    </div>
                    <div style="display: none" class="form-group">
                        <label for="stok_gudang">Stok Gudang</label>
                        <input value="{{$item->stock->stok_gudang}}" class="form-control" placeholder="Nama Barang" type="number"
                            name="stok_gudang" id="stok_gudang" />
                        @if ($errors->has('stok_gudang'))
                        <span class="text-danger">{{ $errors->first('stok_gudang') }}</span>
                        @endif
                    </div>
                    <div style="display: none" class="form-group">
                        <label for="stok_toko">Stok Toko</label>
                        <input value="{{$item->stock->stok_toko}}" class="form-control" placeholder="Nama Barang" type="number"
                            name="stok_toko" id="stok_toko" />
                        @if ($errors->has('stok_toko'))
                        <span class="text-danger">{{ $errors->first('stok_toko') }}</span>
                        @endif
                    </div>
                    <input class="btn btn-primary" type="submit" value="Simpan" />
                    <a href="{{route('items.index')}}" class="btn btn-dark">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
