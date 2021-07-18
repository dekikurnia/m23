@extends('layouts.app')
@section('title') Tambah Barang @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Barang</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-header">{{ __('Tambah Barang Baru') }}</div>
                <div class="card-body">
                    <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                        action="{{route('items.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="provider_id">Provider</label>
                            <select id="type" name="provider_id" class="form-control">
                                <option value="" selected>Pilih Provider</option>
                                @foreach($provider as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('provider_id'))
                            <span class="text-danger">{{ $errors->first('provider_id') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Barang</label>
                            <input class="form-control" placeholder="Nama Barang" type="text" name="nama" id="nama" />
                            @if ($errors->has('nama'))
                            <span class="text-danger">{{ $errors->first('nama') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select name="category_id" class="form-control">
                                <option value="" selected>Pilih Kategori</option>
                                @foreach($category as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('category_id'))
                            <span class="text-danger">{{ $errors->first('category_id') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="stok_gudang">Stok Gudang</label>
                            <input class="form-control" placeholder="Stok Gudang" type="number" name="stok_gudang" id="stok_gudang" />
                            @if ($errors->has('stok_gudang'))
                            <span class="text-danger">{{ $errors->first('stok_gudang') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="stok_toko">Stok Toko</label>
                            <input class="form-control" placeholder="Stok Toko" type="number" name="stok_toko" id="stok_toko" />
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
</div>
@endsection