<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Transactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("amount")->nullable();
            $table->string("check_in")->nullable();
            $table->string("check_out")->nullable();
            $table->string("nfc_id")->nullable();
            $table->string("parking_id")->nullable();
            //$table->foreign("parking_id")->references("parking_id")->on("users")->nullable();
            $table->string("parking_agent_id")->nullable();
            //$table->foreign("parking_agent_id")->references("users_id")->on("users")->nullable();
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
