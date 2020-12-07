<?php

namespace Parque\Seguranca\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\Acesso;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use Parque\Seguranca\App\Models\Entity\SegPerfil;
use Parque\Seguranca\App\Models\Entity\Sistema;
use Parque\Seguranca\App\Models\Entity\Usuario;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;
use Parque\Seguranca\App\Models\Entity\Unidade;
use Parque\Seguranca\App\Models\Facade\UsuarioDB;
use Parque\Seguranca\App\Models\Formatar;
use Parque\Seguranca\App\Models\Regras\UsuarioRegras;
use Parque\Seguranca\App\Requests\AtualizarDadosRequest;
use Parque\Seguranca\App\Requests\LoginRequest;
use Parque\Seguranca\App\Requests\UsuarioRequest;


class UsuarioController extends Controller
{
    public function definirHome()
    {
        if (config('parque.dashboard') === false) {
            return redirect('seguranca/usuario/home');
        } else {
            return redirect(config('parque.dashboard'));
        }
    }

    public function index()
    {
        if (Auth::check()) { //usuário já autenticado
            return $this->definirHome();
        } else {
            return view('Seguranca::usuario.index');
        }
    }

    public function atualizarDados(AtualizarDadosRequest $request)
    {
        $oUsuario = Auth::user(); //usuario logado
        $oUsuario->cpf = preg_replace('/\D/', null, request('cpf'));
        $oUsuario->nascimento = request('nascimento');
        $oUsuario->save();

        return $this->definirHome();
    }

    public function login(LoginRequest $request)
    {
        if (Auth::check()) { //usuário já autenticado
            return $this->definirHome();
        }

        DB::beginTransaction();
        try {

            //registrando acesso
            $acesso_id = UsuarioRegras::registrarAcesso($request->usuario->id, $request->ip());
            $request->session()->put('acesso_id', $acesso_id); //registra na sessão o id do acesso
            //renovando login do usuario
            UsuarioRegras::renovarLogin($request->usuario->id);

            Auth::login($request->usuario);
            DB::commit();
            return redirect('admin/home');
        } catch (\Exception $e) {
            DB::rollback();

            if (config('app.debug')) {
                printvardie($e->getMessage());
            } else {
                return redirect('seguranca/usuario');
            }
        }
    }

    public function logout(Request $request)
    {
        $acesso_id = $request->session()->get('acesso_id');
        
        if ($acesso_id) { //se estiver nulo a sessao está expirada
            $oAcesso = Acesso::find($acesso_id);
            $oAcesso->logout = date('Y-m-d H:i:s');
            $oAcesso->save();
            Auth::logout();
        }

        $request->session()->flush();
        return redirect('seguranca/usuario');
    }

    public function home()
    {
        if (config('parque.dashboard') === false) {
            return view('Seguranca::usuario.home');
        } else {
            return redirect(config('parque.dashboard'));
        }
    }

    public function admin()
    {
        $oSistema = Sistema::select(['id', 'nome'])->orderBy('nome')->get();
        return view('Seguranca::usuario.admin', compact('oSistema'));
    }

    public function grid()
    {
        $nome = request('nome', null);
        $email = request('email', null);
        $sistema = request('sistema', null);
        $status = request('status', null);

        return response()->json(UsuarioDB::grid($nome, $email, $sistema, $status));
    }

    public function novo()
    {
        $oUsuario = Auth::user(); //usuario logado
        $aPerfil = $oUsuario->perfis;
        $aPerfisCadastrados = SegPerfil::all(['id', 'nome']);
        $aUnidade = Unidade::all(['id', 'nome']);
        $aSistema = Sistema::all(['id', 'nome']);
        $aUnidade = Unidade::all(['id', 'nome']);

        return view('Seguranca::usuario.novo', compact('aPerfil', 'aPerfisCadastrados', 'aSistema', 'aUnidade'));
    }

