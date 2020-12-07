<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegPermissaoLocalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $itens = [
            
        ];

        DB::table('seg_permissao')->insert($itens);
    }
}
