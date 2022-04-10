@extends('layouts.app')
@section('title') Update Penjualan Gudang @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Update Penjualan Gudang') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('warehouses.update', [$sale->id])}}" method="PUT" id="form-warehouses">
                    @csrf
                    <div class="column" style="background-color:#ffffff;">
                        <div class="form-group col-md-10">
                            <label for="invoice">No. Invoice</label>
                            <input value="{{ $sale->invoice }}" class="form-control" type="text" name="invoice" readonly />
                        </div>
                        <div class="form-group col-md-10">
                            <label for="tanggal">Tanggal</label>
                            <input value="{{ $sale->tanggal }}" type="text" class="form-control datepicker" name="tanggal" autocomplete="off">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="customer">Customer</label>
                            <select style="min-width:300px" name="customer_id" multiple id="customers"
                                class="form-control">
                                @foreach($customer as $row)
                                <option value="{{ $row->id }}"
                                    {{ $row->id == $sale->customer_id ? 'selected' : '' }}>
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
                                    {{ old('cara_bayar', $sale->cara_bayar) == "Kas" ? 'selected' : '' }}>
                                    Kas</option>
                                <option value="Kredit"
                                    {{ old('cara_bayar', $sale->cara_bayar) == "Kredit" ? 'selected' : '' }}>
                                    Kredit</option>
                                <option value="Transfer"
                                    {{ old('cara_bayar', $sale->cara_bayar) == "Transfer" ? 'selected' : '' }}>
                                    Transfer</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10 Kredit box">
                            <label for="jatuh_tempo">Jatuh Tempo</label>
                            <input value="{{ $sale->jatuh_tempo }}" type="text" class="form-control datepicker"
                                name="jatuh_tempo">
                        </div>
                        <div class="form-group col-md-10">
                            <label for="pajak">PPN / Non PPN</label>
                            <select class="form-control" name="pajak" id="select-ppn">
                                <option value="Non PPN"
                                    {{ old('pajak', $sale->pajak) == "Non PPN" ? 'selected' : '' }}>
                                    Non PPN</option>
                                <option value="PPN" {{ old('pajak', $sale->pajak) == "PPN" ? 'selected' : '' }}>
                                    PPN</option>
                            </select>
                        </div>
                        <div class="form-group col-md-10">
                            <label for="keterangan">Keterangan</label>
                            <textarea value="{{ $sale->keterangan }}" class="form-control" rows="3" name="keterangan"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <hr class="my-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#itemsModal">
                Tambah
            </button>
            <p>
                <form id="form-warehouses-details">
                    <table class="table borderless table-sm" id="warehouses-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15%"><b>Provider</b></th>
                                <th style="width: 30%"><b>Nama Barang</b></th>
                                <th style="width: 15%"><b>Stok Gudang</b></th>
                                <th style="width: 15%"><b>Kuantitas</b></th>
                                <th style="width: 15"><b>Harga Jual</b></th>
                                <th style="text-align: right; width: 15%"><b>Sub Total</b></th>
                                <th style="width: 5%"><b></b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <div style="display: none">
                                {{ $total = 0 }}
                            </div>
                            @foreach ($saleDetails as $saleDetail)
                            <tr class='row-warehouses'>
                                <td style="width: 15%">{{$saleDetail->nama_provider}}</td>
                                <td style="width: 30%">{{$saleDetail->nama }}</td>
                                <td style="width: 15%; display:none;"><input value="{{ $saleDetail->item_id }}" type="number"
                                    class="form-control" name="item_id[]" /></td>
                                <td style="width: 15%">{{ $saleDetail->stok_gudang }}</td>
                                <td style="width: 15%"><input value="{{ $saleDetail->kuantitas }}" type="number" onkeyup="calcTotal() "
                                        class="form-control form-control-sm w-50 kuantitas" name="kuantitas[]" /></td>
                                <td style="width: 15%"><input
                                        value="{{ floor($saleDetail->harga) }}" type="number" onkeyup="calcTotal() "
                                        class="form-control form-control-sm harga" name="harga[]"/></td>
                                <td style="text-align: right; width: 10%; font-weight: bold;" class="multTotal">
                                    {{ number_format($saleDetail->sub_total, 0, ',', '.') }}</td>
                                <div style="display: none">{{$total += $saleDetail->sub_total}}</div>
                                <td><input id="btn-delete" type="button" class="btnDel btn btn-sm btn-danger"
                                        value="Delete" style="float: right;"></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td style="width: 15%"></td>
                                <td style="width: 30%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">Total :</td>

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
                                <td style="width: 15%"></td>
                                <td style="text-align: right;font-weight: bold; width: 15%">PPN :</td>

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
                    <a class="btn btn-dark text-white" href="{{ route('sales.index') }}">Batal</a>
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

