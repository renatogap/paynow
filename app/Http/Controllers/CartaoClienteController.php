<?php

namespace App\Http\Controllers;

use App\Models\Entity\Cartao;
use App\Models\Entity\CartaoCliente;
use App\Models\Entity\EntradaCredito;
use App\Models\Entity\SaidaCredito;
use App\Models\Entity\SituacaoCartao;
use App\Models\Entity\TipoCliente;
use App\Models\Entity\TipoPagamento;
use App\Models\Facade\CartaoClienteDB;
use App\Models\Regras\CartaoClienteRegras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\DB;
use Parque\Seguranca\App\Models\Entity\SegGrupo;

class CartaoClienteController extends Controller
{
    private $msgError = [];
    
    public function index(Request $request)
    {
        $lista = [];

        if(COUNT($request->all()) > 0) {
            if(!$request->data && !$request->cpf && !$request->nome) {
                return redirect('cartao-cliente')->with('error', 'Informe ao menos um campo para pesquisar.');
            }
        }

        $lista = CartaoClienteDB::grid($request);

        return view('cartao-cliente.index', compact('lista', 'request'));
    }

    public function create($codigo)
    {
        $cartao = Cartao::where('codigo', $codigo)->first();

        if(!$cartao) {
            return redirect('cartao-cliente')->with('error', 'Falha na leitura do cartão.');
        }

        //Se for um cartão Ativo do Cliente ir para a Edição
        $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->whereIn('status', [2, 3])->first();

        if($cartaoCliente){
            return redirect('cartao-cliente/edit/'.$cartaoCliente->id);
        }

        #if($cartao->fk_situacao == 3) {
        #    return redirect('cartao-cliente')->with('error', 'Este cartão se encontra <b>'.$cartao->situacao->nome.'</b>.');
        #}

        $tipo = TipoCliente::all();
        $formaPagamento = TipoPagamento::where('id', '!=', 4)->where('id', '!=', 5)->get(); //Estorno

        return view('cartao-cliente.create', compact('tipo','cartao', 'formaPagamento'));
    }

