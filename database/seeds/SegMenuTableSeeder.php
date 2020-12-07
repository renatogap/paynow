<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_menu')->delete();
        $items = [
            [
                'nome' => 'Segurança',
                'acao_id' => 1,
                'pai' => null,
                'status' => true,
                'ordem' => 10,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'Ações',
                'acao_id' => 7,
                'pai' => 1,
                'status' => true,
                'ordem' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'Menus',
                'acao_id' => 14,
                'pai' => 1,
                'status' => true,
                'ordem' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome' => 'Usuários',
                'acao_id' => 21,
                'pai' => 1,
                'status' => true,
                'ordem' => 3,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('seg_menu')->insert($items);
    }
}
