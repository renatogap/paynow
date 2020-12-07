<?php

namespace App\Http\Controllers;

use App\Models\Entity\Pedido;
use App\Models\Entity\PedidoItem;
use Parque\Seguranca\App\Models\Entity\SegGrupo;
use App\Models\Entity\UsuarioTipoCardapio;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;

class CozinhaController extends Controller
{
    public function monitor()
    {
        $tipoCardapioCozinha = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();

        $pedidos = DB::table('pedido as p')->distinct()
            ->join('pedido_item as pi','pi.fk_pedido', '=', 'p.id')
            ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
            ->whereIn('c.fk_tipo_cardapio', $tipoCardapioCozinha)

            
            //->where('p.status', 1)
            ->where('pi.status', 1)
            ->where('c.cozinha', 1)
            ->select('p.*')
            ->orderBy('p.id','asc')
            ->get();

        /*$itens = [];    
        if($pedidos->count() > 0) {
            foreach($pedidos as $p) {
                $itens[] = DB::table('pedido_item as pi')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->where('c.fk_tipo_cardapio', $tipoCardapioCozinha)
                    ->where('pi.fk_pedido', $p->id)
                    ->where('pi.status', 1)
                    ->get();
            }
        }

        dd($itens);*/

        #$pedidos = Pedido::where('status', 1)->orderBy('id','asc')->get();
        return view('cozinha.monitor', compact('pedidos', 'tipoCardapioCozinha'));
    }

    public function confirma($id_pedido)
    {
        $tipoCardapio = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();

        return view('cozinha.confirmar', compact('id_pedido', 'tipoCardapio'));
    }

    public function pedidoPronto($id_pedido)
    {
        DB::beginTransaction();

        try {
            //Atualiza o status do pedido e pedido item para "PRONTO"
            //PedidoItem::where('fk_pedido', $id_pedido)->update(['status' => 2]);

            $tiposDeCardapios = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();
            
            if(count($tiposDeCardapios) == 0) {
                return response()->json(['message' => 'Seu usuário não está vinculado a nenhum Ponto de Venda.'], 500);
            }

            $itens = DB::table('pedido_item as pi')
            ->join('cardapio as c', 'c.id', '=', 'fk_item_cardapio')
            ->where('fk_pedido', $id_pedido)
            ->whereIn('c.fk_tipo_cardapio', $tiposDeCardapios)
            //->where('c.fk_tipo_cardapio', request('tipo'))
            ->select(['pi.*', 'c.fk_tipo_cardapio'])
            ->where('pi.status', 1)
            ->get();

            if($itens->count() == 0) {
                return response()->json(['message' => 'Não foi possível deixar o pedido pronto. Os ítens deste pedido não foram localizados.'], 500);
            }
            
            $pedido = Pedido::find($id_pedido);
            
            //pega o perfil de quem fez o pedido
            $perfisUsuarioQueFezPedido = SegGrupo::where('usuario_id', $pedido->fk_usuario)->get()->pluck('perfil_id')->toArray();
            
            //pega os restaurantes do usuário do pedido
            $tiposDeCardapiosUsuaroDoPedido = UsuarioTipoCardapio::where('fk_usuario', $pedido->fk_usuario)->get()->pluck('fk_tipo_cardapio')->toArray();
            

            foreach($itens as $item) {
                $itemRow = PedidoItem::find($item->id);                
                $itemRow->status = 2;
                $itemRow->dt_pronto = date('Y-m-d H:i:s');
                $itemRow->save();
                
                //O usuário que fez o pedido é um PDV
                if(in_array(4, $perfisUsuarioQueFezPedido)){
                
                   //Quem fez o pedido é deste restaurante
                   if(in_array($item->fk_tipo_cardapio, $tiposDeCardapiosUsuaroDoPedido)){
                   	$itemRow = PedidoItem::find($item->id);                
		        $itemRow->status = 3;
		        $itemRow->dt_entregue = date('Y-m-d H:i:s');
		        $itemRow->save();
                   }
		}
            }


            $itensPedidoSolicitados = PedidoItem::where('fk_pedido', $id_pedido)->where('status', 1)->get();

            if($itensPedidoSolicitados->count() == 0) {
                Pedido::find($id_pedido)->update(['status' => 2, 'dt_pronto' => date('Y-m-d H:i:s')]);
            }

            DB::commit();
            return redirect('cozinha/monitor')->with('sucesso', 'O Pedido agora está pronto para entrega');
            #return response()->json(['message' => 'Pedido preparado.']);
        } catch(\Exception $ex) {
            DB::rollback();
            //request()->session()->flush('error', 'Não foi possível deixar o pedido pronto. Tente novamente mais tarde. '.$ex->getMessage());
            return redirect('cozinha/monitor')->with('error', 'Não foi possível deixar o pedido pronto. Tente novamente. '.$ex->getMessage());
            #return response()->json(['message' => 'Não foi possível deixar o pedido pronto. Tente novamente mais tarde. '.$ex->getMessage()], 500);
            //return redirect('cozinha/monitor')->with('error', 'Não foi possível deixar o pedido pronto. Tente novamente mais tarde. '.$ex->getMessage());
        }
    }
}
