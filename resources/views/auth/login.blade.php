@extends('layouts.auth')
​
@section('title')
    <title>Login</title>
@endsection
​
@section('content')
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Masukkan Username dan Password</p>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                @if (session('error'))
                    @alert(['type' => 'danger'])
                        {{ session('error') }}
                    @endalert
                @endif
                <div class="form-group has-feedback">
                    <input type="text"
                        name="username" 
                        class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" 
                        placeholder="{{ __('Username') }}"
                        value="{{ old('username') }}">
                </div>
                <div class="form-group has-feedback">
                    <input type="password" 
                        name="password"
                        class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }} " 
                        placeholder="{{ __('Password') }}">
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection