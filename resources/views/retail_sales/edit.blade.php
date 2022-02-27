@extends('layouts.app')
@section('title') Update Penjualan Retail @endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Update Penjualan Retail') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('retail-sales.update', [$sale->id])}}" method="PUT" id="form-retail-sales">
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
                    </div>
                    <div class="column" style="background-color:#ffffff;">
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
                <form id="form-retail-sales-details">
                    <table class="table borderless table-sm" id="retail-sales-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15%"><b>Provider</b></th>
                                <th style="width: 30%"><b>Nama Barang</b></th>
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
                            <tr class='row-retails'>
                                <td style="width: 15%">{{$saleDetail->nama_provider}}</td>
                                <td style="width: 30%">{{ $saleDetail->nama }}</td>
                                <td style="width: 15%; display:none;"><input value="{{ $saleDetail->item_id }}" type="number"
                                    class="form-control" name="item_id[]" /></td>
                                <td style="width: 15%"><input value="{{ $saleDetail->kuantitas }}" type="number" onkeyup="calcTotal() "
                                        class="form-control form-control-sm w-50 kuantitas" name="kuantitas[]" /></td>
                                <td style="width: 15%"><input
                                        value="{{ floor($saleDetail->harga) }}" type="number"
                                        class="form-control form-control-sm harga" name="harga[]" readonly/></td>
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
                            <th><b>Stok Toko</b></th>
                            <th><b>Harga</b></th>
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
    $("tr.row-retails").each(function () {
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
                    var ppn = (reverseFormatNumber(total, 'id-ID')) * 0.1;
                    $("#ppn").text(ppn.toLocaleString("id-ID"));

                    var grandTotal = ppn + parseInt(reverseFormatNumber(total, 'id-ID'));
                    $("#grand-total").text(grandTotal.toLocaleString("id-ID"));
                } else {
                    $("#grand-total").text(total);
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
                ajax: "{{ route('retail.items-list') }}",
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
                        data: 'stok_toko',
                        name: 'stocks.stok_toko'
                    },
                    {
                        data: 'harga',
                        name: 'harga'
                    }
                ]
            });

            var counter = 0;

            $('#items-table tbody').on('click', 'tr', function () {
                var data = table.row(this).data();

                var newRow = $("<tr class='row-retails'>");
                var cols = "";
                cols += '<td style="display:none;"><input type="number" class="form-control form-control-sm stok-toko" name="stok_toko[]" value="' + data['stok_toko']  + '"></td>';
                cols += '<td style="display:none;"><input type="hidden" name="item_id[]" value="' + data['id']  + '"></td>';
                cols += '<td>' + data['nama_provider']  + '</td>';
                cols += '<td>' + data['nama']  + '</td>';
                cols += '<td><input type="number" class="form-control form-control-sm w-50 kuantitas" name="kuantitas[]"/></td>'
                cols += '<td><input type="number" class="form-control form-control-sm harga" name="harga[]" value=' + data['harga'].replace(/\./g, "")  + ' readonly/></td>';
                cols += '<td id="sub_total" style="text-align: right;font-weight: bold" class="multTotal"></td>';
                cols += '<td><input type="button" class="btnDel btn btn-sm btn-danger" value="Delete" style="float: right;"></td>';
                newRow.append(cols);
                $("#retail-sales-table").append(newRow);
                counter++;

                $('#itemsModal').modal('hide');
                cekDuplikatItem();
                hitungTotal();
                //cekStokToko();
                //compareStokKuantitas();
            });
            
            /*fungsi ini untuk membandingkan kuantitas dan stok toko yang tersedia,
            jika kuantitas melebihi stok toko, maka beri pesan
            
            function compareStokKuantitas() {
                $(".row-retails input").keyup(cekStok);

                function cekStok() {
                    $("tr.row-retails").each(function () {
                        var $kuantitas = parseFloat($('.kuantitas', this).val());
                        var $stokToko = parseFloat($('.stok-toko', this).val());

                        if ($kuantitas > $stokToko) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Kuantitas melebihi stok toko',
                            })
                        }
                    });
                }
            }

            //fungsi ini untuk mengecek jumlah stok toko, beri pesan jika stok toko bernilai 0
            function cekStokToko() {
                $("tr.row-retails").each(function () {
                    if ($('.stok-toko', this).val()== 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Stok toko tidak tersedia',
                        })
                    }
                });
            }
            */

            function cekDuplikatItem() {
                var namaItem = {};
                $('.row-retails').each(function () {
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
                $(".row-retails input").keyup(multInputs);

                function multInputs() {
                    calcTotal();
                }

                $("#retail-sales-table").on("click", ".btnDel", function (event) {
                    $(this).closest("tr").remove();
                    counter -= 1
                    multInputs();
                });
            }
            $("#retail-sales-table").on("click", ".btnDel", function (event) {
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
        var dataString = $("#form-retail-sales, #form-retail-sales-details").serialize();
        $.ajax({
            type: 'json',
            method: 'PUT',
            url: `{{ route('retail-sales.update', [$sale->id]) }}`,
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