<?php

use Illuminate\Database\Seeder;

class SegPerfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seg_perfil')->delete();
        $itens = [
            [
                'nome' => 'root',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('seg_perfil')->insert($itens);
    }
}
