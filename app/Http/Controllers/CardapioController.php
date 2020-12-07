<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cardapio;
use App\Models\Entity\CardapioCategoria;
use App\Models\Entity\CardapioFoto;
use App\Models\Entity\CardapioTipo;
use App\Models\Facade\CardapioDB;
use App\Models\Regras\CardapioRegras;
use Illuminate\Http\Request;
use Parque\Seguranca\App\Models\DB;

class CardapioController extends Controller
{
    public function index()
    {
        $tiposCardapios = getTiposDeCardapio();

        $myCardapio = CardapioDB::pesquisarAdmin($tiposCardapios);

        return view('cardapio.index', compact('myCardapio'));
    }

    public function create()
    {
        $id_tipo_cardapio = request('id_tipo_cardapio');
        $comboCategoria   = null;

        if($id_tipo_cardapio){
            $tiposCardapios = [$id_tipo_cardapio];
            $comboCategoria = CardapioCategoria::whereIn('fk_tipo_cardapio', $tiposCardapios)->get();
        }else {
            $tiposCardapios = getTiposDeCardapio();
        }
        
        $comboTipo = CardapioTipo::whereIn('id', $tiposCardapios)->get();

        

        return view('cardapio.create', compact('comboTipo', 'comboCategoria', 'id_tipo_cardapio'));
    }

