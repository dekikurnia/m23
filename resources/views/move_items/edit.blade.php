@extends('layouts.app')
@section('title') Ubah Pindah Barang @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" style="display:none"></div>
            <div class="alert alert-success print-success-msg" style="display:none">
                <ul></ul>
            </div>
            <div class="card">
                <div class="card-header">{{ __('Ubah Pindah Barang') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('move-items.update', [$moveItem->id])}}" method="PUT" id="form-move-items">
                    @csrf
                    <div class="column">
                        <div class="form-group col-md-10">
                            <label for="nomor">Nomor</label>
                            <input value="{{ $moveItem->nomor }}" class="form-control" type="text" name="nomor" readonly />
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Tanggal</label>
                            <input value="{{ $moveItem->tanggal }}" type="text" class="form-control datepicker"
                                name="tanggal" autocomplete="off">
                            @error('tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="column">
                        <div class="form-group col-md-10">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" value="{{ $moveItem->keterangan }}" rows="3" name="keterangan"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemsModal">
                Tambah
            </button>
            <p>
                <form id="form-move-item-details">
                    <table class="table borderless table-sm" id="move-items-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 20%"><b>Provider</b></th>
                                <th style="width: 40%"><b>Nama Barang</b></th>
                                <th style="width: 15%"><b>Stok Gudang</b></th>
                                <th style="width: 10;"><b>Kuantitas Pindah</b></th>
                                <th style="width: 15"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($moveItemDetails as $moveItemDetail)
                            <tr style="background-color:#FFFFFF">
                                <td>{{$moveItemDetail->nama_provider}}</td>
                                <td>{{$moveItemDetail->nama}}</td>
                                <td style="display:none;"><input value="{{ $moveItemDetail->item_id }}" type="number"
                                    class="form-control" name="item_id[]" /></td>
                                <td>{{$moveItemDetail->stok_gudang}}</td>
                                <td><input value="{{ $moveItemDetail->kuantitas }}" type="number" 
                                    class="form-control form-control-sm w-50" name="kuantitas[]" /></td>
                                <td><input id="btn-delete" type="button" class="btnDel btn btn-sm btn-danger"
                                    value="Delete" style="float: right;"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <input class="btn btn-primary" id="save" type="submit" value="Simpan" />
                    <a class="btn btn-dark text-white" href="{{ route('move-items.index') }}" >Batal</a>
                </form>
        </div>
    </div>
</div>
<div class="modal fade" id="itemsModal" tabindex="-1" role="dialog" aria-labelledby="itemsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsModalLabel">Daftar Barang</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table-striped" id="items-table" style="width:100%">
                    <thead>
                        <tr>
                            <th style="display:none;"><b>Provider ID</b></th>
                            <th style="display:none;"><b>ID Barang</b></th>
                            <th><b>Provider</b></th>
                            <th><b>Nama Barang</b></th>
                            <th><b>Kategori</b></th>
                            <th><b>Stok Gudang</b></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function () {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        $(function () {
            var table = $('#items-table').DataTable({
                pageLength: 300,
                lengthMenu: [100, 200, 300, 400, 500],
                processing: true,
                serverSide: true,
                ordering : false,
                ajax: "{{ route('moves.items-list') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        visible : false
                    },
                    {
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
                    }
                ]
            });

            var counter = 0;

            $('#items-table tbody').on('click', 'tr', function () {
                var data = table.row(this).data();

                var newRow = $('<tr class="row-move">');
                var cols = "";
                cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id']  + '">' + data['id']  + '</td>';
                cols += '<td>' + data['nama_provider']  + '</td>';
                cols += '<td>' + data['nama']  + '</td>';
                cols += '<td class="stok-gudang">' + data['stok_gudang']  + '</td>';
                cols += '<td><input type="number" id="kuantitas" class="form-control form-control-sm w-50" name="kuantitas[]" value="" /></td>'
                cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
                newRow.append(cols);
                $("#move-items-table").append(newRow);
                counter++;

                cekDuplikatItem();
                $('#itemsModal').modal('hide');
                cekStokGudang();

            });

            function cekDuplikatItem() {
                var namaItem = {};
                $('.row-move').each(function () {
                    var txt = $(this).text();
                    if (namaItem[txt]) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: "Item sudah ada di keranjang"
                        })
                        $(this).remove();
                    } else {
                        namaItem[txt] = true;
                    }
                });
            }

            function cekStokGudang() {
                $("tr.row-move").each(function () {
                    if ($('.stok-gudang', this).val() == 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Stok gudang tidak tersedia',
                        })
                        $(this).closest("tr").remove();
                        counter -= 1
                    }     
                });
            }

            $("#move-items-table").on("click", ".btnDel", function (event) {
                $(this).closest("tr").remove();
                counter -= 1
            });

            $('.modal').on('shown.bs.modal', function () {
                table.columns.adjust()
            })
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#save').on('click', function (e) {
        e.preventDefault();
        var dataString = $("#form-move-items, #form-move-item-details").serialize();
        $.ajax({
            type: 'json',
            method: 'PUT',
            url: `{{ route('move-items.update', [$moveItem->id]) }}`,
            data: dataString,
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: "Sukses",
                    text: data.msg
                }).then(function () {
                    window.location.href = "/move-items";
                });
            },
            error: function (data) {
                $('.alert-danger').empty();
                $.each(data.responseJSON.msg, function (key, value) {
                    $('.alert-danger').show();
                    $('.alert-danger').append('<p>' + value + '</p>');
                    $(window).scrollTop(0);
                    
                    $('.alert-danger').delay(4000).slideUp(200, function () {
                        $(this).alert('');
                    });
                });
            }
        });
    });
</script>
@stop