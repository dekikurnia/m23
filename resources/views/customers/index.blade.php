@extends('layouts.app')
@section('title') Data Customer @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Customer</li>
                </ol>
            </nav>
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <form action="{{route('customers.index')}}">
                        <div class="input-group">
                            <input value="{{Request::get('keyword')}}" name="keyword" type="text" class="form-control"
                                placeholder="Filter berdasarkan nama customer" name="nama">
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
                <div class="card-header">{{ __('Data Customer') }}</div>

                <div class="card-body">
                    <a href="{{route('customers.create')}}" class="btn btn-primary">Tambah Customer</a>
                    <p>
                        <table class="table table-bordered table-stripped table-sm">
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
                                @foreach ($customers as $customer)
                                <tr>
                                    <td>{{$customer->kode}}</td>
                                    <td>{{$customer->nama}}</td>
                                    <td>{{$customer->alamat}}</td>
                                    <td>{{$customer->telepon}}</td>
                                    <td>
                                        <a class="btn btn-info text-white btn-sm" href="{{route('customers.edit',
                                        [$customer->id])}}">Ubah</a>
                                        <!-- <form onsubmit="return confirm('Anda yakin menghapus data ini ?')"
                                            class="d-inline" action="{{route('customers.destroy', [$customer->id])}}"
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
                                        {{$customers->appends(Request::all())->links()}}
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