<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cardapio;
use App\Models\Entity\CardapioFoto;
use App\Models\Entity\CardapioTipo;
use App\Models\Entity\Cartao;
use App\Models\Entity\CartaoCliente;
use App\Models\Entity\EntradaCredito;
use App\Models\Entity\Estoque;
use App\Models\Entity\Pedido;
use App\Models\Entity\PedidoItem;
use App\Models\Entity\SaidaCredito;
use App\Models\Entity\SituacaoCartao;
use App\Models\Entity\UsuarioTipoCardapio;
use App\Models\Facade\CardapioDB;
use App\Models\Regras\PedidoRegras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use Parque\Seguranca\App\Models\Entity\SegPerfil;
use Parque\Seguranca\App\Models\Facade\PerfilDB;
use Parque\Seguranca\App\Models\Facade\UsuarioDB;

class PedidoController extends Controller
{

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

    public function cardapios()
    {
        $tiposDeCardapios = getTiposDeCardapio();
        $tipo_cardapios = CardapioTipo::whereIn('id', $tiposDeCardapios)
                            ->where('status', 1)
                            ->orderBy('nome')->get();
        
        return view('pedido.tipos-cardapio', compact('tipo_cardapios'));
    }

    public function cardapio($id_tipo_cardapio)
    {
        $myCardapio = CardapioDB::pesquisar($id_tipo_cardapio);
        return view('pedido.cardapio', compact('myCardapio'));
    }

    public function pedidoItem($id)
    {
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();
        $cardapio = Cardapio::where('id', $id)->first();
        $fotoCardapio = CardapioFoto::where('fk_cardapio', $id)->select(['id'])->first(); 
        $mesa = null;

        if(request()->session()->exists('pedido') && isset(request()->session()->get('pedido')[0]->mesa)){
            $mesa = request()->session()->get('pedido')[0]->mesa;
        }

        return view('pedido.pedido-item', compact('cardapio', 'fotoCardapio', 'mesa', 'perfisUsuario'));
    }

    public function addPedidoCliente(Request $request)
    {
        if(!$request->quantidade || $request->quantidade <= 0) {
            return redirect('pedido/cardapio/item/'.$request->id_cardapio)
                ->with('error', 'A quantidade informada é inválida')
                ->with('observacao', $request->observacao);
        }
        #$request->session()->flush(); die;
        $sessao = [];
        
        if ($request->session()->exists('pedido')) {
            $sessao = $request->session()->get('pedido');
        }
        
        $params = (object) $request->all();

        $sessao[] = $params;

        session(["pedido" => $sessao]);

        return redirect('pedido/cardapio/'.$request->id_tipo_cardapio);
    }

    public function leitor()
    {
        if(request('taxaServico') == "true"){
            request()->session()->flash('taxaServico', true);
        }

        $taxaServico = (request('taxaServico') == "true" ? true : false);
        

        return view('pedido.leitor-cartao', compact('taxaServico'));
    }

    public function confirmarPedido()
    {
        $perflUsuario = DB::table('usuario as u')
            ->join('seg_grupo as g', 'g.usuario_id', '=', 'u.id')
            ->where('u.id', Auth::user()->id)
            ->select('g.perfil_id')
            ->get()->pluck('perfil_id')->toArray();
        
            
        $pedido = request()->session()->get('pedido');

        if( isset($pedido[request('remove')]) ){

            unset($pedido[request('remove')]);

            
            if(count($pedido) > 0){
                session(["pedido" => $pedido]);
                redirect('pedido/confirmar-pedido');
            }
            else{
                request()->session()->forget('pedido');
                return redirect('pedido/cardapios');
            }
        }

        return view('pedido.confirmar-pedido', compact('pedido', 'perflUsuario'));
    }

