@extends('layouts.app')
@section('title') Tambah Pengguna @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Pengguna</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-header">{{ __('Tambah Pengguna') }}</div>
                <div class="card-body">
                    <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('users.store')}}"
                        method="POST">
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
                        <!--
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input class="form-control" placeholder="Email" type="text" name="email" />
                            @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        !-->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" placeholder="Nama Pengguna" type="password" name="password" />
                            @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Konfirmasi Password</label>
                            <input class="form-control" placeholder="Konfirmasi Password" type="password"
                                name="confirm-password" />
                            @if ($errors->has('confirm-password'))
                            <span class="text-danger">{{ $errors->first('confirm-password') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Roles</label>
                            <select class="roles js-states form-control" name="roles[]" multiple>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('roles'))
                            <span class="text-danger">{{ $errors->first('roles') }}</span>
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

@section('js')
<script type="text/javascript">
    $(".roles").select2({
        placeholder: " Pilih Roles",
        allowClear: true
    });
</script>
@stop