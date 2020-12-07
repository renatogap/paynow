<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSegAcaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seg_acao', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nome');
            $table->string('method')->default('get');
            $table->string('descricao')->nullable();
            $table->boolean('destaque')->default(false);
            $table->string('nome_amigavel')->nullable();
            $table->boolean('obrigatorio')->default(false);
            $table->string('grupo')->nullable();
            $table->boolean('log_acesso')->default(false);
            $table->timestamps();

            $table->index(['nome', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seg_acao');
    }
}
