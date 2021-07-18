@extends('layouts.app')
@section('title') Kelola Role @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Role</li>
                </ol>
            </nav>
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
                <div class="card-header">{{ __('Kelola Role') }}</div>

                <div class="card-body">
                    @can('role-create')
                    <a href="{{route('roles.create')}}" class="btn btn-primary">Tambah Role</a>
                    @endcan
                    <p>
                        <table class="table table-stripped table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10%"><b>No.</b></th>
                                    <th><b>Nama</b></th>
                                    <th style="width: 16%;"><b>Aksi</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($roles as $role)
                                <tr>
                                    <td>{{$no++}}</td>
                                    <td>{{$role->name}}</td>
                                    <td>
                                        <a class="btn btn-dark btn-sm" href="{{ route('roles.show',$role->id) }}">Lihat</a>
                                        @can('role-edit')
                                        <a class="btn btn-primary btn-sm" href="{{ route('roles.edit', $role->id) }}">Ubah</a>
                                        @endcan
                                        @can('role-delete')
                                        <form onsubmit="return confirm('Anda yakin menghapus data ini ?')"
                                            class="d-inline" action="{{route('roles.destroy', [$role->id])}}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="submit" value="Hapus" class="btn btn-danger btn-sm">
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection