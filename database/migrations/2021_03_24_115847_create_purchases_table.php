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
            $table->enum('pajak',['Non PPN', 'PPN']);
            $table->enum('pajak2',['Non PPH', 'PPH']);
            $table->date('jatuh_tempo')->nullable();
            $table->date('tanggal_lunas')->nullable();
            $table->boolean('is_lunas')->default(false);
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
