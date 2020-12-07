<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SegPerfilTableSeeder::class);
        $this->call(SegGrupoTableSeeder::class);
        $this->call(SegAcaoTableSeeder::class);
        $this->call(SegDependenciaTableSeeder::class);
        $this->call(SegMenuTableSeeder::class);

        //carga local
        /*$this->call(SegAcaoLocalTableSeeder::class);
        $this->call(SegPerfilLocalTableSeeder::class);
        $this->call(SegPermissaoLocalTableSeeder::class);
        $this->call(SegGrupoLocalTableSeeder::class);
        $this->call(SegDependenciaLocalTableSeeder::class);
        $this->call(SegMenuLocalTableSeeder::class);*/
    }
}
