<?php

namespace Parque\Seguranca\App\Models\Regras;

use App\Models\Entity\UsuarioTipoCardapio;
use App\Models\Facade\UsuarioLocalDB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\Entity\Acesso;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use Parque\Seguranca\App\Models\Entity\SegPreCadastro;
use Parque\Seguranca\App\Models\Entity\Usuario;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;
use Parque\Seguranca\App\Models\Facade\PerfilDB;
use Parque\Seguranca\App\Models\Formatar;
use Parque\Seguranca\App\Models\Util;

class UsuarioRegras
{
    /**
     * Cria um usuário no segurança.
     * Aqui não será feita nenhuma validação. Elas devem ser feitas no controlador
     *
     * @param array $aParametro
     * @return Usuario
     * @throws \Exception
     */
    public static function criarUsuario(array $aParametro)
    {
        //verificando se todos os parâmetros foram enviados
        $obrigatorio = [
            'nome',
            'email',
            'senha',
        ];

        if (!Util::todasAsChavesPresentes($obrigatorio, $aParametro)) {
            throw new \Exception('Campo obrigatório não informado');
        }

        if(!isset($aParametro['perfil']) || count($aParametro['perfil']) == 0){
            throw new \Exception('Informe qual o perfíl do usuário.');
        }

        // pega os dados do usuário pelo email

        $usuario = UsuarioLocalDB::getUsuarioByEmail($aParametro['email']);

        // verifica se o servidor já tem usuário no segurança
        if(!empty($usuario)){

            // verifica se o usuário já esta vinculado ao sistema em questão
            if (UsuarioLocalDB::isUsuarioDoSistema($usuario->id)) {

                throw new \Exception('Já existe um usuário neste sistema com este email.');
            } 


            UsuarioRegras::addUsuarioNoSistema($usuario->id);
                
        } else {

            $usuario = new Usuario();
            $usuario->nome = mb_convert_case($aParametro['nome'], MB_CASE_UPPER, 'UTF-8');
            $usuario->email = mb_convert_case($aParametro['email'], MB_CASE_LOWER, 'UTF-8');
            $usuario->senha = $aParametro['senha'];
            $usuario->dt_cadastro = date('Y-m-d H:i:s');
            //$usuario->primeiro_acesso = (isset($aParametro['renovar_senha']) && $aParametro['renovar_senha'] ? 1 : 0);
            //$usuario->cpf = preg_replace('/\D/', null, $aParametro['cpf']);
            $usuario->nascimento = $aParametro['dt_nascimento'];
            $usuario->excluido = false;
            //$usuario->unidade = $aParametro['unidade'];
            $usuario->save();
        
            self::addUsuarioNoSistema($usuario->id);

        }


        // adiciona o Perfil ao usuário
        UsuarioRegras::atualizarPerfil($usuario, $aParametro['perfil']);

        // adicionar o Cardápio ao usuário
        UsuarioRegras::atualizarCardapio($usuario, $aParametro['cardapio']);
        
        return $usuario;
    }

    public static function addUsuarioNoSistema($usuarioID)
    {
        $oUsuarioSistema = new UsuarioSistema();
        $oUsuarioSistema->usuario_id = $usuarioID;
        $oUsuarioSistema->ultimo_acesso = date('Y-m-d H:i:s');
        $oUsuarioSistema->sistema_id = config('parque.codigo');
        $oUsuarioSistema->status = 1;
        $oUsuarioSistema->save();
    }

