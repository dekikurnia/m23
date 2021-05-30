@extends('layouts.app')
@section('title') Data Supplier @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Supplier</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Supplier</li>
                </ol>
            </nav>
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <form action="{{route('suppliers.index')}}">
                        <div class="input-group">
                            <input value="{{Request::get('keyword')}}" name="keyword" type="text" class="form-control"
                                placeholder="Filter berdasarkan nama supplier" name="nama">
                            <div class="input-group-append">
                                <input type="submit" value="Filter" class="btn btn-primary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-3">
            @if(session('status-create'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-create')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(session('status-edit'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-edit')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(session('status-delete'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-delete')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Data Supplier') }}</div>

                <div class="card-body">
                    <a href="{{route('suppliers.create')}}" class="btn btn-primary">Tambah Supplier</a>
                    <p>
                        <table class="table table-stripped table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10%"><b>Kode</b></th>
                                    <th style="width: 25%"><b>Nama</b></th>
                                    <th><b>Alamat</b></th>
                                    <th style="width: 20%"><b>Telepon</b></th>
                                    <th style="width: 5%"><b>Aksi</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>{{$supplier->kode}}</td>
                                    <td>{{$supplier->nama}}</td>
                                    <td>{{$supplier->alamat}}</td>
                                    <td>{{$supplier->telepon}}</td>
                                    <td>
                                        <a class="btn btn-info text-white btn-sm" href="{{route('suppliers.edit',
                                        [$supplier->id])}}">Ubah</a>
                                        <!-- <form onsubmit="return confirm('Anda yakin menghapus data ini ?')"
                                            class="d-inline" action="{{route('suppliers.destroy', [$supplier->id])}}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="submit" value="Hapus" class="btn btn-danger btn-sm">
                                        </form> -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colSpan="10">
                                        {{$suppliers->appends(Request::all())->links()}}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection