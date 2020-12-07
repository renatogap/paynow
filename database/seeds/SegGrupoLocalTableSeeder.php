<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegGrupoLocalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itens = [
            //Perfil Philipe e Renato
            ['usuario_id' => 1, 'perfil_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ];

        DB::table('seg_grupo')->insert($itens);
    }
}
