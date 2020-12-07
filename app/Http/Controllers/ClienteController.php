<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cardapio;
use App\Models\Entity\CardapioFoto;
use App\Models\Entity\CardapioTipo;
use App\Models\Entity\Cartao;
use App\Models\Entity\CartaoCliente;
use App\Models\Entity\SituacaoCartao;
use App\Models\Entity\UsuarioTipoCardapio;
use App\Models\Facade\CardapioDB;
use Parque\Seguranca\App\Models\DB;

class ClienteController extends Controller
{    
    public function index()
    {
        return view('cliente.index');
    }

    public function home()
    {
        return view('cliente.home');
    }

    public function saldo()
    {
        $id_cartao_cliente = session('cliente');
        $cartaoClinete = CartaoCliente::find($id_cartao_cliente);
        return view('cliente.saldo', compact('cartaoClinete'));
    }

    public function pedidos()
    {
        #$cpf = preg_replace('/[^0-9]/', '', request('cpf'));
        $id_cartao_cliente = session('cliente');

        $pedidos = DB::table('pedido as p')
                    ->join('pedido_item as pi', 'pi.fk_pedido', '=', 'p.id')
                    ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                    ->join('cardapio_tipo as t', 't.id', '=', 'c.fk_tipo_cardapio')
                    ->join('cartao_cliente as cc', 'p.fk_cartao_cliente', '=', 'cc.id')
                    ->select([
                        'p.id',
                        't.nome as tipo_cardapio',
                        'cc.nome',
                        'c.nome_item',
                        'pi.observacao',
                        'c.valor as valor_item',
                        'pi.quantidade',
                        'pi.valor as valor_total_item',
                        'pi.status',
                        'p.valor_total',
                        'p.taxa_servico',
                        'p.dt_pedido',
                        'p.dt_pronto',
                        'p.dt_entrega'
                    ])
                    ->where('cc.id', $id_cartao_cliente)
                    ->get();


        $itensPedidoCliente = [];
        $pedidoCliente = [];

        if($pedidos->count() > 0) {
            foreach($pedidos as $pedido) {
                $itensPedidoCliente[$pedido->id][] = $pedido;

                $pedidoCliente[$pedido->id] = [
                    'id' => $pedido->id,
                    'tipo_cardapio' => $pedido->tipo_cardapio,
                    'nome' => $pedido->nome,
                    'valor_total' => $pedido->valor_total,
                    'taxa_servico' => $pedido->taxa_servico,
                    'status' => $pedido->status,
                    'dt_pedido' => date('d/m/Y', strtotime($pedido->dt_pedido)),
                    'hora_pedido' => date('H:i', strtotime($pedido->dt_pedido)),
                    'hora_pronto' => ($pedido->dt_pronto ? date('H:i', strtotime($pedido->dt_pronto)) : null),
                    'hora_entrega' => ($pedido->dt_entrega ? date('H:i', strtotime($pedido->dt_entrega)) : null),
                ];
            }
        }


        //printvardie($nomeCliente);


        return view('cliente.pedidos', compact('pedidoCliente', 'itensPedidoCliente'));
    }

    public function cardapios()
    {
        $tipo_cardapios = CardapioTipo::orderBy('nome')->get();        
        return view('cliente.tipos-cardapio', compact('tipo_cardapios'));
    }

    public function cardapio($id_tipo_cardapio)
    {
        $myCardapio = CardapioDB::pesquisar($id_tipo_cardapio);
        return view('cliente.cardapio', compact('myCardapio'));
    }

    public function pedidoItem($id)
    {
        $cardapio = Cardapio::where('id', $id)->first();
        $fotoCardapio = CardapioFoto::where('fk_cardapio', $id)->select(['id'])->first(); 
        $mesa = null;

        if(request()->session()->exists('pedido')){
            $mesa = request()->session()->get('pedido')[0]->mesa;
        }

        return view('cliente.pedido-item', compact('cardapio', 'fotoCardapio', 'mesa'));
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

    public function login($codigo)
    {
        //encontra o cartão independente do status
        $cartao = Cartao::where('codigo', $codigo)->first();

        if($cartao->fk_situacao !== 2) {
            return redirect('cliente')->withInput()
                ->with('error', 'Não foi possível localizar o cliente. Este cartão se encontra <b>'.$cartao->situacao->nome.'</b> e não está habilitado para uso.');
        }

        $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->where('status', 2)->first();

        if(!isset($cartaoCliente->status) || $cartaoCliente->status !== 2) {
            return redirect('cliente')->withInput()
                ->with('error', 'Não foi possível localizar o cliente. Este cartão não está em uso.');
        }


        if ($cartaoCliente && !request()->session()->exists('cliente')) {
            request()->session()->put('cliente', $cartaoCliente->id);
        }

        return redirect('cliente/home');
    }

    /*
    public function login()
    {
        dd(request()->all());


        if (!request()->session()->exists('cliente')) {
            $cpf = preg_replace('/[^0-9]/', '', request('cpf'));
            request()->session()->put('cliente', $cpf);
        }

        return redirect('cliente/home');
    }
    */

    public function logout()
    {
        request()->session()->forget('cliente');
        return redirect('cliente');
    }

}
