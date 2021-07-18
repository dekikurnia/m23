@extends('layouts.app')
@section('title') Ubah Kata Sandi @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Ubah Kata Sandi') }}</div>
                <div class="card-body">
                    <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                        action="{{route('change-password.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="current_password">Password Lama</label>
                            <input type="password" class="form-control" name="current_password">
                            @if ($errors->has('current_password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('current_password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" class="form-control" name="new_password">
                            @if ($errors->has('new_password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('new_password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="new_confirm_password">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="new_confirm_password">
                            @if ($errors->has('new_confirm_password'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('new_confirm_password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <input class="btn btn-primary" type="submit" value="Ubah Kata Sandi" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection