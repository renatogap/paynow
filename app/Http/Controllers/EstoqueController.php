<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cardapio;
use App\Models\Entity\CardapioTipo;
use App\Models\Entity\Estoque;
use App\Models\Entity\EstoqueEntrada;
use App\Models\Entity\EstoqueSaida;
use App\Models\Entity\TipoUnidadeMedida;
use App\Models\Entity\UsuarioTipoCardapio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\SegGrupo;

class EstoqueController extends Controller
{
    public function index()
    {
        $id_tipo_cardapio = request('id_tipo_cardapio');
        $itensCardapio = null;
        $itensEstoquePDV = null;

        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        if($id_tipo_cardapio){
            $tiposCardapios = [$id_tipo_cardapio];

            //$itensCardapio = Cardapio::where('fk_tipo_cardapio', $id_tipo_cardapio)->get();
            
            $itens = DB::table('cardapio as c')
                ->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
                ->where('c.fk_tipo_cardapio', $id_tipo_cardapio)
                ->select(['c.id', 'c.nome_item', 'cc.nome as categoria'])
                ->orderBy('cc.nome')
                ->orderBy('c.nome_item')
                ->get();

            if($itens->count() > 0) {
                $itensCardapio = [];
                foreach($itens as $it) {
                    $itensCardapio[$it->categoria][$it->id] = $it->nome_item;
                }
            }

            //$itensEstoquePDV = $this->gridEstoque();

            $itensEstoquePDV = DB::table('estoque as e')
                ->join('cardapio as c', 'c.id', '=', 'e.fk_item_cardapio')
                ->join('tipo_unidade_medida as um', 'um.id', '=', 'e.fk_tipo_unidade_medida')
                ->select(['e.*', 'c.nome_item', 'um.nome as unidade_medida'])
                ->where('e.fk_tipo_cardapio', $id_tipo_cardapio)
                ->get();

            //$itensEstoquePDV = Estoque::where('fk_tipo_cardapio', $id_tipo_cardapio)->orderBy('id', 'DESC')->get();
        }else {
            if(in_array(4, $perfisUsuario)){
                $tiposCardapios = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();
                //$tiposCardapios = CardapioTipo::orderBy('id')->whereIn('id', $perfisUsuario)->get()->pluck('id')->toArray();
            }else {
                $tiposCardapios = CardapioTipo::orderBy('nome')->get()->pluck('id')->toArray();
            }
        }       
        
        $comboTipo = CardapioTipo::whereIn('id', $tiposCardapios)->orderBy('nome')->get();

        $comboPDVsDestino = CardapioTipo::orderBy('nome')->get();        

        $tipoUnidadeMedida = TipoUnidadeMedida::all();
        
        return view('estoque.index', compact('comboTipo', 'comboPDVsDestino', 'id_tipo_cardapio', 'itensCardapio', 'itensEstoquePDV', 'tipoUnidadeMedida'));
    }

    public function gridEstoque()
    {
        $entrada = DB::table('estoque as e')
                    ->leftJoin('estoque_entrada as ee', 'ee.fk_item_cardapio', '=', 'e.fk_item_cardapio')
                    ->join('cardapio as c', 'c.id', '=', 'e.fk_item_cardapio')
                    ->join('cardapio_tipo as ct', 'ct.id', '=', 'e.fk_tipo_cardapio')
                    #->leftJoin('usuario as u', 'u.id', '=', 'ee.fk_usuario_cad')
                    #->where('ee.created_at', '>=', "$dtInicio $horaInicio")
                    #->where('ee.created_at', '<=', "$dtTermino $horaTermino")
                    ->select([
                        'e.id as id_estoque_entrada',
                        'ct.id as id_tipo_cardapio',
                        'ct.nome',
                        'c.id as id_item_cardapio',
                        'c.nome_item as produto',
                        'e.qtd_atual',
                        'e.qtd_dose_por_garrafa',
                        'ee.quantidade',
                        'ee.valor_unitario',
                        'ee.valor_total',
                        'ee.created_at as data',
                        'ee.observacao',
                        DB::raw("'Entrada' as tipo")
                    ])
                    ->orderBy('ee.id', 'DESC');

        $saida = DB::table('estoque as e')
                    ->leftJoin('estoque_saida as es', 'es.fk_item_cardapio', '=', 'e.fk_item_cardapio')
                    ->join('cardapio as c', 'c.id', '=', 'e.fk_item_cardapio')
                    ->join('cardapio_tipo as ct', 'ct.id', '=', 'e.fk_tipo_cardapio')
                    #->leftJoin('usuario as u', 'u.id', '=', 'es.fk_usuario')
                    #->leftJoin('pedido_item as pi', 'pi.id', '=', 'es.fk_pedido_item')
                    ->where(DB::raw('es.fk_pedido_item is null'))
                    #->where('es.created_at', '>=', "$dtInicio $horaInicio")
                    #->where('es.created_at', '<=', "$dtTermino $horaTermino")
                    ->select([
                        'e.id as id_estoque_saida',
                        'ct.id as id_tipo_cardapio',
                        'ct.nome',
                        'c.id as id_item_cardapio',
                        'c.nome_item as produto',
                        'e.qtd_atual',
                        'e.qtd_dose_por_garrafa',
                        'es.quantidade',
                        DB::raw("'-' as valor_unitario"),
                        DB::raw("'-' as valor_total"),
                        'es.created_at as data',
                        'es.observacao',
                        DB::raw("'Saida' as tipo")
                    ])
                    ->orderBy('es.id', 'DESC');            
        
        $dados = null;

        //dd(request('soEntradas'), request('soSaidas'), $entrada, $saida);
        
        $dados = $entrada->union($saida)->orderBy('data', 'DESC')->get();

        return $dados;
    }

    public function getEstoqueItem($id)
    {
        $estoqueItem = Estoque::where('fk_item_cardapio', $id)->first();
        return response()->json($estoqueItem);
    }

