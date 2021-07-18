@extends('layouts.app')
@section('title') Ubah Role @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Daftar Role</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah Role</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-header">{{ __('Ubah Role') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('roles.update', [$role->id])}}"
                    method="POST">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div class="form-group">
                        <label for="name">Nama Role</label>
                        <input value="{{ $role->name }}" class="form-control" placeholder="Nama Role" type="text" name="name" />
                        @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="nama">Permissions</label>
                        <br/>
                        @foreach($permission as $value)
                        <input type="checkbox" value="{{ $value->id }}" name="permission[]" {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}/> <label>{{$value->name}}</label>
                        <br />
                        @endforeach
                        @if ($errors->has('permission'))
                        <span class="text-danger">{{ $errors->first('permission') }}</span>
                        @endif
                    </div>
                    <input class="btn btn-primary" type="submit" value="Simpan" />
                    <a href="{{route('roles.index')}}" class="btn btn-dark">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection