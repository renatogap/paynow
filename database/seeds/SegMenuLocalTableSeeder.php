<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegMenuLocalTableSeeder extends Seeder
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

        DB::table('seg_menu')->insert($itens);

    }
}