    public static function getDiretorDaUnidade($id_unidade)
    {
        return Usuario::where('fk_unidade', $id_unidade)
            ->where('diretor', true)
            ->select('nome')
            ->first();
    }


    public function store(UsuarioRequest $r)
    {
        DB::beginTransaction();
        try {
            $unidade = request('unidade');

            if (request('diretor') === 'true') {
                $usuario = self::getDiretorDaUnidade($unidade);
                if ($usuario) {
                    return response()->json(array('msg' => 'O servidor(a) ' . $usuario->nome . ' já é o diretor desta unidade'), 422);
                }
            }

            $oUsuario = new Usuario();
            $oUsuario->nome = request('nome', null);
            $oUsuario->unidade = $unidade;
            $oUsuario->email = mb_convert_case(request('email', null), MB_CASE_LOWER, 'UTF-8');
            $oUsuario->dt_cadastro = date('Y-m-d H:i:s');
            // $oUsuario->status = 1;
            $oUsuario->excluido = false;
            $oUsuario->diretor = request('diretor', null);
            $oUsuario->fk_unidade = $unidade;

            if (request('trocar_senha', null) === 'true') {
                $oUsuario->primeiro_acesso = true;
            } else {
                $oUsuario->primeiro_acesso = false;
            }

            if (request('senha', null)) {
                $oUsuario->senha = request('senha');
            }

            $oUsuario->cpf = preg_replace('/\D/u', null, request('cpf'));
            $oUsuario->nascimento = request('nascimento', null);
            $oUsuario->save();

            $aPerfisEnviados = request('perfil', []);
            foreach ($aPerfisEnviados as $p) {
                $oGrupo = new SegGrupo();
                $oGrupo->usuario_id = $oUsuario->id;
                $oGrupo->perfil_id = $p;
                $oGrupo->save();
            }

            $aSistemasEnviados = request('sistema', []);
            foreach ($aSistemasEnviados as $p) {
                $oSistema = new UsuarioSistema();
                $oSistema->usuario_id = $oUsuario->id;
                $oSistema->sistema_id = $p;
                $oSistema->save();
            }

            DB::commit();
            return response()->json(array('msg' => 'Usuário criado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('msg' => $e->getMessage()), 422);
        }
    }

    public function editar($id)
    {
        $oUsuario = Usuario::where('id', '=', $id)->first();
        $aPerfil = $oUsuario->perfis;
        $aPerfisCadastrados = SegPerfil::select(['id', 'nome'])->orderBy('nome')->get();
        $aPerfilUsuario = $oUsuario->perfis;
        $aSistema = Sistema::select(['id', 'nome'])->orderBy('nome')->get();
        $aSistemasUsuario = $oUsuario->sistemas();
        $aUsuario = Usuario::select(['diretor'])->get();

        return view(
            'Seguranca::usuario.editar',
            compact('oUsuario', 'aPerfil', 'aPerfisCadastrados', 'aSistema', 'aSistemasUsuario', 'aPerfilUsuario', 'aUsuario')
        );
    }

    public function update(UsuarioRequest $r)
    {
        DB::beginTransaction();
        try {
            $oUsuario = Usuario::find(request('id'));
            $oUsuario->nome = request('nome');
            $oUsuario->email = mb_convert_case(request('email'), MB_CASE_LOWER, 'UTF-8');
            $oUsuario->diretor = request('diretor');
            $oUsuario->fk_unidade = request('unidade');

            if ($senha = request('senha', null)) {
                $oUsuario->senha = $senha;
            }

            $oUsuario->cpf = preg_replace('/\D/u', null, request('cpf'));
            $oUsuario->nascimento = request('nascimento', null);

            if (request('trocar_senha', null)) {
                $oUsuario->primeiro_acesso = true;
            }
            $oUsuario->save();

            $aPerfisEnviados = request('perfil', []);
            $aPerfisBanco = $oUsuario->listaPerfilSimplificado();

            if (is_array($aPerfisEnviados)) { //se o usuário enviou algum perfil
                $aPerfilNovo = array_diff(
                    $aPerfisEnviados,
                    $aPerfisBanco
                ); //perfis novos que não estavam no banco para este usuário

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

            $aSistemasEnviados = request('sistema', []);
            $aSistemaBanco = $oUsuario->listaSistemaSimplificado();

            if (is_array($aSistemasEnviados)) { //se o usuário enviou algum sistema
                $aSistemaNovo = array_diff($aSistemasEnviados, $aSistemaBanco);

                foreach ($aSistemaNovo as $p) {
                    $oSistema = new UsuarioSistema();
                    $oSistema->usuario_id = $oUsuario->id;
                    $oSistema->sistema_id = $p;
                    $oSistema->save();
                }
                $aSistemasExcluidos = array_diff($aSistemaBanco, $aSistemasEnviados);

                UsuarioSistema::where('usuario_id', $oUsuario->id)
                    ->whereIn('sistema_id', $aSistemasExcluidos)
                    ->delete();
            }

            DB::commit();
            return response()->json(array('msg' => 'Usuário atualizado com sucesso.'));
        } catch (\Exception $e) {
            DB::rollback();
            if (config('app.debug')) {
                return response()->json(array('msg' => $e->getMessage()), 422);
            } else {
                return response()->json(array('msg' => 'Falha ao atualizar usuário'), 422);
            }
        }
    }

    public function alterarSenha()
    {
        $itens = [];
        return view('Seguranca::usuario.alterar-senha', compact('itens'));
    }

    
    public function atualizarSenha(\Illuminate\Http\Request $request)
    {
        $senhaAtual = request('senha_atual');
        $novaSenha = request('nova_senha');

        if(!$senhaAtual) {
            return redirect('seguranca/usuario/alterar-senha')->with('error', 'Informe a senha atual')->withInput();
        }

        if(!$novaSenha) {
            return redirect('seguranca/usuario/alterar-senha')->with('error', 'Informe a nova senha')->withInput();
        }

        $oUsuario = Auth::user();

        if ($oUsuario->senha != sha1($senhaAtual)) {
            return redirect('seguranca/usuario/alterar-senha')->with('error', 'Senha atual inválida')->withInput();
        }

        try {

            $oUsuario = Usuario::find($oUsuario->id);
            $oUsuario->senha = $novaSenha;
            $oUsuario->senha2 = null;
            $oUsuario->primeiro_acesso = false;
            $oUsuario->save();

            return redirect('');
        } catch(Exception $ex) {
            return response()->json(['message' => 'Erro ao trocar a senha. '.$ex->getMessage()], 500);
        }
    }
    

    /*
    public function atualizarSenha(\Illuminate\Http\Request $request)
    {
        $senhaAtual = request('senha_atual');
        $novaSenha = request('nova_senha');

        $oUsuario = Auth::user();

        if ($oUsuario->senha != sha1($senhaAtual)) {
            return response()->json(array('message' => 'Senha atual inválida.'), 422);
        }

        try {

            $oUsuario = Usuario::find($oUsuario->id);
            $oUsuario->senha = $novaSenha;
            $oUsuario->senha2 = null;
            $oUsuario->primeiro_acesso = false;
            $oUsuario->save();

            return response()->json(array('message' => 'Senha alterada com sucesso.'));
        } catch(Exception $ex) {
            return response()->json(['message' => 'Erro ao trocar a senha. '.$ex->getMessage()], 500);
        }
    }
    */

    public function editarAjuda()
    {
        return view('Seguranca::usuario.editar-ajuda');
    }

    public function excluir()
    {
        $usuario_id = request('id');

        $oUsuario = Usuario::find($usuario_id);
        $oUsuario->excluido = true;
        $oUsuario->save();

        try {
            Usuario::excluir($usuario_id);
            DB::commit();
            return response()->json(array('Usuário excluído com sucesso'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json('Falha ao excluir usuário')->setStatusCode(422);
        }
    }
}
