<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelian_detail_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id');
            $table->foreignId('produk_id');
            $table->integer('diskon');
            $table->bigInteger('jumlah_barang');
            $table->bigInteger('total_harga');
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
        Schema::dropIfExists('pembelian_detail_produk');
    }
};