    public function finalizarPedido($codigo)
    {
        $params = new \StdClass();

        //verifica se o pedido ainda está na sessão
        if(!request()->session()->exists('pedido')) {
            return redirect('pedido/cardapios')->with('error', 'Não há pedidos registrados no momento.');
        }

        $params->cartao = Cartao::where('codigo', $codigo)->first();

        //pega o pedido da sessão e armazena na variável
        $params->pedidoCliente = request()->session()->get('pedido');


        //verifica falha de leitura do cartão
        if(!$params->cartao){
            return redirect('pedido/confirmar-pedido')->with('error', 'Não foi possível ler o QR Code do cartão, tente novamente.');
        }

        $situacao = SituacaoCartao::find($params->cartao->fk_situacao);

        //verifica se o cartão está ativo
        if($params->cartao->fk_situacao !== 2) {
            return redirect('pedido/confirmar-pedido')->with('error', 'Não foi possível finalizar o pedido. Este cartão se encontra <b>'.$situacao->nome.'</b> e não está habilitado para uso.');
        }
        
        //pega o cartão ativo para o cliente
        $params->cartaoCliente = CartaoCliente::where('fk_cartao', $params->cartao->id)->where('status', 2)->first();

        //verifica se o cartão do cliente foi encontrado
        if(!$params->cartaoCliente){
            return redirect('pedido/confirmar-pedido')->with('error', 'Não foi possível localizar o cartão do cliente. Tente novamente.');
        }

        //verifica se o cartão do cliente está ativo
        if($params->cartaoCliente->status != 2){
            return redirect('pedido/confirmar-pedido')->with('error', 'Não foi possível finalizar o pedido. Este cartão não está habilitado para uso.');
        }


        //Validação do ESTOQUE
        $msgErro = [];
        foreach($params->pedidoCliente as $item) {
            
            $estoqueItem = Estoque::where('fk_item_cardapio', $item->id_cardapio)->first();
            if($estoqueItem && $estoqueItem->qtd_atual < $item->quantidade) {
                $cardapio = Cardapio::find($item->id_cardapio);
                $msgErro[] = '* '.$cardapio->nome_item.' insuficiente. Quantidade no estoque: <b>'.$estoqueItem->qtd_atual.'</b>';
            }
        }

        if(count($msgErro)>0){
            return redirect('pedido/confirmar-pedido')->with('error', implode('<br>', $msgErro));
        }
        //End validação Estoque


        $params->valorTotalPedido = array_sum(array_column($params->pedidoCliente, 'valor'));

        $params->taxaServico = 0;

        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();

        //if(in_array(3, $perfisUsuario)){
        //if(request()->session()->get('taxaServico')){if(request()->session()->get('taxaServico')){
        if(request('taxa_servico')){
            $params->taxaServico = ($params->valorTotalPedido * (10 / 100));

            $arr = explode('.', $params->taxaServico);

            if(count($arr) > 1) {

                $arr[1] = (strlen($arr[1]) < 2) ? intval($arr[1].'0') : $arr[1];

                if($arr[1] >= 51) {
                    $params->taxaServico = ($arr[0] + 1);
                }else {
                    $params->taxaServico = $arr[0];
                }
            }

            //$params->taxaServico = arredondar($params->valorTotalPedido);

            $params->valorTotalPedido = $params->valorTotalPedido + $params->taxaServico;
        }


        //verifica se existe saldo no cartão para finalizar o pedido
        if($params->cartaoCliente->valor_atual < $params->valorTotalPedido) {
            return redirect('pedido/confirmar-pedido')
                ->with('error', 'Crédito insuficiente no cartão. O saldo atual é de: <b>R$ '.$params->cartaoCliente->valor_atual.'</b>');
        } 


        DB::beginTransaction();

        try {

            //Regras
            PedidoRegras::salvarPedido($params);

            request()->session()->forget('pedido');

            DB::commit();
            return view('pedido.pedido-finalizado', compact('params'));
        } catch(\Exception $ex) {
            DB::rollback();
            die('<h1>Ops, um erro ocorreu!</h1><br>'.$ex->getMessage());
            return redirect('pedido/cardapios')->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }


    public function meusPedidos()
    {
        $pedidos = DB::table('pedido as p')
                    ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')

                    ->where('p.fk_usuario', Auth::user()->id)
                    ->whereIn('pi.status', [1,2]) //Solicitado e Pronto
                    
                    #->where('p.dt_pedido', '>=', date('Y-m-d 00:00:00'))
                    #->where('p.dt_pedido', '<=', date('Y-m-d 23:59:59'))


                    //->whereIn('p.status', [1,2]) //Solicitado e Pronto
                    ->select(['c.fk_tipo_cardapio', 'p.mesa', 'p.id', 'pi.status', 'p.dt_pedido', 'pi.visto_pelo_promotor', 't.apelido'])
                    //->select(['p.mesa', 'p.id', 'p.status'])
                    ->groupBy('c.fk_tipo_cardapio', 'p.mesa', 'p.id', 'pi.status', 'pi.visto_pelo_promotor', 't.apelido')
                    //->groupBy('p.mesa', 'p.id', 'p.status')
                    ->orderBy('pi.status', 'DESC')
                    
                    ->orderBy('p.dt_pedido', 'ASC')
                    
                    ->get();

        #dd($pedidos);

        return view('pedido.meus-pedidos', compact('pedidos'));
    }

    public function historicoPedido($id_pedido, $tipo)
    {
        $pedidos = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')
            ->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
            ->join('situacao_pedido as s', 's.id', '=', 'p.status')
            ->select([
                'p.id', 't.nome as tipo_cardapio', 'p.mesa', 'p.dt_pedido', 'p.status', 's.nome as situacao',
                'c.fk_tipo_cardapio', 'c.nome_item', 'c.valor as valor_unit', 'c.unid',
                'cc.nome as categoria',
                'pi.id as id_item_pedido', 'pi.quantidade', 'pi.valor as valor_total_item', 'pi.observacao', 'pi.status', 'pi.dt_pronto'
            ])
            ->where('c.fk_tipo_cardapio', $tipo)
            ->where('p.id', $id_pedido)
            ->where('pi.status', '!=', 3) //Entregue
            ->get();

        $myDados = [];
        if($pedidos->count() > 0){

            foreach($pedidos as $i => $item) {
                $myDados[$item->tipo_cardapio][] = $item;
            }
        }else {
            return redirect('pedido/meus-pedidos');
        }
        
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();


        return view('pedido.historico-pedido', compact('myDados', 'pedidos', 'perfisUsuario'));
    }

    public function historicoPedidoGerente($id_pedido, $tipo)
    {
        $pedidos = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')
            ->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
            ->join('situacao_pedido as s', 's.id', '=', 'p.status')
            ->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
            ->select([
                'p.id', 't.nome as tipo_cardapio', 'p.mesa', 'p.dt_pedido', 's.nome as situacao', 'p.status as status_pedido',
                'c.fk_tipo_cardapio', 'c.nome_item', 'c.valor as valor_unit', 'c.unid',
                'cc.nome as categoria',
                'pi.id as id_item_pedido', 'pi.quantidade', 'pi.valor as valor_total_item', 'pi.observacao', 'pi.status', 'pi.dt_pronto',
                'u.nome as usuario'
            ])
            ->where('c.fk_tipo_cardapio', $tipo)
            ->where('p.id', $id_pedido)
            //->where('pi.status', '!=', 3) //Entregue
            ->get();

        $myDados = [];
        if($pedidos->count() > 0){
            foreach($pedidos as $i => $item) {
                $myDados[$item->tipo_cardapio][] = $item;
            }
        }else {
            return redirect('pedido/visualizacao-gerente');
        }

        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();


        return view('pedido.historico-pedido-gerente', compact('myDados', 'pedidos', 'perfisUsuario'));
    }


    public function historicoPedidos($mesa)
    {
        $pedidos = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
            //->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
            ->select([
                'ct.nome as tipo_cardapio', 'p.id', 'p.mesa', 'p.dt_pedido', 'p.dt_pronto', 'p.dt_entrega',
                'c.nome_item', 'c.valor as valor_unit',
                'pi.id as id_item_pedido', 'pi.quantidade', 'pi.valor as valor_total_item', 'pi.observacao'
            ])
            ->where('pi.status', '!=', 4)
            ->where('p.mesa', $mesa)
            ->orderBy('p.mesa', 'ASC')
            ->get();

        $myDados = [];
        if($pedidos->count() > 0){
            foreach($pedidos as $i => $item) {
                $myDados[$item->tipo_cardapio][] = $item;
            }
        }

        #dd($myDados);

        return view('pedido.historico-pedidos', compact('myDados','mesa'));
    }

    public function confirmarCancelamento($item, $id_tipo_cardapio)
    {
        $pedido = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->select(['p.*', 'c.nome_item'])
            ->where('pi.id', $item)
            ->where('pi.status', '!=', 4)
            ->first();

        return view('pedido.confirmar-cancelamento', compact('item', 'pedido', 'id_tipo_cardapio'));    
    }

    public function confirmarCancelamentoGerente($item, $id_tipo_cardapio)
    {
        $pedido = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->select(['p.*', 'c.nome_item'])
            ->where('pi.id', $item)
            ->where('pi.status', '!=', 4)
            ->first();

        return view('pedido.confirmar-cancelamento-gerente', compact('item', 'pedido', 'id_tipo_cardapio'));    
    }

    public function confirmarCancelamentoGerente2($item, $codigo)
    {
        $pedido = DB::table('pedido as p')
            ->join('pedido_item as pi', 'p.id', '=', 'pi.fk_pedido')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->select(['p.*', 'c.nome_item'])
            ->where('pi.id', $item)
            ->where('pi.status', '!=', 4)
            ->first();

        return view('pedido.confirmar-cancelamento-gerente2', compact('item', 'pedido', 'codigo'));    
    }
    

    public function cancelarItem($item, $id_tipo_cardapio)
    {
        DB::beginTransaction();

        try {
            $pedido = PedidoRegras::cancelarPedidoItem($item);

            DB::commit();
            return redirect('pedido/historico-pedido/'.$pedido->id.'/'.$id_tipo_cardapio)->with('sucesso', 'O ítem foi <b>Cancelado</b> com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('pedido/historico-pedido/'.$pedido->id.'/'.$id_tipo_cardapio)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function cancelarItemGerente($item, $id_tipo_cardapio)
    {
        DB::beginTransaction();

        try {
            $pedido = PedidoRegras::cancelarPedidoItem($item);

            DB::commit();
            return redirect('pedido/historico-pedido-gerente/'.$pedido->id.'/'.$id_tipo_cardapio)->with('sucesso', 'O ítem foi <b>Cancelado</b> com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('pedido/historico-pedido-gerente/'.$pedido->id.'/'.$id_tipo_cardapio)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function cancelarItemGerente2($item, $codigo)
    {
        DB::beginTransaction();

        try {
            
            PedidoRegras::cancelarPedidoItem($item);

            DB::commit();
            return redirect('relatorio/fechamento-conta/'.$codigo)->with('sucesso', 'O ítem foi <b>Cancelado</b> com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            dd($ex->getMessage());
            return redirect('relatorio/fechamento-conta/'.$codigo)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function confirmarEntrega($id_pedido, $tipo)
    {
        return view('pedido.confirmar-entrega', compact('id_pedido', 'tipo'));
    }

    public function confirmarEntregaGerente($id_pedido, $tipo)
    {
        return view('pedido.confirmar-entrega-gerente', compact('id_pedido', 'tipo'));
    }

    public function salvarEntrega($id_pedido, $tipo)
    {
        DB::beginTransaction();

        try {
            $itens = DB::table('pedido_item as pi')
            ->join('cardapio as c', 'c.id', '=', 'fk_item_cardapio')
            ->where('fk_pedido', $id_pedido)
            ->where('c.fk_tipo_cardapio', $tipo)
            ->select(['pi.*', 'c.fk_tipo_cardapio'])
            ->get();

            foreach($itens as $item) {
                $itemRow = PedidoItem::find($item->id);
                $itemRow->status = 3;
                $itemRow->dt_entregue = date('Y-m-d H:i:s');
                $itemRow->save();
            }


            $itensPedidoSolicitados = PedidoItem::where('fk_pedido', $id_pedido)->where('status', '!=', 3)->where('status', 4)->get();

            if($itensPedidoSolicitados->count() == 0) {
                Pedido::find($id_pedido)->update(['status' => 3, 'dt_entrega' => date('Y-m-d H:i:s')]);
            }

            #PedidoItem::where('fk_pedido', $id_pedido)->update(['status' => 3]);
            #Pedido::find($id_pedido)->update(['status' => 3, 'dt_entrega' => date('Y-m-d H:i:s')]);
            DB::commit();
            return redirect('pedido/meus-pedidos')->with('sucesso', 'O Pedido foi entregue.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('pedido/historico-pedido/'.$id_pedido)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function salvarEntregaGerente($id_pedido, $tipo)
    {
        DB::beginTransaction();

        try {
            $itens = DB::table('pedido_item as pi')
            ->join('cardapio as c', 'c.id', '=', 'fk_item_cardapio')
            ->where('fk_pedido', $id_pedido)
            ->where('c.fk_tipo_cardapio', $tipo)
            ->select(['pi.*', 'c.fk_tipo_cardapio'])
            ->get();

            foreach($itens as $item) {
                $itemRow = PedidoItem::find($item->id);
                $itemRow->status = 3;
                $itemRow->dt_entregue = date('Y-m-d H:i:s');
                $itemRow->save();
            }


            $itensPedidoSolicitados = PedidoItem::where('fk_pedido', $id_pedido)->where('status', '!=', 3)->get();

            if($itensPedidoSolicitados->count() == 0) {
                Pedido::find($id_pedido)->update(['status' => 3, 'dt_entrega' => date('Y-m-d H:i:s')]);
            }

            #PedidoItem::where('fk_pedido', $id_pedido)->update(['status' => 3]);
            #Pedido::find($id_pedido)->update(['status' => 3, 'dt_entrega' => date('Y-m-d H:i:s')]);
            DB::commit();
            return redirect('pedido/historico-pedido-gerente/'.$id_pedido.'/'.$tipo)->with('sucesso', 'O Pedido foi entregue.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('pedido/historico-pedido-gerente/'.$id_pedido.'/'.$tipo)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }



    //Visualização do Gerente
    public function visualizacaoGerente()
    {
        $pedidos = DB::table('pedido as p')
                    ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
                    ->join('usuario as u', 'u.id', '=', 'p.fk_usuario')
                    ->whereIn('pi.status', [1,2]) //Solicitado e Pronto
                    #->where('p.dt_pedido', '>=', date('Y-m-d 00:00:00'))
                    #->where('p.dt_pedido', '<=', date('Y-m-d 23:59:59'))

//                    ->where('pi.status', '!=', 4) //Solicitado e Pronto
                    ->select(['ct.nome as tipo_cardapio', 'c.fk_tipo_cardapio', 'p.mesa', 'p.id', 'p.dt_pedido', 'pi.status', 'u.nome as usuario', 'pi.dt_pronto'])
                    ->groupBy('c.fk_tipo_cardapio', 'p.mesa', 'p.id', 'p.dt_pedido', 'pi.status', 'pi.dt_pronto', 'u.nome')
                    ->orderBy('ct.nome')
                    ->orderBy('pi.status', 'DESC')
                    ->orderBy('p.dt_pedido')
                    ->get();       

        $pedidosAll = [];
        if($pedidos->count() > 0){
            foreach($pedidos as $i => $item) {
                $pedidosAll[$item->tipo_cardapio][] = $item;
            }
        }
        
        return view('pedido.ver-todos-pedidos', compact('pedidosAll'));
    }


}
