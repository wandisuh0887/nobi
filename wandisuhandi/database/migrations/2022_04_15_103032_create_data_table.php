<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ticker');
            $table->integer('coin_id');
            $table->string('code');
            $table->string('exchange');
            $table->string('invalid');
            $table->string('record_time');
            $table->double('usd');
            $table->double('idr');
            $table->double('hnst');
            $table->double('eth');
            $table->double('btc');
            $table->timestamps();

            // id 50348
            // name Bitcoin
            // ticker BTC
            // coin_id 5
            // code bitcoin
            // exchange gecko 
            // invalid "0"
            // record_time 1502323200
            // usd "3367.905387088210"
            // idr "44990164.113417800000"
            // hnst "0.000000000000"
            // eth 11.348280083543
            // btc "1.000000000000"
            // created_at 2017-08-10 00:00:00
            // updated_at2017-08-10 00:00:00
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data');
    }
}