function calcTotal() {
        var mult = 0;
        $("tr.row-warehouses").each(function () {
            var $kuantitas = $('.kuantitas', this).val();
            var $harga = $('.harga', this).val();
            var $total = $kuantitas * (reverseFormatNumber($harga, 'id-ID'));

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
                    var ppn = mult * 0.11;
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
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        });

        $("#select-ppn").change(function () {
            $(this).find("option:selected").each(function () {
                var optionValue = $(this).attr("value") == "PPN";
                var total = $("#total").text();

                if (optionValue) {
                    var ppn = (reverseFormatNumber(total, 'id-ID')) * 0.11;
                    $("#ppn").text(ppn.toLocaleString("id-ID"));

                    var grandTotal = ppn + parseInt(reverseFormatNumber(total, 'id-ID'));
                    $("#grand-total").text(grandTotal.toLocaleString("id-ID"));
                } else {
                    $("#grand-total").text(total);
                }
            });
        }).change();

        $('#customers').select2({
            ajax: {
                url: "{{ route('customers.search') }}",
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
                pageLength: 300,
                lengthMenu: [100, 200, 300, 400, 500],
                processing: true,
                serverSide: true,
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
                    },
                ]
            });

            var counter = 0;

            $('#items-table tbody').on('click', 'tr', function () {
                var data = table.row(this).data();

                var newRow = $("<tr class='row-warehouses'>");
                var cols = "";
                cols += '<td style="display:none;"><input type="number" class="form-control form-control-sm stok-gudang" name="stok_gudang[]" value="' + data['stok_gudang']  + '"></td>';
                cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id']  + '"></td>';
                cols += '<td>' + data['nama_provider']  + '</td>';
                cols += '<td>' + data['nama']  + '</td>';
                cols += '<td>' + data['stok_gudang']  + '</td>';
                cols += '<td><input type="number" class="form-control form-control-sm w-50 kuantitas" name="kuantitas[]"/></td>'
                cols += '<td><input type="number" class="form-control form-control-sm harga" name="harga[]"/></td>';
                cols += '<td id="sub_total" style="text-align: right;font-weight: bold" class="multTotal"></td>';
                cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
                newRow.append(cols);
                $("#warehouses-table").append(newRow);
                counter++;

                cekDuplikatItem();
                $('#itemsModal').modal('hide');

                hitungTotal();
                cekStokGudang();
                compareStokKuantitas();
            });
            
            function cekDuplikatItem() {
                var namaItem = {};
                $('.row-warehouses').each(function () {
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

            /*fungsi ini untuk membandingkan kuantitas dan stok gudang yang tersedia,
            jika kuantitas melebihi stok gudang, maka beri pesan
            */
            function compareStokKuantitas() {
                $(".row-warehouses input").keyup(cekStok);

                function cekStok() {
                    $("tr.row-warehouses").each(function () {
                        var $kuantitas = parseFloat($('.kuantitas', this).val());
                        var $stokToko = parseFloat($('.stok-gudang', this).val());

                        if ($kuantitas > $stokToko) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Kuantitas melebihi stok gudang',
                            })
                            $(this).find(".kuantitas").val("");
                        }
                    });
                }
            }

            //fungsi ini untuk mengecek jumlah stok gudang, beri pesan jika stok gudang bernilai 0
            function cekStokGudang() {
                $("tr.row-warehouses").each(function () {
                    if ($('.stok-gudang', this).val()== 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Stok gudang tidak tersedia',
                        })
                    }
                });
            }

            function hitungTotal() {
                $(".row-warehouses input").keyup(multInputs);

                function multInputs() {
                    calcTotal();
                }

                $("#warehouses-table").on("click", ".btnDel", function (event) {
                    $(this).closest("tr").remove();
                    counter -= 1
                    multInputs();
                });
            }
            $("#warehouses-table").on("click", ".btnDel", function (event) {
                $(this).closest("tr").remove();
                calcTotal();
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
        var dataString = $("#form-warehouses, #form-warehouses-details").serialize();
        $.ajax({
            type: 'json',
            method: 'PUT',
            url: `{{ route('warehouses.update', [$sale->id]) }}`,
            data: dataString,
            success: function (data) {
                Swal.fire({
                    icon: 'success',
                    title: "Sukses",
                    text: data.msg
                }).then(function () {
                    window.location.href = "/sales";
                });
            },
            error: function (data) {
                var values = '';
                $.each(data.responseJSON.msg, function (key, value) {
                    values += '<br/>'+value
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