    public function store(Request $request)
    {
        $p = (object) $request->all();

        if($p->tipoUnidadeMedida == 2 && !$p->qtdDosePorGarrafa) {
            return redirect('estoque?id_tipo_cardapio='.$p->tipo_cardapio)->with('error', 'Obrigatório o campo <b>Doses por Garrafa</b>')->withInput();
        }


        DB::beginTransaction();

        try {

            if($p->tipoMovimento == 'E') {

                //Entrada do Estoque
                EstoqueEntrada::create([
                    'fk_tipo_cardapio' => $p->tipo_cardapio,
                    'fk_item_cardapio' => $p->item_cardapio,
                    'quantidade' => $p->quantidade,
                    'valor_unitario' => $p->valor,
                    'valor_total' => ($p->qtdDosePorGarrafa ? ($p->valor * ($p->quantidade/$p->qtdDosePorGarrafa)) : $p->valor * $p->quantidade),
                    'observacao' => 'Entrada no Estoque',
                    'fk_usuario_cad' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            else if($p->tipoMovimento == 'S') {
                if(!$p->observacao) {
                    return redirect('estoque?id_tipo_cardapio='.$p->observacao)->with('error', 'Obrigatório o campo <b>Observação</b>')->withInput();
                }

                EstoqueSaida::create([
                    'fk_tipo_cardapio' => $p->tipo_cardapio,
                    'fk_item_cardapio' => $p->item_cardapio,
                    'quantidade' => $p->quantidade,
                    'fk_pedido_item' => null,
                    'observacao' => $p->observacao ?? 'Saída manual pelo usuário '.Auth::user()->nome,
                    'fk_usuario' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }


            //Estoque
            $estoque = Estoque::where('fk_item_cardapio', $p->item_cardapio)->first();
            if(!$estoque) { $estoque = new Estoque(); }
            $estoque->fk_tipo_cardapio = $p->tipo_cardapio;
            $estoque->fk_item_cardapio = $p->item_cardapio;
            $estoque->tipo_movimento = $p->tipoMovimento;

            if($p->tipoMovimento == 'E'){
                $estoque->qtd_atual = ($estoque->qtd_atual + $p->quantidade);

                $estoque->estoque_minimo = $p->estoque_minimo ?? null;
                $estoque->estoque_maximo = $p->estoque_maximo ?? null;
                $estoque->fk_tipo_unidade_medida = $p->tipoUnidadeMedida;
                $estoque->qtd_dose_por_garrafa = $p->qtdDosePorGarrafa;
            }else {
                $estoque->qtd_atual = ($estoque->qtd_atual - $p->quantidade);                
            }

            $estoque->dt_ultima_atualizacao = date('Y-m-d H:i:s');
            $estoque->fk_usuario_cad = Auth::user()->id;
            $estoque->created_at = date('Y-m-d H:i:s');
            $estoque->save();

            DB::commit();
            return redirect('estoque?id_tipo_cardapio='.$p->tipo_cardapio)->with('sucesso', 'Estoque atualizado com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('estoque?id_tipo_cardapio='.$p->tipo_cardapio)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage())->withInput();
        }
    }

    public function impressao()
    {
        $soEntradas = request('soEntradas') ?? null;
        $soSaidas = request('soSaidas') ?? null;
        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:01'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i', strtotime('+ 1 minutes')));

        $entrada = null;
        $saida = null;

        if(request('soEntradas') || (!request('soEntradas') && !request('soSaidas'))){
        $entrada = DB::table('estoque as e')
                    ->leftJoin('estoque_entrada as ee', 'ee.fk_item_cardapio', '=', 'e.fk_item_cardapio')
                    ->join('cardapio as c', 'c.id', '=', 'e.fk_item_cardapio')
                    ->join('cardapio_tipo as ct', 'ct.id', '=', 'e.fk_tipo_cardapio')
                    ->leftJoin('usuario as u', 'u.id', '=', 'ee.fk_usuario_cad')
                    ->where('ee.created_at', '>=', "$dtInicio $horaInicio")
                    ->where('ee.created_at', '<=', "$dtTermino $horaTermino")
                    ->select([
                        'ct.id as id_tipo_cardapio',
                        'ct.nome',
                        'c.id as id_item_cardapio',
                        'c.nome_item as produto',
                        'e.qtd_atual',
                        'e.qtd_dose_por_garrafa',
                        'ee.quantidade',
                        'ee.valor_unitario',
                        'ee.valor_total',
                        'ee.created_at as data',
                        'ee.observacao',
                        'u.nome as usuario',
                        DB::raw("'E' as tipo")
                    ])
                    ->orderBy('ee.id', 'DESC');
        }

        if(request('soSaidas') || (!request('soEntradas') && !request('soSaidas'))){
        $saida = DB::table('estoque as e')
                    ->leftJoin('estoque_saida as es', 'es.fk_item_cardapio', '=', 'e.fk_item_cardapio')
                    ->join('cardapio as c', 'c.id', '=', 'e.fk_item_cardapio')
                    ->join('cardapio_tipo as ct', 'ct.id', '=', 'e.fk_tipo_cardapio')
                    ->leftJoin('usuario as u', 'u.id', '=', 'es.fk_usuario')
                    ->leftJoin('pedido_item as pi', 'pi.id', '=', 'es.fk_pedido_item')
                    ->where('es.created_at', '>=', "$dtInicio $horaInicio")
                    ->where('es.created_at', '<=', "$dtTermino $horaTermino")
                    ->select([
                        'ct.id as id_tipo_cardapio',
                        'ct.nome',
                        'c.id as id_item_cardapio',
                        'c.nome_item as produto',
                        'e.qtd_atual',
                        'e.qtd_dose_por_garrafa',
                        'es.quantidade',
                        DB::raw("0 as valor_unitario"),
                        DB::raw("(es.quantidade * pi.valor) as valor_total"),
                        'es.created_at as data',
                        'es.observacao',
                        'u.nome as usuario',
                        DB::raw("'S' as tipo")
                    ])
                    ->orderBy('es.id', 'DESC');            
        }
        
        $dados = null;

        //dd(request('soEntradas'), request('soSaidas'), $entrada, $saida);
        
        if($entrada && $saida) {
            $dados = $entrada->union($saida)->orderBy('data', 'DESC')->get();
        }
        else if($entrada && !$saida) {
            $dados = $entrada->orderBy('data', 'DESC')->get();
        }
        else if(!$entrada && $saida) {
            $dados = $saida->orderBy('data', 'DESC')->get();
        }
        
        $estoquePdvs = [];

        if($dados->count() > 0) {
            foreach($dados as $d) {
                $estoquePdvs[$d->nome.'_'.$d->id_tipo_cardapio][$d->produto.'__'.$d->qtd_atual.'__'.$d->id_item_cardapio][] = $d;
            }
        }

        

        return view('estoque.impressao', compact('soEntradas', 'soSaidas', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino', 'estoquePdvs'));

    }
}
