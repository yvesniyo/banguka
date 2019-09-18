<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable()->default(null);
            $table->text('choices')->nullable()->default(null);
            $table->string('answer')->nullable()->default(null);
            $table->string('added_by')->nullable()->default(null);
            $table->string('rates')->nullable()->default(null);
            $table->string("who_corrected")->nullable()->default(null);
            $table->string("who_incorrected")->nullable()->default(null);
            $table->string("questionImage")->nullable()->default(null);
            $table->string("image_downloaded")->nullable()->default(null);
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
        Schema::dropIfExists('questions');
    }
}
