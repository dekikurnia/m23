@extends('layouts.app')
@section('title') Ubah Harga Jual @endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('price.index') }}">Harga Jual</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah Harga Jual</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-header">{{ __('Ubah Harga Jual') }}</div>
                <form enctype="multipart/form-data" class="bg-white shadow-sm p-3"
                    action="{{route('price.update', [$item->id])}}" method="POST">
                    @csrf
                    <input type="hidden" value="PUT" name="_method">
                    <div class="form-group">
                        <label for="nama">Nama Barang</label>
                        <input value="{{$item->nama}}" class="form-control"  placeholder="Nama Barang" type="text"
                            name="nama" id="nama" disabled/>
                        @if ($errors->has('nama'))
                        <span class="text-danger">{{ $errors->first('nama') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="harga">Harga Jual</label>
                        <input class="form-control mata-uang" onkeyup="hargaTerbilang();" placeholder="Harga Jual" type="text"
                            name="harga" id="harga" />
                            <label id="terbilang-output">
                                Terbilang
                            </label>
                        @if ($errors->has('harga'))
                        <span class="text-danger">{{ $errors->first('harga') }}</span>
                        @endif
                    </div>
                    <input class="btn btn-primary" type="submit" value="Simpan" />
                    <a href="{{route('price.index')}}" class="btn btn-dark">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function terbilang(bilangan) {

        bilangan = String(bilangan);
        var angka = new Array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
        var kata = new Array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
        var tingkat = new Array('', 'Ribu', 'Juta', 'Milyar', 'Triliun');

        var panjang_bilangan = bilangan.length;

        /* pengujian panjang bilangan */
        if (panjang_bilangan > 15) {
            kalimat = "Diluar Batas";
            return kalimat;
        }

        /* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
        for (i = 1; i <= panjang_bilangan; i++) {
            angka[i] = bilangan.substr(-(i), 1);
        }

        i = 1;
        j = 0;
        kalimat = "";


        /* mulai proses iterasi terhadap array angka */
        while (i <= panjang_bilangan) {

            subkalimat = "";
            kata1 = "";
            kata2 = "";
            kata3 = "";

            /* untuk Ratusan */
            if (angka[i + 2] != "0") {
                if (angka[i + 2] == "1") {
                    kata1 = "Seratus";
                } else {
                    kata1 = kata[angka[i + 2]] + " Ratus";
                }
            }

            /* untuk Puluhan atau Belasan */
            if (angka[i + 1] != "0") {
                if (angka[i + 1] == "1") {
                    if (angka[i] == "0") {
                        kata2 = "Sepuluh";
                    } else if (angka[i] == "1") {
                        kata2 = "Sebelas";
                    } else {
                        kata2 = kata[angka[i]] + " Belas";
                    }
                } else {
                    kata2 = kata[angka[i + 1]] + " Puluh";
                }
            }

            /* untuk Satuan */
            if (angka[i] != "0") {
                if (angka[i + 1] != "1") {
                    kata3 = kata[angka[i]];
                }
            }

            /* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
            if ((angka[i] != "0") || (angka[i + 1] != "0") || (angka[i + 2] != "0")) {
                subkalimat = kata1 + " " + kata2 + " " + kata3 + " " + tingkat[j] + " ";
            }

            /* gabungkan variabe sub kalimat (untuk Satu blok 3 angka) ke variabel kalimat */
            kalimat = subkalimat + kalimat;
            i = i + 3;
            j = j + 1;

        }

        /* mengganti Satu Ribu jadi Seribu jika diperlukan */
        if ((angka[5] == "0") && (angka[6] == "0")) {
            kalimat = kalimat.replace("Satu Ribu", "Seribu");
        }

        return kalimat + "";
    }

    function hargaTerbilang() {
        $('.mata-uang').mask('0.000.000.000', {
            reverse: true
        });

        var input = document.getElementById("harga").value.replace(/\./g, "");

        document.getElementById('terbilang-output').innerHTML = terbilang(input).replace(/  +/g, ' ');
    }
</script>
@stop