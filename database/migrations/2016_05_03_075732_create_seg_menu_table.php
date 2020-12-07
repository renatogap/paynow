<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acao_id')->unsigned()->nullable();
            $table->integer('pai')->unsigned()->nullable();
            $table->string('nome');
            $table->string('dica')->nullable();
            $table->boolean('status')->default(true);
            $table->smallInteger('ordem');
            $table->timestamps();

            $table->foreign('acao_id')->references('id')->on('seg_acao');
            $table->foreign('pai')->references('id')->on('seg_menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_menu');
    }
}