    public function edit($id)
    {
        //pega o id caso o usuario mude o tipo na edição
        $id_tipo_cardapio = request('id_tipo_cardapio');

        $tiposCardapios = getTiposDeCardapio();
        
        $cardapio = Cardapio::where('id', $id)->first();
        
        if($id_tipo_cardapio){
            $tiposCardapios = [$id_tipo_cardapio];
            $comboCategoria = CardapioCategoria::whereIn('fk_tipo_cardapio', $tiposCardapios)->get();
        }else{
            $comboCategoria = CardapioCategoria::whereIn('fk_tipo_cardapio', [$cardapio->fk_tipo_cardapio])->get();
        }

        $comboTipo = CardapioTipo::whereIn('id', $tiposCardapios)->get();

        $fotoCardapio = CardapioFoto::where('fk_cardapio', $id)->select(['id'])->first(); 

        return view('cardapio.edit', compact('cardapio', 'comboTipo', 'comboCategoria', 'fotoCardapio', 'id_tipo_cardapio'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            //$params = (object) $request->all();
            CardapioRegras::salvar($request);
            DB::commit();

            if(!$request->id){
                return redirect('cardapio/create?id_tipo_cardapio='.$request->tipo)->with('sucesso', 'Item do cardápio salvo com sucesso.');
            }else {
                return redirect('cardapio/edit/'.$request->id)->with('sucesso', 'Item do cardápio salvo com sucesso.');
            }
        } catch(\Exception $ex) {
            DB::rollback();
            if(!$request->id){
                return redirect('cardapio/create?id_tipo_cardapio='.$request->tipo)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
            }else {
                return redirect('cardapio/edit/'.$request->id)->with('sucesso', 'Item do cardápio salvo com sucesso.');
            }    
        }
    }

    public function tipoCardapio()
    {
        $action = (request('action') ?? '');

        $tiposCardapios = CardapioTipo::orderBy('nome')->where('status', 1)->select(['id', 'nome', 'thumbnail'])->get();

        //$tiposCardapios = getTiposDeCardapio();

        return view('cardapio.tipo-cardapio', compact('tiposCardapios', 'action'));
    }

    public function deletarCardapio($id)
    {
        DB::beginTransaction();

        try {

            if(Cardapio::where('fk_tipo_cardapio', $id)->first()) {
                return redirect('cardapio/tipo-cardapio?action=create')->with('error', 'Este Cardápio não pode ser removido. Existem ítens de cardápio vinculados ao mesmo.')->withInput();
            }
            
            if(CardapioCategoria::where('fk_tipo_cardapio', $id)->first()) {
                return redirect('cardapio/tipo-cardapio?action=create')->with('error', 'Este Cardápio não pode ser removido. Existem Categorias vinculadas ao mesmo.')->withInput();
            }

            //dd('Deletou...');
            //DB::commit();
            return redirect('cardapio/tipo-cardapio?action=create')->with('sucesso', 'Desculpe, esta função ainda não foi desenvolvida.');
        } catch(\Exception $ex) {
            //DB::rollback();
            return redirect('cardapio/tipo-cardapio?action=create')->with('error', 'Error ao remover o Cardápio.'. $ex->getMessage())->withInput();
        }
    }


    public function verThumbTipoCardapio($id)
    {
        $thumb = CardapioTipo::find($id);
        header('Content-Type:'.$thumb->type);
        exit($thumb->thumbnail);
    }

    public function salvarTipoCardapio(Request $request)
    {
        DB::beginTransaction();

        try {
            if(!$request->id){
                $tipoCardapio = CardapioTipo::create(['nome' => $request->tipo]);
            } else {
                $tipoCardapio = CardapioTipo::find($request->id);
                $tipoCardapio->nome = $request->tipo;
                $tipoCardapio->desabilitarLog();
                $tipoCardapio->save();
            }

            if(isset($request->foto) && $request->foto){
                CardapioRegras::addAnexosTipoCardapio($request, $tipoCardapio);
            }

            DB::commit();
            return redirect('cardapio/tipo-cardapio?action='.$request->action)->with('sucesso', 'Tipo de Cardápio salvo com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cardapio/tipo-cardapio?action='.$request->action)->with('error', 'Error ao salvar o Tipo de Cardápio.'. $ex->getMessage())->withInput();
        }
    }

    public function salvarCategoria(Request $request)
    {
        DB::beginTransaction();

        try {

            CardapioCategoria::create([
                'nome' => request('categoria'),
                'fk_tipo_cardapio' => request('tipo')
            ]);

            DB::commit();
            return response()->json(['message' => 'Categoria salva com sucesso.']);
        } catch(\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => 'Erro ao tentar salvar a categoria.<br>'.$ex->getMessage()], 500);
        }
    }

    public function inativarItem()
    {
        DB::beginTransaction();

        try {
            $cardapio = Cardapio::find(request('id'));
            $cardapio->status = 0;
            $cardapio->save();

            DB::commit();
            request()->session()->flash('sucesso', 'O ítem do cardápio está inativo.');
            return response()->json(['message' => 'O ítem do cardápio está inativo.']);
        } catch(\Exception $ex) {
            DB::rollback();
            request()->session()->flash('error', 'Erro ao tentar inativar o ítem do cardápio.<br>'.$ex->getMessage());
            return response()->json(['message' => 'Erro ao tentar inativar o ítem do cardápio.<br>'.$ex->getMessage()], 500);
        }
    }

    public function ativarItem()
    {
        DB::beginTransaction();

        try {
            $cardapio = Cardapio::find(request('id'));
            $cardapio->status = 1;
            $cardapio->save();

            DB::commit();
            request()->session()->flash('sucesso', 'O ítem do cardápio está ativo.');
            return response()->json(['message' => 'O ítem do cardápio está ativo.']);
        } catch(\Exception $ex) {
            DB::rollback();
            request()->session()->flash('error', 'Erro ao tentar ativar o ítem do cardápio.<br>'.$ex->getMessage());
            return response()->json(['message' => 'Erro ao tentar ativar o ítem do cardápio.<br>'.$ex->getMessage()], 500);
        }
    }

    public function verFoto($id)
    {
        $foto = CardapioFoto::where('fk_cardapio', $id)->first();
        header('Content-Type:'.$foto->type);
        exit($foto->foto);
    }

    public function verThumb($id)
    {
        $foto = CardapioFoto::where('fk_cardapio', $id)->first();
        header('Content-Type:'.$foto->type);
        exit($foto->thumbnail);
    }
    
}

