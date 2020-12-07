<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegDependenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_dependencia', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acao_atual_id')->unsigned();
            $table->integer('acao_dependencia_id')->unsigned();
            $table->timestamps();

            $table->foreign('acao_atual_id')->references('id')->on('seg_acao');
            $table->foreign('acao_dependencia_id')->references('id')->on('seg_acao');

            $table->unique(['acao_atual_id', 'acao_dependencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_dependencia');
    }
}
