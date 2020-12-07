<?php

use Illuminate\Database\Seeder;

class SegGrupoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_grupo')->delete();
        DB::table('seg_grupo')->insert([
            'usuario_id' => 1,
            'perfil_id' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
