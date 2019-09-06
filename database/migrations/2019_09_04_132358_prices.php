<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Prices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("parking_id")->nullable();
            //$table->foreign("parking_id")->references("parking_id")->on("users")->nullable();
            $table->string("amount")->nullable();
            $table->string("manager_id")->nullable();
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
