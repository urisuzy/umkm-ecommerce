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
        Schema::create('holding_umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id');
            $table->foreignId('holding_id');
            $table->timestamps();

            $table->unique(['umkm_id', 'holding_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holding_umkm');
    }
};
