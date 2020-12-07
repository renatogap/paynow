<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_historico', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->unsigned();
            $table->integer('acao_id')->unsigned();
            $table->json('antes')->nullable();
            $table->json('depois')->nullable();
            $table->timestamp('dt_historico');
            $table->string('ip');
//            $table->timestamps();

            //$table->foreign('usuario_id')->references('id')->on('usuario');
            $table->foreign('acao_id')->references('id')->on('seg_acao');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_historico');
    }
}