    public static function atualizarUsuario(array $aParametro)
    {
        //verificando se todos os parâmetros foram enviados
        $obrigatorio = [
            'usuario_id',
        ];

        if (!Util::todasAsChavesPresentes($obrigatorio, $aParametro)) {
            throw new \Exception('Campo obrigatório não informado');
        }

        //atualizando dados do usuário
        $oUsuario = Usuario::find($aParametro['usuario_id']);

        if (isset($aParametro['nome'])) {
            $oUsuario->nome = mb_convert_case($aParametro['nome'], MB_CASE_UPPER, 'UTF-8');
        }

        if (isset($aParametro['email'])) {
            $oUsuario->email = mb_convert_case($aParametro['email'], MB_CASE_LOWER, 'UTF-8');
        }

        if (isset($aParametro['unidade'])) {
            $oUsuario->unidade = $aParametro['unidade'];
        }

        if (isset($aParametro['senha'])) {
            $oUsuario->senha = $aParametro['senha'];
        }

        $oUsuario->cpf = preg_replace('/\D/', null, $aParametro['cpf']);

        if (isset($aParametro['nascimento'])) {
            $oUsuario->nascimento = Formatar::data($aParametro['nascimento'], 'banco');
        }

        if (isset($aParametro['primeiro_acesso'])) {
            $oUsuario->primeiro_acesso = $aParametro['primeiro_acesso'];
        }
        if (isset($aParametro['excluido'])) {
            $oUsuario->excluido = $aParametro['excluido'];
        } 
        $oUsuario->save();

        return $oUsuario;
    }

    /**
     * Atualiza a lista de perfis de um determinado usuário
     * @param Usuario $oUsuario
     * @param array $aPerfisEnviados
     */
    public static function atualizarPerfil(Usuario $oUsuario, array $aPerfisEnviados)
    {

        $aPerfisBanco = $oUsuario->listaPerfilSimplificado();

        if (!empty($aPerfisEnviados)) { //se o usuário enviou algum perfil
            $aPerfilNovo = array_diff($aPerfisEnviados,
                $aPerfisBanco); //perfis novos que não estavam no banco para este usuário
            foreach ($aPerfilNovo as $p) {
                $oGrupo = new SegGrupo();
                $oGrupo->usuario_id = $oUsuario->id;
                $oGrupo->perfil_id = $p;
                $oGrupo->save();
            }

            $aPerfisExcluidos = array_diff($aPerfisBanco, $aPerfisEnviados);

            SegGrupo::where('usuario_id', $oUsuario->id)->whereIn('perfil_id', $aPerfisExcluidos)->delete();

        } else { //o usuário removeu todos os perfis da tela ou não atribuiu nenhum

            if (!empty($aPerfisBanco)) { //o usuário perdeu todos os perfis que tinha

                SegGrupo::where('usuario_id', $oUsuario->id)->delete();

            }
        }
    }

    public static function atualizarCardapio(Usuario $oUsuario, array $aCardapiosEnviados)
    {

        $aCardapiosBanco = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();

        if (!empty($aCardapiosEnviados)) { //se o usuário enviou algum perfil

            $aCardapioNovo = array_diff($aCardapiosEnviados, $aCardapiosBanco); //perfis novos que não estavam no banco para este usuário

            foreach ($aCardapioNovo as $p) {
                $oUserTipoCardapio = new UsuarioTipoCardapio();
                $oUserTipoCardapio->fk_usuario = $oUsuario->id;
                $oUserTipoCardapio->fk_tipo_cardapio = $p;
                $oUserTipoCardapio->save();
            }

            $aCardapiosExcluidos = array_diff($aCardapiosBanco, $aCardapiosEnviados);

            UsuarioTipoCardapio::where('fk_usuario', $oUsuario->id)->whereIn('fk_tipo_cardapio', $aCardapiosExcluidos)->delete();

        } else { //o usuário removeu todos os perfis da tela ou não atribuiu nenhum

            if (!empty($aCardapiosBanco)) { //o usuário perdeu todos os perfis que tinha

                UsuarioTipoCardapio::where('fk_usuario', $oUsuario->id)->delete();

            }
        }
    }

    /**
     * Atualiza a lista de sistemas que um usuário tem acesso
     * @param Usuario $oUsuario
     * @param array $aSistemasEnviados
     */
    public static function atualizarSistemas(Usuario $oUsuario, array $aSistemasEnviados = [])
    {
        if (!empty($aSistemasEnviados)) { //se o usuário enviou algum sistema
            $aSistemaBanco = $oUsuario->listaSistemaSimplificado();
            $aSistemaNovo = array_diff($aSistemasEnviados, $aSistemaBanco);

            foreach ($aSistemaNovo as $p) {
                $oSistema = new UsuarioSistema();
                $oSistema->usuario_id = $oUsuario->id;
                $oSistema->sistema_id = $p;
                $oSistema->status = 1;
                $oSistema->save();
            }

            $aSistemasExcluidos = array_diff($aSistemaBanco, $aSistemasEnviados);

            UsuarioSistema::where('usuario_id', $oUsuario->id)->whereIn('sistema_id', $aSistemasExcluidos)->delete();
        }
    }

