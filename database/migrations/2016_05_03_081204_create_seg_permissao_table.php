<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegPermissaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_permissao', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acao_id')->unsigned();
            $table->integer('perfil_id')->unsigned();
            $table->timestamps();

            $table->foreign('acao_id')->references('id')->on('seg_acao');
            $table->foreign('perfil_id')->references('id')->on('seg_perfil');

            $table->unique(['acao_id', 'perfil_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_permissao');
    }
}
