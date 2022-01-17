@extends('layouts.app')
@section('title') Data Barang @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Barang</li>
                </ol>
            </nav>
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
            <div class="card">
                <div class="card-header">{{ __('Data Barang') }}</div>

                <div class="card-body">
                    <a href="{{route('items.create')}}" class="btn btn-primary">Tambah Barang</a>
                    <p>
                        <table class="table table-sm" id="items_table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 20%"><b>Provider</b></th>
                                    <th style="width: 25%"><b>Nama Barang</b></th>
                                    <th style="width: 15%">
                                        <select name="category_filter" id="category_filter" class="form-control">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($category as $row)
                                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th style="width: 15%;"><b>Stok Gudang</b></th>
                                    <th style="width: 15%;"><b>Stok Toko</b></th>
                                    <th style="width: 5%"><b>Aksi</b></th>
                                </tr>
                            </thead>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {

    fetch_data();

    function fetch_data(category = '') {
        $('#items_table').DataTable({
            pageLength: 200,
            lengthMenu: [100, 200, 300, 400, 500],
            processing: true,
            serverSide: true,
            ordering: false,
            ajax: {
                url: "{{ route('items.index') }}",
                dataType: "json",
                data: {
                    category: category
                }
            },
            columns: [{
                    data: 'nama_provider',
                    name: 'providers.nama'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'nama_kategori',
                    name: 'categories.nama'
                },
                {
                    data: 'stok_gudang',
                    name: 'stocks.stok_gudang'
                },
                {
                    data: 'stok_toko',
                    name: 'stocks.stok_toko'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    $('#category_filter').change(function () {
        var category_id = $('#category_filter').val();

        $('#items_table').DataTable().destroy();

        fetch_data(category_id);
    });

}); 
</script>
@stop