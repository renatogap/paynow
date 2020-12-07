<?php

namespace App\Models\Regras;

use App\Models\Entity\Setor;
use App\Models\Entity\SetorUsuario;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;
use Parque\Seguranca\App\Models\Regras\UsuarioRegras;

class UsuarioLocalRegras
{
    public static function salvar(\stdClass $p)
    {
        $oUsuario = UsuarioRegras::criarUsuario([
            'usuario_id' => $p->id,
            'nome' => $p->nome,
            'email' => $p->email,
            'senha' => $p->senha,
            'cpf' => $p->cpf,
            'nascimento' => $p->dt_nascimento,
            'primeiro_acesso' => $p->renovar_senha == 'true' ? true : false,
            'unidade' => $p->unidade
        ]);

        UsuarioRegras::atualizarPerfil($oUsuario, $p->perfil ?? []);
    }

    public static function atualizarUsuario(\stdClass $p)
    {
        $oUsuario = UsuarioRegras::atualizarUsuario([
            'usuario_id' => $p->id,
            'nome' => $p->nome,
            'email' => $p->email,
            'senha' => $p->senha,
            'cpf' => $p->cpf,
            'nascimento' => $p->dt_nascimento,
            'primeiro_acesso' => $p->renovar_senha == 'true' ? true : false,
            'unidade' => $p->unidade,
        ]);

        UsuarioRegras::atualizarPerfil($oUsuario, $p->perfil ?? []);
        PessoaRegras::criar($p);
    }

    public static function excluir($usuario_id)
    {
        $sistema_id = config('parque.sistema');
        $oUsuarioSistema = UsuarioSistema::where('usuario_id', $usuario_id)
            ->where('sistema_id', $sistema_id)
            ->first();

        SegGrupo::where('usuario_id', $usuario_id)->get()->each(function ($e) {
            $e->delete();
        });

        UsuarioSistema::destroy($oUsuarioSistema->id);
    }

    public static function renovarLogin($usuario_id)
    {
        //root não precisa de renovação
        if ($usuario_id === 1) {
            return;
        }

        $sistema_id = config('parque.codigo');
        $oUsuarioSistema = UsuarioSistema::where('usuario_id', $usuario_id)
            ->where('sistema_id', $sistema_id)
            ->where('status', '')
            ->first();

        $oUsuarioSistema->desabilitarLog();
        $oUsuarioSistema->ultimo_acesso = date('Y-m-d');
        $oUsuarioSistema->save();
    }
}
