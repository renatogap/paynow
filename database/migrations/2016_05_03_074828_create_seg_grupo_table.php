<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegGrupoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_grupo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('usuario_id')->unsigned();
            $table->integer('perfil_id')->unsigned();

            //$table->foreign('usuario_id')->references('id')->on('usuario');
            $table->foreign('perfil_id')->references('id')->on('seg_perfil');

            //$table->unique(['usuario_id', 'perfil_id']);

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
        Schema::drop('seg_grupo');
    }
}
