<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TrafficAccidents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traffic_accidents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->integer('screen_id')->unsigned();

            $table
                -> foreign( 'screen_id' )
                -> references( 'id' )
                -> on( 'screens' )
                -> onDelete( 'CASCADE' )
            ;

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
        //
    }
}
