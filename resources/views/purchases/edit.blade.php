@extends('layouts.app')
@section('title') UpdatePembelian @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" style="display:none"></div>
            <div class="alert alert-success print-success-msg" style="display:none">
                <ul></ul>
            </div>
            <div class="card">
                <div class="card-header">{{ __('Ubah Pembelian') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('purchases.update', [$purchase->id])}}" method="PUT" id="form-purchase-detail">
                    @csrf
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="invoice">No. Invoice</label>
                            <input value="{{ $purchase->invoice }}" class="form-control" type="text" name="invoice"
                                readonly />
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Tanggal</label>
                            <input value="{{ $purchase->tanggal }}" type="text" class="form-control datepicker"
                                name="tanggal" autocomplete="off">
                            @error('tanggal')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Supplier</label>
                            <select style="min-width:300px" name="supplier_id" multiple id="suppliers"
                                class="form-control">
                                @foreach($supplier as $row)
                                <option value="{{ $row->id }}"
                                    {{ $row->id == $purchase->supplier_id ? 'selected' : '' }}>
                                    {{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="cara_bayar">Cara Bayar</label>
                            <select class="form-control" name="cara_bayar" id="select-bayar">
                                <option>--Pilih--</option>
                                <option value="Kas"
                                    {{ old('cara_bayar', $purchase->cara_bayar) == "Kas" ? 'selected' : '' }}>
                                    Kas</option>
                                <option value="Kredit"
                                    {{ old('cara_bayar', $purchase->cara_bayar) == "Kredit" ? 'selected' : '' }}>
                                    Kredit</option>
                                <option value="Transfer"
                                    {{ old('cara_bayar', $purchase->cara_bayar) == "Transfer" ? 'selected' : '' }}>
                                    Transfer</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10 Kredit box">
                            <label for="jatuh_tempo">Jatuh Tempo</label>
                            <input value="{{ $purchase->jatuh_tempo }}" type="text" class="form-control datepicker"
                                name="jatuh_tempo">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="pajak">PPN / Non PPN</label>
                            <select class="form-control" name="pajak" id="select-ppn">
                                <option value="Non PPN"
                                    {{ old('pajak', $purchase->pajak) == "Non PPN" ? 'selected' : '' }}>
                                    Non PPN</option>
                                <option value="PPN" {{ old('pajak', $purchase->pajak) == "PPN" ? 'selected' : '' }}>
                                    PPN</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Keterangan</label>
                            <textarea value="{{ $purchase->keterangan }}" class="form-control" rows="3"
                                name="keterangan"></textarea>
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
                            <div style="display: none">
                                {{ $total = 0 }}
                            </div>
                            @foreach ($purchaseDetails as $purchaseDetail)
                            <tr class='txtMult'>
                                <td style="width: 15%">{{$purchaseDetail->nama_provider}}</td>
                                <td style="width: 30%">{{ $purchaseDetail->nama }}</td>
                                <td style="width: 15%; display:none;"><input value="{{ $purchaseDetail->item_id }}" type="number"
                                    class="form-control" name="item_id[]" /></td>
                                <td style="width: 15%"><input value="{{ $purchaseDetail->kuantitas }}" type="number" onkeyup="calcTotal() "
                                        class="form-control val1" name="kuantitas[]" /></td>
                                <td style="width: 15%"><input
                                        value="{{ floor($purchaseDetail->harga) }}" type="number" onkeyup="calcTotal() "
                                        class="form-control val2" name="harga[]" /></td>
                                <td style="text-align: right; width: 10%; font-weight: bold;" class="multTotal">
                                    {{ number_format($purchaseDetail->sub_total, 0, ',', '.') }}</td>
                                <div style="display: none">{{$total += $purchaseDetail->sub_total}}</div>
                                <td><input id="btn-delete" type="button" class="btnDel btn btn-sm btn-danger"
                                        value="Delete" style="float: right;"></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 10%">Total :</td>
                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="total">{{ number_format($total, 0, ',', '.') }}</span>
                                </td>
                                <!--<td></td>-->
                            </tr>
                            <tr class="PPN row-ppn">
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
                                    <span id="grand-total">0 </span>
                                </td>
                                <!--<td></td>-->

                            </tr>
                        </tfoot>
                    </table>
                    <input class="btn btn-primary" id="save" type="submit" value="Simpan" />
                    <a class="btn btn-success text-white" href="/purchases-data">Batal</a>
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
<script type = "text/javascript">

    function calcTotal() {
        var mult = 0;
        $("tr.txtMult").each(function () {
            var $val1 = $('.val1', this).val();
            var $val2 = $('.val2', this).val();
            var $total = $val1 * (reverseFormatNumber($val2, 'id-ID'));

            $('.multTotal', this).text($total.toLocaleString("id-ID"));
            mult += $total;
        });

        $("#total").text(mult.toLocaleString("id-ID"));
        $("#grand-total").text(mult.toLocaleString("id-ID"));

        $("#select-ppn").change(function () {
            $(this).find("option:selected").each(function () {
                var optionValue = $(this).attr("value") == "PPN";
                if (optionValue) {
                    $(".row-ppn").show();
                    var ppn = mult * 0.1;
                    var grandTotal = mult + ppn;
                    $("#ppn").text(ppn.toLocaleString("id-ID"));
                    $("#grand-total").text(grandTotal.toLocaleString("id-ID"));

                } else {
                    $(".row-ppn").hide();
                    $("#grand-total").text(mult.toLocaleString("id-ID"));

                }
            });
        }).change();
    }

    function reverseFormatNumber(val, locale) {
        var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
        var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
        var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
        reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
        return Number.isNaN(reversedVal) ? 0 : reversedVal;
    }

    $(document).ready(function () {
        $("#select-ppn").change(function () {
            $(this).find("option:selected").each(function () {
                var optionValue = $(this).attr("value") == "PPN";
                var total = $("#total").text();

                if (optionValue) {
                    var ppn = (reverseFormatNumber(total, 'id-ID')) * 0.1;
                    $("#ppn").text(ppn.toLocaleString("id-ID"));

                    var grandTotal = ppn + parseInt(reverseFormatNumber(total, 'id-ID'));
                    $("#grand-total").text(grandTotal.toLocaleString("id-ID"));
                } else {
                    $("#grand-total").text(total);
                }
            });
        }).change();

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        $('#suppliers').select2({
            ajax: {
                url: "{{ route('suppliers.search') }}",
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

        $("#select-bayar").change(function () {
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
                var optionValue = $(this).attr("value") == "PPN";
                if (optionValue) {
                    $(".row-ppn").show();
                } else {
                    $(".row-ppn").hide();
                }
            });
        }).change();

        $(function () {
            var table = $('#items-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('items.list') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
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
                cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id'] + '">' + data['id'] + '</td>';
                cols += '<td>' + data['nama_provider'] + '</td>';
                cols += '<td>' + data['nama'] + '</td>';
                cols += '<td><input type="number" class="form-control val1" name="kuantitas[]"/></td>'
                cols += '<td><input type="number" class="form-control val2" name="harga[]"/></td>';
                cols += '<td id="sub_total" style="text-align: right;font-weight: bold" class="multTotal"></td>';
                cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
                newRow.append(cols);
                var tablePurchaseDetails = document.getElementById('purchases-table');
                $("#purchases-table").append(newRow);
                counter++;

                $('#itemsModal').modal('hide');

                hitungTotal();

            });

            function hitungTotal() {
                $(".txtMult input").keyup(multInputs);

                function multInputs() {
                    calcTotal();
                }

                $("#purchases-table").on("click", ".btnDel", function (event) {
                    $(this).closest("tr").remove();
                    counter -= 1
                    multInputs();
                });
            }

            $("#purchases-table").on("click", "#btn-delete", function () {
                $(this).closest("tr").remove();
                calcTotal();
            });
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
            type: 'json',
            method: 'PUT',
            url: `{{ route('purchases.update', [$purchase->id]) }}`,
            data: dataString,
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: "Sukses",
                    text: data.msg
                }).then(function () {
                    window.location.href = "/purchases-data";
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