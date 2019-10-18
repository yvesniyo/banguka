<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("username")->nullable();
            $table->string("email")->nullable()->unique();
            $table->string("password")->nullable();
            $table->string("level")->nullable();
            $table->string("teacher")->nullable();
            $table->string("name")->nullable();
            $table->string("status")->nullable();
            $table->string("phone")->nullable();
            $table->string("whatsapp")->nullable();
            $table->string("intake_date")->nullable();
            $table->string("expire_date")->nullable();
            $table->string("student_code")->nullable();
            $table->string('api_token')->unique()
                        ->nullable()
                        ->default(null);
            $table->string('referrer')
                        ->nullable()
                        ->default(null);
            $table->string('package_id')
                        ->default(1);

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
