<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->date('tanggal');
            $table->bigInteger('supplier_id')->unsigned();
            $table->enum('cara_bayar',['Kas', 'Kredit', 'Transfer']);
            $table->enum('pajak',['PPN', 'Non PPN']);
            $table->date('jatuh_tempo')->nullable();
            $table->decimal('total', 10.0);
            $table->string('keterangan')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
        Schema::dropIfExists('purchases');
    }
}
