<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioLocalRequest;
use App\Models\Entity\CardapioTipo;
use App\Models\Entity\UsuarioTipoCardapio;
use App\Models\Facade\SigServidorDB;
use App\Models\Facade\UsuarioLocalDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\Regras\UsuarioLocalRegras;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Paginacao;
use Parque\Seguranca\App\Requests\UsuarioRequest;
use Parque\Seguranca\App\Models\Entity\Unidade;
use Parque\Seguranca\App\Models\Entity\Usuario;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use Parque\Seguranca\App\Models\Regras\UsuarioRegras;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;
class UsuarioLocalController extends Controller
{
    public function index()
    {
        $p = (object) request()->all();
        $nome = request('nome') ?? null;
        $email = request('email') ?? null;

        $sql = Usuario::where('excluido', 0);

        if(isset($p->nome) && !empty($p->nome)){
            $sql->where('nome', 'like', "%".$p->nome."%");
        }

        if(isset($p->email) && !empty($p->email)){
            $sql->where('email', $p->email);
        }

        $usuarios = $sql->get();

        return view('usuario.index', compact('usuarios', 'nome', 'email'));
    }

    public function info()
    {
        //return response()->json($servidor);
    }

    public function grid()
    {
        $nome = request('nome', null);
        $email = request('email', null);

        return Paginacao::dataTables(UsuarioLocalDB::grid($nome, $email), true);
    }

    public function criar()
    {
        $aPerfisCadastrados = UsuarioLocalDB::perfis();
        $aCardapio = CardapioTipo::orderBy('nome')->get();

        return view('usuario.criar', compact('aCardapio', 'aPerfisCadastrados'));
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $params = $request->all();
            $usuario = UsuarioRegras::criarUsuario($params);
            
            DB::commit();
            return response()->json(array(
                'message' => 'Usuário cadastrado com sucesso.',
                'url' => url('admin/usuario/editar/'.$usuario->id)
            ));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('message' => $e->getMessage()), 422);
        }
    }

    public function editar(Usuario $usuario)
    {
        $aCardapio = CardapioTipo::orderBy('nome')->get();
        $aCardapioCadastrados = UsuarioTipoCardapio::where('fk_usuario', $usuario->id)->get();

        #dd($aCardapioCadastrados);

        #$aUnidadesComDiretor = UsuarioLocalDB::unidadesAll();
        $aPerfisCadastrados = UsuarioLocalDB::perfis();
        $aPerfil = UsuarioLocalDB::perfilUsuario($usuario->id);

        return view('usuario.editar', compact('usuario', 'aPerfil', 'aPerfisCadastrados', 'aCardapioCadastrados', 'aCardapio'));
    }

    public function update()
    {
        DB::beginTransaction();
        try {
            $oUsuario = Usuario::find(request('id'));
            $oUsuario->nome = request('nome');
            $oUsuario->email = mb_convert_case(request('email'), MB_CASE_LOWER, 'UTF-8');
            $oUsuario->dt_cadastro = date('Y-m-d H:i:s');
            $oUsuario->nascimento = request('dt_nascimento', null);

            if ($senha = request('senha', null)) {
                $oUsuario->senha = $senha;
                $oUsuario->senha2 = null;

            }
            $oUsuario->cpf = preg_replace('/\D/u', null, request('cpf'));

            #if (request('trocar_senha', null)) {
            #    $oUsuario->primeiro_acesso = true;
            #}
            
            $oUsuario->save();

            $aPerfisEnviados = request('perfil', []);
            $aPerfisBanco = $oUsuario->listaPerfilSimplificado();

            if (is_array($aPerfisEnviados)) { //se o usuário enviou algum perfil

                $aPerfilNovo = array_diff($aPerfisEnviados, $aPerfisBanco); //perfis novos que não estavam no banco para este usuário

                foreach ($aPerfilNovo as $p) {
                    $oGrupo = new SegGrupo();
                    $oGrupo->usuario_id = $oUsuario->id;
                    $oGrupo->perfil_id = $p;
                    $oGrupo->save();
                }
                $aPerfisExcluidos = array_diff($aPerfisBanco, $aPerfisEnviados);

                SegGrupo::where('usuario_id', $oUsuario->id)
                    ->whereIn('perfil_id', $aPerfisExcluidos)
                    ->delete();
            }
            

            $aCardapiosEnviados = request('cardapio', []);
            $aCardapiosBanco = UsuarioTipoCardapio::where('fk_usuario', $oUsuario->id)->get()->pluck('fk_tipo_cardapio')->toArray();

            if (is_array($aCardapiosEnviados)) { //se o usuário enviou algum perfil

                $aCardapioNovo = array_diff(
                    $aCardapiosEnviados,
                    $aCardapiosBanco
                ); //perfis novos que não estavam no banco para este usuário

                #dd($aCardapioNovo, $aCardapiosEnviados, $aCardapiosBanco);

                foreach ($aCardapioNovo as $p) {
                    $oUserTipoCardapio = new UsuarioTipoCardapio();
                    $oUserTipoCardapio->fk_usuario = $oUsuario->id;
                    $oUserTipoCardapio->fk_tipo_cardapio = $p;
                    $oUserTipoCardapio->save();
                }

                $aCardapiosExcluidos = array_diff($aCardapiosBanco, $aCardapiosEnviados);


                UsuarioTipoCardapio::where('fk_usuario', $oUsuario->id)->whereIn('fk_tipo_cardapio', $aCardapiosExcluidos)->delete();
            }

            DB::commit();
            return response()->json(array('message' => 'Usuário atualizado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            if (config('app.debug')) {
                return response()->json(array('message' => $e->getMessage()), 422);
            } else {
                return response()->json(array('message' => 'Falha ao atualizar usuário'))->setStatusCode(422);
            }
        }
    }

    public function reativar()
    {
        $usuario_id = request('usuario');

        if (!$usuario_id) {
            return response()->json(['message' => 'usuário inválido'])->setStatusCode(422);
        }

        try {
            UsuarioLocalRegras::renovarLogin($usuario_id);
            return response()->json(['message' => 'Usuário reativado com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Falha ao reativar usuário'])->setStatusCode(422);;
        }
    }

    public function excluir()
    {
        $usuario_id = request('id');

        DB::beginTransaction();
        try {
            UsuarioRegras::excluir($usuario_id);
            DB::commit();
            return response()->json(array('message' => 'Usuario excluído com sucesso.'));
        } catch (\Exception $e) {

            DB::rollback();
            if (config('app.debug')) {
                return response()->json(['message' => $e->getMessage()], 422);
            } else {
                return response()->json(['message' => 'Falha ao excluir usuario.'])->setStatusCode(422);
            }
        }
    }

    public function dashboard()
    {
        return view('usuario.dashboard');
    }
}
