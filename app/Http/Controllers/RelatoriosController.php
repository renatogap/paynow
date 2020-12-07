<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cartao;
use App\Models\Entity\CartaoCliente;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use App\Models\Entity\UsuarioTipoCardapio;
use App\Models\Facade\CartaoClienteDB;

class RelatoriosController extends Controller
{
    public function index()
    {
        return view('relatorios.index');
    }

    public function detalhadoPdv()
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));

        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:00'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i'));


        // $db = DB::table('pedido as p')
        //     ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
        //     ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
        //     ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
        //     ->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
        //     ->select([
        //         'ct.nome as tipo',
        //         DB::raw("sum(pi.valor) as valor_total"),
        //         'u.nome as usuario'
        //     ])
        //     ->whereBetween('p.dt_pedido', ["$dtInicio 00:00:00", "$dtTermino 23:59:59"])
        //     ->where('p.status', '!=', 4)
        //     ->where('pi.status', '!=', 4)
        //     ->groupBy([
        //         'ct.nome','u.nome'
        //     ])
        //     ->orderBy('ct.nome');


        //Modificado para mostrar o relatório detalhado
        $db = DB::table('pedido as p')
            ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
            ->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
            ->select([
                'p.id as id_pedido',
                'p.mesa',
                'p.created_at as dt_pedido',
                'c.fk_tipo_cardapio',
                'c.unid',
                'pi.id as id_item_pedido',
                'pi.created_at as dt_pedido_item',
                'pi.quantidade',
                'c.nome_item',
                'ct.nome as tipo',
                'pi.valor as valor_item',
                'u.id as id_usuario',
                'u.nome as usuario'
            ])
            ->where('p.created_at', '>=', "$dtInicio $horaInicio")
            ->where('p.created_at', '<=', "$dtTermino $horaTermino")
            ->where('p.status', '!=', 4)
            ->where('pi.status', '!=', 4)
            ->orderBy('ct.nome');


        // se o usuário for PROMOTOR ou PDV
        #if(in_array(3, $perfisUsuario) || in_array(4, $perfisUsuario)){
        if(in_array(3, $perfisUsuario)){
            $db->where('p.fk_usuario', Auth::user()->id);
        }

        if(in_array(4, $perfisUsuario)){
            $tipoCardapioCozinha = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();
            $db->whereIn('c.fk_tipo_cardapio', $tipoCardapioCozinha);
        }

        $dados = $db->get();


        $myDados = [];
        if($dados->count() > 0){
            foreach($dados as $i => $item) {
                $myDados[$item->fk_tipo_cardapio.'_'.$item->tipo][$item->id_usuario.'_'.$item->usuario][] = [
                    'id_pedido' => $item->id_pedido,
                    'mesa' => $item->mesa,
                    'fk_tipo_cardapio' => $item->fk_tipo_cardapio,
                    'dt_pedido' => $item->dt_pedido,
                    'id_item_pedido' => $item->id_item_pedido,
                    'quantidade' => ($item->unid ==1 ? intval($item->quantidade) : $item->quantidade),
                    'nome_item' => $item->nome_item,
                    'valor_item' => $item->valor_item
                ];
            }
        }    

        #printvardie($myDados);

        //dd($myDados);
        return view('relatorios.detalhado-pdv', compact('myDados', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino'));
    }

    public function resumoPdv()
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:01'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i', strtotime('+ 1 minutes')));

        #printvardie("$dtInicio $horaInicio a $dtTermino $horaTermino");

        $db = DB::table('pedido as p')
            ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
            ->select([
                'ct.nome as tipo',
                DB::raw("sum(pi.valor) as valor_total")
            ])
            ->where('p.created_at', '>=', "$dtInicio $horaInicio")
            ->where('p.created_at', '<=', "$dtTermino $horaTermino")
            ->where('p.status', '!=', 4)
            ->where('pi.status', '!=', 4)
            ->groupBy([
                'ct.nome'
            ])
            ->orderBy('ct.nome');


        // se o usuário for PROMOTOR
        if(in_array(3, $perfisUsuario)){
            $db->where('p.fk_usuario', Auth::user()->id);
        }
        else if(in_array(4, $perfisUsuario)){
            $tipoCardapioCozinha = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();
            $db->whereIn('c.fk_tipo_cardapio', $tipoCardapioCozinha);
        }

        $result = $db->get();

        #printvardie($result);

        $dados = [];
        if($result->count() > 0){
            foreach($result as $i => $item) {
                $dados[$item->tipo] = $item->valor_total;
            }
        }    

        #printvardie($dados);

        /*
        $dados = DB::select("SELECT s.tipo,
                            sum(s.valor) as valor_total
                    FROM (
                        SELECT
                            ct.nome AS tipo,
                            p.valor_total as valor
                        FROM
                            pedido AS p
                            INNER JOIN pedido_item AS pi ON pi.fk_pedido = p.id
                            INNER JOIN cardapio AS c ON c.id = pi.fk_item_cardapio
                            INNER JOIN cardapio_tipo AS ct ON ct.id = c.fk_tipo_cardapio 
                        WHERE
                            p.dt_pedido BETWEEN '".$dtInicio." 00:00:00' AND '".$dtTermino." 23:59:59'
                            AND p.status != 4 
                            AND pi.status != 4 
                    ) s
                    GROUP BY s.tipo");
                    */

        return view('relatorios.resumo-pdv', compact('dados', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino', 'perfisUsuario'));
    }

    public function taxaServico()
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:00'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i', strtotime('+ 1 minutes')));

        $db = DB::table('pedido as p')
            ->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
            ->select(['p.id', 'p.fk_usuario', 'u.nome as usuario', 'p.taxa_servico', 'p.mesa', 'created_at as dt_pedido'])
            ->where('p.created_at', '>=', "$dtInicio $horaInicio")
            ->where('p.created_at', '<=', "$dtTermino $horaTermino")
            ->where('p.taxa_servico', '>', 0)
            ->orderBy('u.nome');


        // se o usuário for PROMOTOR
        if(in_array(3, $perfisUsuario)){
            $db->where('p.fk_usuario', Auth::user()->id);
        }

        $pedidos = $db->get();

        //dd($pedidos);


        $pedidosAll = [];
        if($pedidos->count() > 0){
            foreach($pedidos as $i => $item) {
                $pedidosAll[$item->usuario][] = $item;
            }
        } 

        return view('relatorios.taxa-servico', compact('pedidosAll', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino'));
    }

    public function consultarPedidos()
    {
        return view('pedido.leitor-consultar-pedido');
    }

    public function fechamentoCaixa()
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:00'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i'));

        $db = DB::table('entrada_credito as e')
            ->join('tipo_pagamento as tp', 'tp.id', '=', 'e.fk_tipo_pagamento')
            ->join('usuario as u', 'u.id', '=', 'e.fk_usuario')
            ->select(['u.id as id_usuario', 'u.nome as usuario', 'e.observacao', 'e.data', 'tp.nome as tipo_pagamento', 'e.valor'])
            ->whereBetween('e.data', ["$dtInicio $horaInicio", "$dtTermino $horaTermino"])
            ->where('e.fk_tipo_pagamento', '!=', 4) //estorno
            ->where('e.fk_tipo_pagamento', '!=', 5) //transferencia
            ->orderBy('e.id');

        // se o usuário for CAIXA
        if(in_array(2, $perfisUsuario)){
            $db->where('e.fk_usuario', Auth::user()->id);
        }

        #$dados = $db->get();


        $sql2 = DB::table('saida_credito as s')
            ->join('usuario as u', 'u.id', '=', 's.fk_usuario')
            ->select(['u.id as id_usuario', 'u.nome as usuario', 's.observacao', 's.data', DB::raw("'DINHEIRO' as tipo_pagamento"), DB::raw("(-1 * s.valor) as valor")])
            ->whereBetween('s.data', ["$dtInicio $horaInicio", "$dtTermino $horaTermino"])
            ->where('s.observacao', '!=', 'Transferência de crédito')
            ->orderBy('s.id');


        // se o usuário for CAIXA
        if(in_array(2, $perfisUsuario)){
            $sql2->where('s.fk_usuario', Auth::user()->id);
        }


        #printvardie();

        $dados = $db->union($sql2)->get();
        

        $entradaCaixa = [];
        if($dados->count() > 0){
            foreach($dados as $i => $item) {
                $entradaCaixa[$item->id_usuario][$item->tipo_pagamento][] = $item;
                //$entradaCaixa[$item->usuario][$item->tipo_pagamento][] = $item;
            }
        } 
        
        return view('relatorios.fechamento-caixa', compact('entradaCaixa', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino'));
    }

    public function fechamentoConta($codigo)
    {
        $cartao = Cartao::where('codigo', $codigo)->first();

        if(!$cartao) {
            return redirect('relatorio/consultar-pedidos')->with('error', 'Cartão não localizado, tente novamente.');
        }

        $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->where('status', 2)->first();

        if(!$cartaoCliente){
            return redirect('relatorio/consultar-pedidos')->with('error', 'Cartão não disponível, dirija-se a uma caixa.');
        }

        $pedidos = DB::table('pedido as p')
                    ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')
                    ->join('cardapio_categoria as ca', 'ca.id', '=', 'c.fk_categoria')
                    ->join('cartao_cliente as cc', 'p.fk_cartao_cliente', '=', 'cc.id')
                    ->leftJoin('usuario as u', 'u.id', '=', 'p.fk_usuario')
                    ->select([
                        'p.id',
                        'pi.id as id_item',
                        't.nome as tipo_cardapio',
                        'ca.nome as categoria',
                        'cc.nome',
                        'c.nome_item',
                        'pi.observacao',
                        'c.unid',
                        'c.valor as valor_item',
                        'pi.quantidade',
                        'pi.valor as valor_total_item',
                        'pi.fk_item_cardapio',
                        'pi.status',
                        'p.mesa',
                        'p.valor_total',
                        'p.taxa_servico',
                        'p.dt_pedido',
                        'p.dt_pronto',
                        'p.dt_entrega',
                        'u.nome as usuario'
                    ])
                    ->where('cc.id', $cartaoCliente->id)
                    ->get();


        $itensPedidoCliente = [];
        $pedidoCliente = [];

        if($pedidos->count() > 0) {
            foreach($pedidos as $pedido) {
                $itensPedidoCliente[$pedido->id][] = $pedido;

                $pedidoCliente[$pedido->id] = [
                    'id' => $pedido->id,
                    'id_item' => $pedido->id_item,
                    'mesa' => $pedido->mesa,
                    'tipo_cardapio' => $pedido->tipo_cardapio,
                    'categoria' => $pedido->categoria,
                    'fk_item_cardapio' => $pedido->fk_item_cardapio,
                    'nome' => $pedido->nome,
                    'valor_total' => $pedido->valor_total,
                    'taxa_servico' => $pedido->taxa_servico,
                    'status' => $pedido->status,
                    'dt_pedido' => date('d/m/Y', strtotime($pedido->dt_pedido)),
                    'hora_pedido' => date('H:i', strtotime($pedido->dt_pedido)),
                    'hora_pronto' => ($pedido->dt_pronto ? date('H:i', strtotime($pedido->dt_pronto)) : null),
                    'hora_entrega' => ($pedido->dt_entrega ? date('H:i', strtotime($pedido->dt_entrega)) : null),
                    'status' => $pedido->status,
                    'usuario' => $pedido->usuario
                ];
            }
        }

        $historicoCartao = CartaoClienteDB::extratoCartaoCliente($cartaoCliente->id);

        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        return view('relatorios.consultar-pedidos', compact('historicoCartao', 'pedidoCliente', 'perfisUsuario', 'itensPedidoCliente', 'codigo', 'cartaoCliente'));       
    }

    public function devolucaoCartoes()
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:00'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i'));


        $sql = CartaoCliente::whereBetween('created_at', ["$dtInicio $horaInicio", "$dtTermino $horaTermino"]);

        // se o usuário for CAIXA
        if(in_array(2, $perfisUsuario)){
            $sql->where('fk_usuario', Auth::user()->id);
        }

        $dados = $sql->get();

        $devolucaoCartoes = [];
        if($dados->count() > 0){
            foreach($dados as $i => $item) {
                $devolucaoCartoes[$item->fk_usuario][] = $item;
                //$devolucaoCartoes[$item->usuario][$item->tipo_pagamento][] = $item;
            }
        }

        return view('relatorios.devolucao-cartoes', compact('devolucaoCartoes', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino'));
    }

    public function cancelamento()
    {
        $dtInicio  = (request('dtInicio') ? request('dtInicio') : date('Y-m-d'));
        $dtTermino = (request('dtTermino') ? request('dtTermino') : date('Y-m-d'));
        $horaInicio  = (request('horaInicio') ? request('horaInicio') : date('00:00'));
        $horaTermino = (request('horaTermino') ? request('horaTermino') : date('H:i', strtotime('+ 1 minutes')));
        

        $dados = DB::table('pedido as p')
                    ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')
                    ->join('cardapio_categoria as ca', 'ca.id', '=', 'c.fk_categoria')
                    ->join('cartao_cliente as cc', 'p.fk_cartao_cliente', '=', 'cc.id')
                    ->leftJoin('usuario as u', 'u.id', '=', 'pi.fk_usuario_cancelamento')
                    ->select([
                        'p.id as id_pedido',
                        'p.mesa',
                        'p.created_at as dt_pedido',
                        'c.fk_tipo_cardapio',
                        'c.unid',
                        'pi.id as id_item_pedido',
                        'pi.created_at as dt_pedido_item',
                        'pi.quantidade',
                        'c.nome_item',
                        't.nome as tipo',
                        'c.valor as valor_item',
                        'u.id as id_usuario',
                        'u.nome as usuario'
                    ])
                    ->where('pi.status', 4)
                    ->where('p.created_at', '>=', "$dtInicio $horaInicio")
                    ->where('p.created_at', '<=', "$dtTermino $horaTermino")
                    ->get();


        $myDados = [];
        if($dados->count() > 0){
            foreach($dados as $i => $item) {
                $myDados[$item->fk_tipo_cardapio.'_'.$item->tipo][$item->id_usuario.'_'.$item->usuario][] = [
                    'id_pedido' => $item->id_pedido,
                    'mesa' => $item->mesa,
                    'fk_tipo_cardapio' => $item->fk_tipo_cardapio,
                    'dt_pedido' => $item->dt_pedido,
                    'id_item_pedido' => $item->id_item_pedido,
                    'quantidade' => ($item->unid ==1 ? intval($item->quantidade) : $item->quantidade),
                    'nome_item' => $item->nome_item,
                    'valor_item' => $item->valor_item,
                    'unid' => $item->unid
                ];
            }
        }

        #printvardie($myDados);


        return view('relatorios.cancelamento', compact('myDados', 'dtInicio', 'dtTermino', 'horaInicio', 'horaTermino'));       
    }
}
