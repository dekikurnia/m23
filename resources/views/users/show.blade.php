@extends('layouts.app')
@section('title') Detail Pengguna @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Detail Pengguna') }}</div>
                <div class="card-body">
                    <table class="table borderless table-sm">
                        <tr>
                            <th style="width: 15%">Username</th>
                            <td style="width: 2%">:</td>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Nama Pengguna</th>
                            <td style="width: 2%">:</td>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th style="width: 15%">Roles</th>
                            <td style="width: 2%">:</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                @foreach($user->getRoleNames() as $v)
                                <label class="badge badge-success">{{ $v }}</label>
                                @endforeach
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection