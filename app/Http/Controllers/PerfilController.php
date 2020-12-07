<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\SegPerfil;
use Parque\Seguranca\App\Models\Entity\SegPermissao;
use Parque\Seguranca\App\Models\Facade\PerfilDB;
use Parque\Seguranca\App\Models\Facade\PermissaoDB;
use Parque\Seguranca\App\Models\Paginacao;
use Parque\Seguranca\App\Models\Regras\PerfilRegras;
use Parque\Seguranca\App\Requests\PerfilRequest;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil.index');
    }

    public function grid()
    {
        $oPerfil = new PerfilDB();
        return response()->json(Paginacao::dataTables($oPerfil->grid(), true));
    }

    public function novo()
    {
        $oUsuario = Auth::user();
        $aAcao = PerfilRegras::permissoesUsuario($oUsuario->id);

        return view('perfil.novo', compact('aAcao'));
    }

    public function store(PerfilRequest $request)
    {
        $oPerfil = new SegPerfil();
        $oPerfil->nome = request('nome');

        DB::beginTransaction();
        try {
            $oPerfil->save();
            $aAcao = request('acao', []);

            $oPermissao = new PermissaoDB();
            $aAcao = array_unique(array_merge($aAcao, $oPermissao->dependencia($aAcao)));

            foreach ($aAcao as $a) {
                $oSegPermissao = new SegPermissao();
                $oSegPermissao->acao_id = $a;
                $oSegPermissao->perfil_id = $oPerfil->id;
                $oSegPermissao->save();
            }

            DB::commit();
             $request->session()->flash('alerta', 'Perfil cadastrado com sucesso'); //colocando na sessão pra um único acesso (removido assim que acessado)
            return response()->json(['perfil' => $oPerfil->id]);
        } catch (\Exception $e) {
            DB::rollback();
            if (config('app.debug')) {
                return response()->json(['message' => $e->getMessage()], 500);
            } else {
                return response()->json(['message' => 'Falha ao cadastrar Perfil', 500]);
            }
        }
    }

    public function editar(Request $request, $id)
    {
        //verifica se deve exibir alerta ao entrar na página
        $alerta = $request->session()->get('alerta');

        $oUsuario = Auth::user();

        //todas as ações com destaque = true que o usuário tem acesso
        $aAcao = PerfilRegras::permissoesUsuario($oUsuario->id);

        //baixando permissões do perfil solicitado
        $oPerfil = SegPerfil::find($id);
        $aPermissoes = $oPerfil->permissoes();


        return view('perfil.editar', compact('oPerfil', 'aAcao', 'aPermissoes', 'alerta'));
    }

    public function update(PerfilRequest $request)
    {

        DB::beginTransaction();
        try {

            $oParams = new \stdClass;
            $oParams->perfil = request('perfil');
            $oParams->nome = request('nome');
            $oParams->aAcoesEnviadas = request('acao', []);

            PerfilRegras::atualizarPerfil($oParams);

            DB::commit();
            $request->session()->flash('alerta', 'Perfil atualizado com sucesso'); //colocando na sessão pra um único acesso (removido assim que acessado)
            return response()->json(array('message' => 'Perfil atualizado com sucesso.', 'perfil' => $oParams->perfil));
        } catch (\Exception $e) {
            DB::rollback();

            if (config('app.debug')) {
                return response()->json(['message' => $e->getMessage()], 500);
            } else {
                return response()->json(['message' => 'Erro ao salvar perfil', 500]);
            }
        }
    }

    public function destroy()
    {
        $id = request('perfil', null);

        DB::beginTransaction();
        try {

            PerfilRegras::excluirPerfil($id);
            DB::commit();
            return response()->json(array('message' => 'Perfil excluído com sucesso.'));
        } catch (\Exception $e) {

            DB::rollback();
            if (config('app.debug')) {
                return response()->json(['message' => $e->getMessage()], 500);
            } else {
                return response()->json(['message' => 'Falha ao excluir perfil.'], 500);
            }

        }

    }
}
