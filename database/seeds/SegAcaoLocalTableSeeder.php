<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegAcaoLocalTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            
        ];

        DB::table('seg_acao')->insert($items);

        DB::statement("select setval('seg_acao_id_seq', " . (count($items) + 1000) . ", true);");
    }
}
