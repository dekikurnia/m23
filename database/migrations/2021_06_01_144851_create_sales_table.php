<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->date('tanggal');
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->enum('cara_bayar',['Kas', 'Kredit', 'Transfer'])->nullable();
            $table->enum('jenis',['Retail', 'Grosir', 'Gudang']);
            $table->enum('pajak',['Non PPN', 'PPN']);
            $table->date('jatuh_tempo')->nullable();
            $table->date('tanggal_lunas')->nullable();
            $table->boolean('is_lunas')->default(false);
            $table->string('keterangan')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
