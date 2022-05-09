@extends('layouts.app')
@section('title') Update Pembelian @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Ubah Pembelian') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('purchases.update', [$purchase->id])}}" method="PUT" id="form-purchases">
                    @csrf
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="invoice">No. Invoice</label>
                            <input value="{{ $purchase->invoice }}" class="form-control" type="text" name="invoice"
                                readonly />
                        </div>
                        
                        <div class="form-group col-md-10">
                            <label for="supplier">Supplier</label>
                            <select style="min-width:300px" name="supplier_id" multiple id="suppliers"
                                class="form-control">
                                @foreach($supplier as $row)
                                <option value="{{ $row->id }}"
                                    {{ $row->id == $purchase->supplier_id ? 'selected' : '' }}>
                                    {{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-10">
                            <label for="keterangan">Keterangan</label>
                            <textarea value="{{ $purchase->keterangan }}" class="form-control" rows="3"
                                name="keterangan"></textarea>
                        </div>
                    </div>
                    <div class="column" style="background-color:#ffffff;">
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
                            <label for="pajak">PPN / Non PPN</label>
                            <select class="form-control" name="pajak" id="select-ppn">
                                <option value="Non PPN"
                                    {{ old('pajak', $purchase->pajak) == "Non PPN" ? 'selected' : '' }}>
                                    Non PPN</option>
                                <option value="PPN" {{ old('pajak', $purchase->pajak) == "PPN" ? 'selected' : '' }}>
                                    PPN</option>
                            </select>
                        </div>
                        @if($purchase->pajak2 == 'PPH')
                        <div class="form-group col-md-10 pph">
                            <label for="pajak2">PPH / Non PPH</label>
                            <select class="form-control" name="pajak2" id="select-pph">
                                <option value="Non PPH"
                                    {{ old('pajak2', $purchase->pajak2) == "Non PPH" ? 'selected' : '' }}>
                                    Non PPH</option>
                                <option value="PPH" {{ old('pajak2', $purchase->pajak2) == "PPH" ? 'selected' : '' }}>
                                    PPH</option>
                            </select>
                        </div>
                        @endif
                        @if($purchase->pajak2 == 'Non PPH')
                        <div style="display: none" class="form-group col-md-10 pph">
                            <label for="pajak2">PPH / Non PPH</label>
                            <select class="form-control" name="pajak2" id="select-pph">
                                <option value="Non PPH"
                                    {{ old('pajak2', $purchase->pajak2) == "Non PPH" ? 'selected' : '' }}>
                                    Non PPH</option>
                                <option value="PPH" {{ old('pajak2', $purchase->pajak2) == "PPH" ? 'selected' : '' }}>
                                    PPH</option>
                            </select>
                        </div>
                        @endif
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
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemsModal">
                Tambah
            </button>
            <p>
                <form id="form-purchase-details">
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
                            <tr class='row-purchases'>
                                <td style="width: 15%">{{$purchaseDetail->nama_provider}}</td>
                                <td style="width: 30%">{{ $purchaseDetail->nama }}</td>
                                <td style="width: 15%; display:none;"><input value="{{ $purchaseDetail->item_id }}" type="number"
                                    class="form-control" name="item_id[]" /></td>
                                <td style="width: 15%"><input value="{{ $purchaseDetail->kuantitas }}" type="number" onkeyup="calcTotal() "
                                        class="form-control form-control-sm kuantitas" name="kuantitas[]" /></td>
                                <td style="width: 15%"><input
                                        value="{{ floor($purchaseDetail->harga) }}" type="number" onkeyup="calcTotal() "
                                        class="form-control form-control-sm harga" name="harga[]" /></td>
                                <td style="text-align: right; width: 10%; font-weight: bold;" class="multTotal">
                                    {{ number_format($purchaseDetail->sub_total, 0, ',', '.') }}</td>
                                @if($purchase->pajak == 'PPN')
                                <div style="display: none">{{$total += ($purchaseDetail->sub_total * 100) / 111}}</div>
                                @else
                                <div style="display: none">{{$total += $purchaseDetail->sub_total}}</div>
                                @endif
                                <td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete"
                                        style="float: right;"></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                @if($purchase->pajak == 'PPN')
                                <td class="total" style="text-align: right;font-weight: bold; width: 10%">DPP :</td>
                                @else
                                <td class="total" style="text-align: right;font-weight: bold; width: 10%">Total :</td>
                                @endif
                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="total">{{ number_format($total, 0, ',', '.') }}</span>
                                </td>
                                <!--<td></td>-->
                            </tr>
                            @if($purchase->pajak == 'PPN')
                            <tr class="PPN row-ppn">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPN :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="ppn">{{ number_format(($total*0.11), 0, ',', '.') }}</span>
                                </td>
                                
                                <!--<td></td>-->
                            </tr>
                            @endif

                            @if($purchase->pajak == 'Non PPN')
                            <tr style="display: none" class="PPN row-ppn">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPN :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="ppn">0</span>
                                </td>
                                
                                <!--<td></td>-->
                            </tr>
                            @endif
                            @if($purchase->pajak2 == 'PPH')
                            <tr class="PPH row-pph">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPH :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="pph">{{ number_format(($total*0.005), 0, ',', '.') }}</span>
                                </td>
                                <!--<td></td>-->

                            </tr>
                            @endif
                            @if($purchase->pajak2 == 'Non PPH')
                            <tr style="display: none" class="PPH row-pph">
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPH :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    <span id="pph">0</span>
                                </td>
                                <!--<td></td>-->

                            </tr>
                            @endif
                            <tr>
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">Grand Total :</td>

                                <td style="text-align: right;font-weight: bold; width: 5%">
                                    @if($purchase->pajak == 'PPN' && $purchase->pajak2 == 'PPH')
                                    <span id="grandTotal">{{ number_format($total + ($total*0.11) + ($total*0.005), 0, ',', '.') }} </span>
                                    @elseif ($purchase->pajak == 'PPN')
                                    <span id="grandTotal">{{ number_format($total + ($total*0.11), 0, ',', '.') }} </span>
                                    @else
                                    <span id="grandTotal">{{ number_format($total, 0, ',', '.') }} </span>
                                    @endif
                                </td>
                                <!--<td></td>-->

                            </tr>
                        </tfoot>
                    </table>
                    <input class="btn btn-primary" id="save" type="submit" value="Simpan" />
                    <a class="btn btn-dark text-white" href="/purchases/data">Batal</a>
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
    // for each row:
    $("tr.row-purchases").each(function () {
        var $kuantitas = $('.kuantitas', this).val();
        var $harga = $('.harga', this).val();
        var $total = $kuantitas * $harga
        var $dpp = ($total * 100) / 111

        $('.multTotal', this).text($total.toLocaleString("id-ID"));
        if ($(".total").text() == "DPP :") {
            mult += $dpp;
        } else {
            mult += $total;
        }
    });

    $("#total").text(Math.round(mult).toLocaleString("id-ID"));
    var ppn =  mult * 0.11;
    var pph =  mult * 0.005;
    var grandTotal = mult + parseFloat(ppn) + parseFloat(pph)
    var grandTotalPPN = mult + parseFloat(ppn) 
    if ($('#select-ppn option:selected').val() == "PPN" && $('#select-pph option:selected').val() == "PPH") {
        $("#ppn").text(Math.round(ppn).toLocaleString("id-ID"));
        $("#pph").text(Math.round(pph).toLocaleString("id-ID"));
        $("#grandTotal").text(Math.round(grandTotal).toLocaleString("id-ID"));
    } else if ($('#select-ppn option:selected').val() == "PPN") {
        $("#ppn").text(Math.round(ppn).toLocaleString("id-ID"));
        $("#grandTotal").text(Math.round(grandTotalPPN).toLocaleString("id-ID"));
    } else {
        $("#grandTotal").text(Math.round(mult).toLocaleString("id-ID"));
    }
}

/*
function reverseFormatNumber(val, locale) {
    var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
    var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
    var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
    reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
    return Number.isNaN(reversedVal) ? 0 : reversedVal;
}
*/
var counter = 0;

function deleteRow() {
    $("#purchases-table").on("click", ".btnDel", function (event) {
        $(this).closest("tr").remove();
        counter -= 1

        calcTotal();

        var total = $("#total").text();
        var ppn = total * 0.11;
        var grandTotal = parseFloat(total.replace(/\./g, '')) + parseFloat(ppn)
        var optionValue = $('#select-ppn').find(":selected").text();
        if (optionValue == "PPN") {
            $("#ppn").text(ppn.toLocaleString("id-ID"));
            $("#grandTotal").text(grandTotal.toLocaleString("id-ID"));
        }
    });
}

$(document).ready(function () {
    
    deleteRow();

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

    $(function () {
        var table = $('#items-table').DataTable({
            pageLength: 300,
            lengthMenu: [100, 200, 300, 400, 500],
            processing: true,
            serverSide: true,
            ajax: "{{ route('purchases.items-list') }}",
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

        $('#items-table tbody').on('click', 'tr', function () {
            var data = table.row(this).data();

            var newRow = $("<tr class='row-purchases'>");
            var cols = "";
            cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id'] + '">' + data['id'] + '</td>';
            cols += '<td>' + data['nama_provider'] + '</td>';
            cols += '<td>' + data['nama'] + '</td>';
            cols += '<td><input type="number" class="form-control form-control-sm kuantitas" name="kuantitas[]"/></td>'
            cols += '<td><input type="number" class="form-control form-control-sm harga" name="harga[]"/></td>';
            cols += '<td id="sub_total" style="text-align: right;font-weight: bold" class="multTotal"></td>';
            cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
            newRow.append(cols);
            $("#purchases-table").append(newRow);
            counter++;

            cekDuplikatItem();
            $('#itemsModal').modal('hide');

            hitungTotal();

        });

        function cekDuplikatItem() {
            var namaItem = {};
            $('.row-purchases').each(function () {
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

        function hitungTotal() {
            $(".row-purchases input").keyup(multInputs);

            function multInputs() {
                calcTotal();
            }

            deleteRow();
        }

        $("#select-ppn").on('change', function () {
            var total = $("#total").text();
            var dpp = (parseFloat(total.replace(/\./g, '')) * 100) / 111
            var ppn = dpp * 0.11;
            var grandTotal = parseFloat(dpp) + parseFloat(ppn);
            if ($(this).val() == 'PPN') {
                $(".row-ppn").show();
                $('.total').html('DPP :');
                $(".ppn").show();
                $(".pph").show();
                $("#ppn").text(ppn.toLocaleString("id-ID"));
                $("#total").text(Math.round(dpp).toLocaleString("id-ID"));
                $("#grandTotal").text(Math.round(grandTotal).toLocaleString("id-ID"));
            } else {
                $(".pph").hide();
                $("#select-pph").val("Non PPH");
                $(".row-ppn").hide();
                $(".row-pph").hide();
                $('.total').html('Total :');
                $(".ppn").hide();
                $("#total").text(Math.round(parseFloat((total.replace(/\./g, '') * 111) / 100)).toLocaleString("id-ID"));
                $("#grandTotal").text(Math.round(parseFloat((total.replace(/\./g, '') * 111) / 100)).toLocaleString("id-ID"));
            }
        });

        $("#select-pph").on('change', function () {
            var total = $("#total").text();
            var ppn = (total.replace(/\./g, '')) * 0.11;
            var pph = (total.replace(/\./g, '')) * 0.005;
            var grandTotal = parseFloat(total.replace(/\./g, '')) + parseFloat(ppn) + parseFloat(pph);
            if ($(this).val() == 'PPH') {
                $(".row-pph").show();
                $("#pph").text(Math.round(pph).toLocaleString("id-ID"));
                $("#ppn").text(Math.round(ppn).toLocaleString("id-ID"));
                $("#grandTotal").text(Math.round(grandTotal).toLocaleString("id-ID"));
            } else {
                $(".row-pph").hide();
                $("#grandTotal").text(Math.round(parseFloat(total.replace(/\./g, '')) + parseFloat((total.replace(/\./g, '')) * 0.11)).toLocaleString("id-ID"));
            }
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
    var dataString = $("#form-purchases, #form-purchase-details").serialize();
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
                window.location.href = "/purchases/data";
            });
        },

        error: function (data) {
            var values = '';
            $.each(data.responseJSON.msg, function (key, value) {
                values += '<br/>' + value
            });

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: values
            })
        }
    });
});
</script>
@stop