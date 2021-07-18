@extends('layouts.app')
@section('title') Kelola Pengguna @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Pengguna</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Pengguna</li>
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
                <div class="card-header">{{ __('Kelola Pengguna') }}</div>

                <div class="card-body">
                    <a href="{{route('users.create')}}" class="btn btn-primary">Tambah Pengguna</a>
                    <p>
                        <table class="table table-stripped table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 10%"><b>No.</b></th>
                                    <th><b>Username</b></th>
                                    <th><b>Nama</b></th>
                                    <th><b>Roles</b></th>
                                    <th style="width: 16%;"><b>Aksi</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{$no++}}</td>
                                    <td>{{$user->username}}</td>
                                    <td>{{$user->name}}</td>
                                    <td>
                                        @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $role)
                                        <label class="badge badge-success">{{ $role }}</label>
                                        @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a class="btn btn-dark btn-sm"
                                            href="{{ route('users.show',$user->id) }}">Lihat</a>
                                        <a class="btn btn-primary btn-sm"
                                            href="{{ route('users.edit', $user->id) }}">Ubah</a>
                                        <form onsubmit="return confirm('Anda yakin menghapus data ini ?')"
                                            class="d-inline" action="{{route('users.destroy', [$user->id])}}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="submit" value="Hapus" class="btn btn-danger btn-sm">
                                        </form>
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