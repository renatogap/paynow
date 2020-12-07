<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegDependenciaLocalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            //usuario
            [
                'acao_atual_id' => 28,
                'acao_dependencia_id' => 29,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 30,
                'acao_dependencia_id' => 31,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 32,
                'acao_dependencia_id' => 33,
                'created_at' => date('Y-m-d H:i:s'),
            ],

            //perfil
            [
                'acao_atual_id' => 35,
                'acao_dependencia_id' => 36,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 37,
                'acao_dependencia_id' => 38,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 39,
                'acao_dependencia_id' => 40,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 39,
                'acao_dependencia_id' => 35,
                'created_at' => date('Y-m-d H:i:s'),
            ],


            //Admin das Denúncias
            [
                'acao_atual_id' => 1002,
                'acao_dependencia_id' => 1003,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'acao_atual_id' => 1002,
                'acao_dependencia_id' => 1004,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('seg_dependencia')->insert($items);
    }
}