    /**
     * A exclusão local remove apenas o acesso do usuário ao sistema atual.
     * A exclusão definitiva só pode ser feita por usuário com perfil 1 (root),
     * pois este usuário pode ter acesso a outros sistemas
     * @param $usuario_id
     */
    public static function excluir($usuario_id)
    {
        $usuario = Usuario::find($usuario_id);
        $usuario->excluido = 1;
        $usuario->save();

        $sistema_id = config('parque.codigo');
        $oUsuairoSistema = UsuarioSistema::where('usuario_id', $usuario_id)
            ->where('sistema_id', $sistema_id)
            ->first();

        SegGrupo::where('usuario_id', $usuario_id)->get()->each(function ($e) {
            $e->delete();
        });

        UsuarioSistema::destroy($oUsuairoSistema->id);
    }

    /**
     * Verifica se o login do usuário expirou
     *
     * Este método depende do Middleware autorização ativo pois,
     * a consulta de $oUsuarioSistema pode retornar vazio,
     * o que é tratato no middleware. Tenha isto em mente caso vá
     * realizar testes com este método
     * @param integer $usuarioID
     * @return boolean
     */
    public static function isLoginExpirado(int $usuarioID): bool
    {
        if ($usuarioID === 1) { //usuário root (id = 1) não expira
            return false;
        }

        //todos os perfis do usuário
        $aPerfil = PerfilDB::perfilSimplificado($usuarioID);

        //usuário administrador (perfil = 1) não expira
        if (in_array(1, $aPerfil)) {
            return false;
        }

        $sistema_id = config('parque.codigo');
        $oUsuarioSistema = UsuarioSistema::where('usuario_id', $usuarioID)
            ->where('sistema_id', $sistema_id)
            ->first();

//        dd($oUsuarioSistema);

        if(!$oUsuarioSistema)
            throw new \Exception('Este usuário não possui perfil neste sistema');

        //calculando o tempo desde o último acesso
        $oUltimoAcesso = new Carbon($oUsuarioSistema->ultimo_acesso);
        $oAgora = Carbon::now();

        //se o login ainda está no prazo desde o último acesso
        if ($oAgora->diffInDays($oUltimoAcesso) <= config('parque.expiracao_login')) {
            return false;
        }

        return true;

    }

    /**
     * Registra o acesso de um usuário na tabela acesso
     *
     * @param int $usuarioID
     * @param string $ip
     * @return int
     */
    public static function registrarAcesso($usuarioID, $ip): int
    {
        $oAcesso = new Acesso();
        $oAcesso->fk_usuario = $usuarioID;
        $oAcesso->ip = $ip;
        $oAcesso->login = date('Y-m-d H:i:s');
        $oAcesso->desabilitarLog();
        $oAcesso->save();

        return $oAcesso->id;
    }

    /**
     * Renova o login de um determinado usuário para este sistema
     *
     * @return void
     */
    public static function renovarLogin($usuarioID)
    {
        //root não precisa de renovação
        if ($usuarioID === 1) {
            return;
        }

        $sistema_id = config('parque.codigo');
        $oUsuarioSistema = UsuarioSistema::where('usuario_id', $usuarioID)
            ->where('sistema_id', $sistema_id)
            ->first();

        $oUsuarioSistema->desabilitarLog();
        $oUsuarioSistema->ultimo_acesso = date('Y-m-d');
        $oUsuarioSistema->save();
    }

    /**
     * Faz a criação definitiva do usário que estava apenas com pré-cadastro
     */
    /*public static function criarUsuarioViaPreCadastro(array $p)
    {
        //cadastrando usário e atribuindo perfis
        $oUsuario = self::criarUsuario($p);
        self::atualizarPerfil($oUsuario, $p['perfil']);

        //removendo usuário da tabela de pré-cadastro
        $oPreCadastro = SegPreCadastro::where('email', $oUsuario->email)
            ->orWhere('cpf', $oUsuario->cpf)
            ->delete();

        return;
    }*/
}
