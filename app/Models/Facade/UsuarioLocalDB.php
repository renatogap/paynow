<?php

namespace App\Models\Facade;

use App\Models\Entity\SigServidor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Parque\Seguranca\App\Models\Entity\Usuario;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;

class UsuarioLocalDB extends Model
{
    public static function getUsuarioById($id)
    {
        return DB::table("usuario as u")
            ->where('u.id', $id)
            ->select(['u.*', 'u.unidade as unidade_solicitante'])
            ->first();
    }

    public static function isUsuarioDoSistema($usuarioID)
    {
        return UsuarioSistema::where('usuario_id', $usuarioID)
                        ->where('sistema_id', config('parque.codigo'))
                        ->count();
    }

    public static function getUsuarioByEmail($email)
    {
        return Usuario::where('email', $email)->where('excluido', false)->first();
    }

   

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
        $expiracao_login = config('parque.expiracao_login');

        $colunas = [
            'u.id',
            'u.nome',
            'u.email',
            DB::raw("CASE WHEN current_date - us.ultimo_acesso > $expiracao_login THEN 0 ELSE 1 END as ativo"),
        ];

        $sql = DB::table("usuario as u")->distinct()
            ->join("usuario_sistema as us", 'u.id', '=', 'us.usuario_id')
            ->join("seg_grupo as gr", 'gr.usuario_id', '=', 'us.usuario_id')
            ->where('us.sistema_id', $codigo_sistema)
            ->where('u.excluido', false)
            ->select($colunas);

        if(!auth()->user()->isAdmin()){
            $sql->where('perfil_id', '!=', 1);
        }

        if ($nome) {
            $sql->where('u.nome', 'like', "%$nome%");
        }

        if ($email) {
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

        if ($ocultarAdministrador) {
            $sql->where("id", '!=', 1);
        }

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
