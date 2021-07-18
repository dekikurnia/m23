@extends('layouts.app')
@section('title') Ubah Profil @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            @if(session('status-edit'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-edit')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Ubah Profil') }}</div>
                <div class="card-body">
                    <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                        action="{{route('change-profile.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input style="text-transform: lowercase" class="form-control" placeholder="Username"
                                type="text" name="username" />
                            @if ($errors->has('username'))
                            <span class="text-danger">{{ $errors->first('username') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="name">Nama Pengguna</label>
                            <input class="form-control" placeholder="Nama Pengguna" type="text" name="name" />
                            @if ($errors->has('name'))
                            <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <input class="btn btn-primary" type="submit" value="Ubah Profil" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection