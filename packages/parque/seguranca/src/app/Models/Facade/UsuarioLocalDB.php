<?php

namespace Parque\Seguranca\App\Models\Facade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsuarioLocalDB extends Model
{
    /**
     * Retorna nome e email de todos os usuários do sistema atual
     * para ser usado com a classe de paginação (sql não executado)
     * @param null $nome
     * @param null $email
     * @return mixed
     */
    public static function grid($nome = null, $email = null)
    {
        $codigo_sistema = config('parque.codigo');

        $sql = DB::table("usuario as u")
                ->join("usuario_sistema as us", 'u.id', '=', 'us.usuario_id')
                ->where('us.sistema_id', $codigo_sistema)
                ->where('u.excluido', false)
                ->select('u.id', 'u.nome', 'u.email');

        if($nome) {
            $sql->where('u.nome', 'ilike', "%$nome%");
        }

        if($email) {
            $sql->where('u.email', $email);
        }

        return $sql;
    }

    /**
     * Retorna todos os perfis cadastrados exceto o 1 - Administrador do sistema
     * @param bool $ocultarAdministrador
     * @return
     */
    public static function perfis($ocultarAdministrador = true)
    {
        $sql = DB::table("seg_perfil as p");

        if($ocultarAdministrador)
            $sql->where("id", '!=', 1);

        return $sql->get();
    }


    public static function perfilUsuario($id_usuario)
    {
        return DB::table("seg_grupo as g")
            ->where('usuario_id', $id_usuario)
            ->join("seg_perfil as p", 'p.id', '=', 'g.perfil_id')
            ->select('p.id', 'p.nome')
            ->get();
    }
}
