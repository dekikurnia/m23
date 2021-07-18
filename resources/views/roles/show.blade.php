@extends('layouts.app')
@section('title') Detail Role @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Detail Role') }}</div>
                <div class="card-body">
                    <table class="table borderless table-sm">
                        <tr>
                            <th style="width: 15%">Nama Role</th>
                            <th style="width: 2%">:</th>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Permissions</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if(!empty($rolePermissions))
                                @foreach($rolePermissions as $v)
                                <li>{{ $v->name }}</li>
                                @endforeach
                                @endif
                            </td>
                        </tr>
                    </table>
                    <a href="{{route('roles.index')}}" class="btn btn-dark">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection