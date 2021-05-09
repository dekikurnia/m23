@extends('layouts.app')
@section('title') Entry Pembelian @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(session('status-create'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('status-create')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Entry Pembelian') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3" action="{{route('purchases.store')}}"
                    method="POST" id="form-purchase-detail">
                    @csrf
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="invoice">No. Invoice</label>
                            <input value="{{ $noInvoice }}" class="form-control" type="text" name="invoice" readonly />
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Tanggal</label>
                            <input type="text" class="form-control datepicker" name="tanggal" autocomplete="off">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Supplier</label>
                            <select style="min-width:300px" name="supplier_id" multiple id="suppliers"
                                class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="invoice">Cara Bayar</label>
                            <select class="form-control" name="cara_bayar">
                                <option>--Pilih--</option>
                                <option value="Kas">Kas</option>
                                <option value="Kredit">Kredit</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10 Kredit box">
                            <label for="jatuh_tempo">Jatuh Tempo</label>
                            <input type="text" class="form-control datepicker" name="jatuh_tempo">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="pajak">PPN / Non PPN</label>
                            <select class="form-control" id="select-ppn" name="pajak">
                                <option value="">Non PPN</option>
                                <option value="PPN">PPN</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Keterangan</label>
                            <textarea class="form-control" rows="3" name="keterangan"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemsModal">
                Tambah
            </button>
            <p>
                <form id="purchase-detail">
                    <table class="table borderless table-sm" id="purchases-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15%"><b>Provider</b></th>
                                <th style="width: 30%"><b>Nama Barang</b></th>
                                <th style="width: 15%"><b>Kuantitas</b></th>
                                <th style="width: 15"><b>Harga Beli</b></th>
                                <th style="text-align: right; width: 15%"><b>Sub Total</b></th>
                                <th style="width: 5%"><b></b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="PPN box-ppn">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">Total :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="total">0 </span>
                                </td>
                                <!--<td></td>-->

                            </tr>
                            <tr class="PPN box-ppn">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPN 10% :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="ppn">0 </span>
                                </td>
                                <!--<td></td>-->

                            </tr>
                            <tr>
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">Grand Total :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="grandTotal">0 </span>
                                    <input id="total_pembelian" name="total_pembelian" type="hidden">
                                </td>
                                <!--<td></td>-->

                            </tr>
                        </tfoot>
                    </table>
                    <input class="btn btn-primary" id="save" type="submit" value="Proses Transaksi Pembelian" />
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


        $('#suppliers').select2({
            ajax: {
                url: "{{ route('search-suppliers') }}",
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.nama
                            }
                        })
                    }
                }
            }
        });


        $("select").change(function () {
            $(this).find("option:selected").each(function () {
                var optionValue = $(this).attr("value");
                if (optionValue) {
                    $(".box").not("." + optionValue).hide();
                    $("." + optionValue).show();
                } else {
                    $(".box").hide();
                }
            });
        }).change();

        $("#select-ppn").change(function () {
            $(this).find("option:selected").each(function () {
                var optionValue = $(this).attr("value");
                if (optionValue) {
                    $(".box-ppn").show();
                } else {
                    $(".box-ppn").hide();
                }
            });
        }).change();

        $(function () {
            var table = $('#items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('list-items') }}",
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
                    }
                ]
            });

            var counter = 0;

            $('#items-table tbody').on('click', 'tr', function () {
                var data = table.row(this).data();

                var newRow = $("<tr class='txtMult'>");
                var cols = "";
                cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id']  + '">' + data['id']  + '</td>';
                cols += '<td>' + data['nama_provider']  + '</td>';
                cols += '<td>' + data['nama']  + '</td>';
                cols += '<td><input type="number" class="form-control val1" name="kuantitas[]"/></td>'
                cols += '<td><input type="number" class="form-control val2" name="harga[]"/></td>';
                cols += '<td id="sub_total" style="text-align: right;font-weight: bold" class="multTotal"></td>';
                cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
                newRow.append(cols);
                $("#purchases-table").append(newRow);
                counter++;

                $('#itemsModal').modal('hide');

                hitungTotal();

            });

            function hitungTotal() {
                $(".txtMult input").keyup(multInputs);

                function multInputs() {
                    var mult = 0;
                    // for each row:
                    $("tr.txtMult").each(function () {
                        // get the values from this row:
                        var $val1 = $('.val1', this).val();
                        var $val2 = $('.val2', this).val();
                        var $total = ($val1 * 1) * ($val2 * 1)

                        $('.multTotal', this).text($total.toLocaleString("id-ID"));
                        mult += $total;
                    });

                    $("#total").text(mult.toLocaleString("id-ID"));
                    $("#grandTotal").text(mult.toLocaleString("id-ID"));
                    document.getElementById('total_pembelian').value = document.getElementById('grandTotal').innerText;

                    $("#select-ppn").change(function () {
                        $(this).find("option:selected").each(function () {
                            var optionValue = $(this).attr("value");
                            if (optionValue) {
                                $(".box-ppn").show();
                                var ppn = mult * 0.1;
                                var grandTotal = mult + ppn;
                                $("#ppn").text(ppn.toLocaleString("id-ID"));
                                $("#grandTotal").text(grandTotal.toLocaleString("id-ID"));
                                document.getElementById('total_pembelian').value = document.getElementById('grandTotal').innerText;
                            } else {
                                $(".box-ppn").hide();
                                $("#grandTotal").text(mult.toLocaleString("id-ID"));
                                document.getElementById('total_pembelian').value = document.getElementById('grandTotal').innerText;
                            }
                        });
                    }).change();
                }

                $("#purchases-table").on("click", ".btnDel", function (event) {
                    $(this).closest("tr").remove();
                    counter -= 1
                    multInputs();
                });
            }
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#save').on('click', function (e) {
        e.preventDefault();
        var dataString = $("#form-purchase-detail, #purchase-detail").serialize();
        $.ajax({
            type: 'POST',
            url: `{{ route('purchases.store') }}`,
            data: dataString,
            success: function (data) {
                if (data.error) {
                    //toastr.error(data.error);
                } else {
                    //window.location.href = data.route
                }
            },
            error: function (err) {
                //toastr(data.error);
            }
        });
    });
</script>
@stop