    public function store(Request $request)
    {
        $params = (object) $request->all();
        
        $cartao = Cartao::where('hash', $params->hash)->first();

        $params->id_cartao = $cartao->id;
        
        if(!$this->validaForm($params)) {
            return redirect('cartao-cliente/create/'.$cartao->codigo)->with('error', implode('<br>', $this->msgError))->withInput();
        }

        DB::beginTransaction();

        try {
            CartaoClienteRegras::salvar($params);
            DB::commit();
            return redirect('cartao-cliente')->with('sucesso', 'Registro salvo com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao-cliente/create/'.$cartao->codigo)
                    ->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage())
                    ->withInput();
        }
    }

    public function edit($id)
    {
        $entrada = CartaoCliente::find($id);
        $tipo = TipoCliente::all();
        $formaPagamento = TipoPagamento::where('id', '!=', 4)->where('id', '!=', 5)->get(); //Estorno
        $cartao = Cartao::find($entrada->fk_cartao);
        $perfisUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();
        
        return view('cartao-cliente.edit', compact('entrada', 'cartao', 'tipo', 'formaPagamento', 'perfisUsuario'));
    }


    public function leitorCartao()
    {
        return view('cartao.leitor-cartao');
    }

    public function addCredito($codigo)
    {
        $cartao = Cartao::where('codigo', $codigo)->first();

        if(!$cartao){
            return redirect('cartao-cliente/leitor')->with('error', 'Falha na leitura do cartão.');
        }

        $situacao = SituacaoCartao::find($cartao->fk_situacao);

        if(!in_array($cartao->fk_situacao, [2])) {
            return redirect('cartao-cliente/leitor')->with('error', 'Este cartão não está disponível para uso. Situação atual do cartão <b>'.$cartao->situacao->nome.'</b>.');
        }

        $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->where('status', 2)->first();

        if(!$cartaoCliente) {
            return redirect('cartao-cliente/leitor')->with('error', 'Não foi possível localizar este cartão. O cartão não está disponível para o cliente.');
        }

        $formaPagamento = TipoPagamento::where('id', '!=', 4)->where('id', '!=', 5)->get(); //Estorno

        return view('cartao-cliente.add-credito', compact('cartaoCliente', 'cartao', 'formaPagamento'));
    }

    public function salvarCredito(Request $request)
    {
        $p = (object) $request->all();

        $cartaoCliente = CartaoCliente::find($p->id_cartao_cliente);


        $cartao = Cartao::find($cartaoCliente->fk_cartao);

        if($p->valor <= 0) {
            return redirect('cartao-cliente/add-credito/'.$cartao->codigo)->with('error', 'Informe um valor de crédito válido.')->withInput();
        }

        DB::beginTransaction();

        try {
            EntradaCredito::create([
                'fk_cartao_cliente' => $p->id_cartao_cliente,
                'valor' => formatarMoeda($p->valor),
                'fk_tipo_pagamento' => $p->tipo_pagamento,
                'observacao' => 'Recarga de crédito',
                'data' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);

            $cartaoCliente = CartaoCliente::find($p->id_cartao_cliente);
            $cartaoCliente->valor_atual = ($cartaoCliente->valor_atual + $p->valor);
            $cartaoCliente->save();

            DB::commit();
            return redirect('cartao-cliente/confirma-credito')
                        ->with('sucesso', 'O valor de <b>R$ '.number_format($p->valor, 2, ',', '.').'</b> foi creditado no cartão informado.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao-cliente/leitor')->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function confirmaCredito()
    {
        return view('cartao-cliente.confirma-credito');
    }

    public function bloqueiaDesbloqueia(Request $request)
    {
        DB::beginTransaction();        

        try {
            $cartao = Cartao::where('codigo', $request->codigo)->first();
            $cartao->fk_situacao = $request->status;
            $cartao->save();
            
            //Verifica se o cartão está bloqueado ou em uso
            $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->whereIn('status', [2, 3])->first();
            $cartaoCliente->status = $request->status;
            $cartaoCliente->save();

            DB::commit();

            $request->session()->flash('sucesso', 'O status do cartão foi atualizado com sucesso.');
            return response()->json(['message' => 'Ok.']) ;
        } catch(\Exception $ex) {
            DB::rollback();
            $request->session()->flash('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
            return response()->json(['message' => 'Error']) ;
        }
    }

    public function devolverCartao(Request $request)
    {
        DB::beginTransaction();        

        try {
            $cartao = Cartao::where('codigo', $request->codigo)->first();

            $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->whereIn('status', [2])->first();

            if($request->valorDevolvido > config('parque.limite_devolucao')) {
                return response()->json(['message' => 'O valor informado ultrapassa o valor limite de devolução.'], 500) ;
            }

            //Sim, o Cliente Devolveu o Cartão
            if($request->rdCartaoDevolvido == 'S'){

                if($request->valorDevolvido > ($cartaoCliente->valor_atual + $cartaoCliente->valor_cartao)){
                    return response()->json(['message' => 'O valor informado excede o crédito do Cartão mais a Caução.'], 500) ;
                }


                $cartao->fk_situacao = 1;
                $cartao->save();


                $cartaoCliente->status = 1;
                $cartaoCliente->devolvido = 'S';
                $cartaoCliente->valor_cartao = '0.00';
                $cartaoCliente->valor_devolvido = $request->valorDevolvido;
                $cartaoCliente->valor_atual = (($cartaoCliente->valor_atual + $cartaoCliente->valor_cartao) - $request->valorDevolvido);
                
                $textoSaida = 'Devolução de '.number_format($request->valorDevolvido, 2, ',', '.').' (cartão '.$cartao->codigo.' devolvido)';
            }
            
            //Não, o Cliente Levou o Cartão
            else {

                if($request->valorDevolvido > $cartaoCliente->valor_atual){
                    return response()->json(['message' => 'O valor informado excede o crédito do Cartão.'], 500) ;
                }

                $cartaoCliente->devolvido = 'N';
                $cartaoCliente->valor_devolvido = $request->valorDevolvido;
                $cartaoCliente->valor_atual = ($cartaoCliente->valor_atual - $request->valorDevolvido);

                $textoSaida = 'Devolução de '.number_format($request->valorDevolvido, 2, ',', '.').' (cartão '.$cartao->codigo.' não entregue)';
            }

            $cartaoCliente->dt_devolucao = date('Y-m-d H:i:s');
            $cartaoCliente->save();


            SaidaCredito::create([
                'fk_cartao_cliente' => $cartaoCliente->id,
                'valor' => $request->valorDevolvido,
                'data' => date('Y-m-d H:i:s'),
                'observacao' => $textoSaida,
                'fk_usuario' => Auth::user()->id
            ]);


            DB::commit();

            $request->session()->flash('sucesso', 'Devolução registrada com sucesso.');
            return response()->json(['message' => 'Ok.']) ;
        } catch(\Exception $ex) {
            DB::rollback();
            #request()->session()->flash('error', 'Erro ao devolver o cartão.<br>'. $ex->getMessage());
            return response()->json(['message' => 'Erro ao devolver o cartão.<br>'. $ex->getMessage()], 500) ;
        }
    }

    public function transferirCredito($codigo)
    {
        DB::beginTransaction();        

        $id_cartao_atual = request('id');

        try {
            
            $cartaoClienteAtual = CartaoCliente::find($id_cartao_atual);

            if(!$cartaoClienteAtual) {
                return redirect('cartao-cliente/edit/'.$id_cartao_atual)->with('error', 'Não foi possível localizar o cartão atual');
            }

            if($cartaoClienteAtual->status != 3) {
                return redirect('cartao-cliente/edit/'.$id_cartao_atual)->with('error', 'O cartão deve ser bloqueado para poder realizar a transferência.');
            }

            $valorNovoCartao = config('parque.valor_cartao');
            $valorCartaoClienteAtual = ($cartaoClienteAtual->valor_atual - $valorNovoCartao);

            

            $cartao = Cartao::where('codigo', $codigo)->first();

            if(!$cartao) {
                return redirect('cartao-cliente/edit/'.$id_cartao_atual)->with('error', 'Falha na leitura do cartão.');
            }

            if($cartao->fk_situacao != 1) {
                return redirect('cartao-cliente/edit/'.$id_cartao_atual)->with('error', 'Operação não realizada. Este cartão se encontra <b>'.$cartao->situacao->nome.'</b>.');
            }


            $cartaoCliente = CartaoCliente::create([
                'fk_cartao' => $cartao->id,
                'nome' => $cartaoClienteAtual->nome,
                'cpf' => $cartaoClienteAtual->cpf ? preg_replace('/[^0-9]/', '', $cartaoClienteAtual->cpf) : null,
                'telefone' => $cartaoClienteAtual->telefone ?? null,
                'fk_tipo_cliente' => $cartaoClienteAtual->fk_tipo_cliente,
                'valor_atual' => formatarMoeda($valorCartaoClienteAtual),
                'valor_cartao' => formatarMoeda($valorNovoCartao),
                'fk_tipo_pagamento' => $cartaoClienteAtual->fk_tipo_pagamento,
                'observacao' => 'Transferência de crédito',
                'status' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);



            //Atualiza o saldo zero e informa qual o id do cartão que foi transferido
            $cartaoClienteAtual->valor_atual = 0;
            $cartaoClienteAtual->fk_cartao_transferido = $cartaoCliente->id;
            $cartaoClienteAtual->save();


            /*EntradaCredito::create([
                'fk_cartao_cliente' => $cartaoCliente->id,
                'valor' => $valorCartaoClienteAtual,
                'fk_tipo_pagamento' => $cartaoClienteAtual->fk_tipo_pagamento,
                'observacao' => 'Transferência de Crédito de outro cartão',
                'data' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);*/

            Cartao::where('id', $cartao->id)->update(['fk_situacao' => 2]);

            DB::commit();
            return redirect('cartao-cliente/edit/'.$cartaoCliente->id)
                        ->with('sucesso', 'Transferência de crédito realizada com sucesso.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao-cliente/edit/'.request('id'))->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function zerarCartao(Request $request)
    {
        DB::beginTransaction();

        try {

            $cartaoCliente = CartaoCliente::find($request->id);
            
            $creditoDoCartao = $cartaoCliente->valor_atual;

            $cartaoCliente->valor_atual = 0;
            $cartaoCliente->save();


            SaidaCredito::create([
                'fk_cartao_cliente' => $cartaoCliente->id,
                'valor' => $creditoDoCartao,
                'data' => date('Y-m-d H:i:s'),
                'observacao' => 'Zerando o Cartão do Cliente',
                'fk_usuario' => Auth::user()->id
            ]);


            DB::commit();
            $request->session()->flash('sucesso', 'O Cartão foi zerado com sucesso.');
            return response()->json(['message' => 'Ok.']) ;
        } catch(\Exception $ex) {
            DB::rollback();
            $request->session()->flash('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
            return response()->json(['message' => 'Error']) ;
        }
    }

    public function leitorTransferencia()
    {
        return view('cartao-cliente.leitor-transferencia');
    }

    public function dadosTransferencia($codigo)
    {
        $cartao = Cartao::where('codigo', $codigo)->first();

        if(!$cartao){
            return redirect('cartao-cliente/leitor')->with('error', 'Falha na leitura do cartão.');
        }

        $situacao = SituacaoCartao::find($cartao->fk_situacao);

        if(!in_array($cartao->fk_situacao, [2])) {
            return redirect('cartao-cliente/leitor')->with('error', 'Este cartão não está disponível para uso. Situação atual do cartão <b>'.$cartao->situacao->nome.'</b>.');
        }

        $cartaoCliente = CartaoCliente::where('fk_cartao', $cartao->id)->where('status', 2)->first();

        if(!$cartaoCliente) {
            return redirect('cartao-cliente/leitor')->with('error', 'O cartão não está disponível para o cliente.');
        }

        return view('cartao-cliente.dados-transferencia', compact('cartaoCliente', 'cartao'));
    }

    public function salvarTransferencia(Request $request)
    {
        $p = (object) $request->all();

        //Cartão de Orígem
        $cartaoClienteOrigem = CartaoCliente::find($p->id_cartao_origem);
        $cartaoOrigem = Cartao::where('id', $cartaoClienteOrigem->fk_cartao)->where('fk_situacao', 2)->first();
        

        //Cartão de Destino
        $cartaoDestino = Cartao::where('codigo', $p->cartao_destino)->where('fk_situacao', 2)->first();

        if(!$cartaoDestino) {
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'Falha na leitura do cartão.')->withInput();
        }

        if($cartaoDestino->fk_situacao != 2) {
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'O cartão de destino não está habilitado para uso. Situação: <b>'.$cartaoDestino->situacao->nome.'</b>.')->withInput();
        }

        $cartaoClienteDestino = CartaoCliente::where('fk_cartao', $cartaoDestino->id)->where('status', 2)-> first();

        if(!$cartaoClienteDestino) {
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'O cartão-cliente de destino não está habilitado para o cliente.')->withInput();
        }

        if($p->valorTransferencia <= 0) {
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'Informe um valor de crédito válido.')->withInput();
        }

        if($cartaoClienteOrigem->valor_atual < $p->valorTransferencia) {
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'Saldo insuficiente para realizar esta transferência.')->withInput();
        }


        DB::beginTransaction();

        try {
            $valorCartaoOrigem = formatarMoeda($cartaoClienteOrigem->valor_atual - $p->valorTransferencia);
            $valorCartaoDestino = formatarMoeda($cartaoClienteDestino->valor_atual + $p->valorTransferencia);
            
            
            //Cartão de Destino
            EntradaCredito::create([
                'fk_cartao_cliente' => $cartaoClienteDestino->id,
                'valor' => $p->valorTransferencia,
                'fk_tipo_pagamento' => 5, //TRANSFERENCIA
                'observacao' => 'Crédito por Transferência',
                'data' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);

            $cartaoClienteDestino->valor_atual = $valorCartaoDestino;
            $cartaoClienteDestino->save();


            //Cartão Orígem

            SaidaCredito::create([
                'fk_cartao_cliente' => $cartaoClienteOrigem->id,
                'valor' => $p->valorTransferencia,
                'data' => date('Y-m-d H:i:s'),
                'observacao' => 'Transferência de crédito',
                'fk_usuario' => Auth::user()->id
            ]);

            $cartaoClienteOrigem->valor_atual = $valorCartaoOrigem;
            $cartaoClienteOrigem->save();

            DB::commit();
            return redirect('cartao-cliente/confirma-transferencia')
                        ->with('sucesso', 'O valor de <b>R$ '.number_format($p->valorTransferencia, 2, ',', '.').'</b> foi transferido para cartão do(a) <b>'.$cartaoClienteDestino->nome.'</b>.');
        } catch(\Exception $ex) {
            DB::rollback();
            return redirect('cartao-cliente/dados-transferencia/'.$cartaoOrigem->codigo)->with('error', 'Um erro ocorreu.<br>'. $ex->getMessage());
        }
    }

    public function confirmaTransferencia()
    {
        return view('cartao-cliente.confirma-transferencia');
    }


    public function validaFormAddCartao($p)
    {
        $this->msgError = [];

        if(!$p->nome) {
            $this->msgError[] = 'O campo <b>Nome</b> é obrigatório';
        }
        if(!$p->tipo) {
            $this->msgError[] = 'O campo <b>Tipo de Cliente</b> é obrigatório';
        }
        if(!$p->tipo_pagamento) {
            $this->msgError[] = 'O campo <b>Tipo de Pagamento</b> é obrigatório';
        }
        if(!$p->valor) {
            $this->msgError[] = 'O campo <b>Valor</b> é obrigatório';
        }

        if(count($this->msgError) > 0){
            return false;
        }

        return true;
    }


    public function validaForm($p)
    {
        $this->msgError = [];

        if(!$p->nome) {
            $this->msgError[] = 'O campo <b>Nome</b> é obrigatório';
        }
        if(!$p->tipo) {
            $this->msgError[] = 'O campo <b>Tipo de Cliente</b> é obrigatório';
        }

        if(!$p->tipo_pagamento) {
            $this->msgError[] = 'O campo <b>Tipo de Pagamento</b> é obrigatório';
        }

        //Só valida o valor no cadastro
        if(!$p->id) {
            if(!$p->valor) {
                $this->msgError[] = 'O campo <b>Valor</b> é obrigatório';
            }
        }

        if(!$p->valorCartao) {
            $this->msgError[] = 'O campo <b>Valor do Cartão</b> é obrigatório';
        }

        if(count($this->msgError) > 0){
            return false;
        }

        return true;
    }

}
