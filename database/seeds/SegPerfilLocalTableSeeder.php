<?php

use Illuminate\Database\Seeder;

class SegPerfilLocalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itens = [
            [
                'nome' => 'Diretor',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('seg_perfil')->insert($itens);
    }
}
