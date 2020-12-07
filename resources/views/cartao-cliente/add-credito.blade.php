@extends('layouts.default')
@section('conteudo')
    <h4>
        <span class="material-icons icone" style="font-size: 1.5em;">local_atm</span> Adicionar Crédito
        <a href="{{url('cartao-cliente/leitor')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h4>
    <hr>

    <form id="formulario" method="post" action="{{ url('cartao-cliente/salvar-credito') }}">
        {{ @csrf_field() }}

        <input type="hidden" name="id_cartao_cliente" value="{{ $cartaoCliente->id }}">

        @if (session('sucesso'))
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('sucesso') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <div><label>Nome do Cliente</label></div>
                    <input type="text" value="{{$cartaoCliente->nome}}" class="form-control" readonly>
                </div>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div><label>Valor do Crédito *</label></div>
                    <input type="number" name="valor" class="form-control" required value="{{ (old('valor') ?? '') }}" onkeyup="changeValor(this.value)">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                <div><label>Forma de Pagamento *</label></div>
                    <select name="tipo_pagamento" id="formaPagamento" class="form-control" required onchange="changeTipoPagamento(this)">
                        <option value="">SELECIONE...</option>
                        @foreach($formaPagamento as $p)
                            <option {{ (old('tipo_pagamento'))==$p->id ? 'selected' : '' }} value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <button type="button" id="btnSalvar" class="btn btn-parque ml-3 btn" data-toggle="modal" data-target="#modal" disabled >Adicionar</button>
                <a href="{{url('cartao-cliente')}}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        <!-- Modal -->
        <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Confirmação
                        </h5>
                    </div>
                    <div class="modal-body text-center mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>
                                    <span class="material-icons icone text-success" style="font-size: 3em !important;">contact_support</span> 
                                    <div>Adicionar o valor de<br><b id="valorCreditoCliente" style="font-size: 1.5em;"></b> em <b id="tipoPagamento" style="font-size: 1.5em;"></b><br>Confirma ?</div>
                                </h5>
                                <hr>
                                <button type="submit" onclick="aguarde(this)" class="btn btn-parque">Confirmar</button>
                                <a href="#" class="btn btn-danger" data-dismiss="modal">Cancelar</a>

                                <div id="msgError" class="alert alert-danger d-none mt-3"></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('scripts')

<script>
    var valorCredito = document.getElementById('valorCreditoCliente');
    var tipoPagamento = document.getElementById('tipoPagamento');
    var formaPagamento = document.getElementById('formaPagamento');
    var btSalvar = document.getElementById('btnSalvar');

    //inicia setando a forma de pagamento caso seja a página carregue já com o campo selecionado
    //exemplo: caso dê erro, o sistema retorna para a página com os campos preenchidos
    tipoPagamento.textContent = formaPagamento.options[formaPagamento.selectedIndex].textContent;;

    function aguarde(e) {
        e.textContent = 'Aguarde...';
        e.disabled = true;

        $('#formulario').submit();
    }

    document.body.addEventListener('change', function(){
        var msgError = new Array();

            $('#formulario :input').not('button').each(function(i, v){
                if(v.required) {
                    if(!v.value){
                        msgError.push('O campo '+v.parentNode.parentNode.children[0].textContent+' é obrigatório.');
                    }
                }
            });

            if(msgError.length == 0) {
                btSalvar.disabled = false;
            }else {
                //$('#msgError').html(msgError.join('<br>'));
                btSalvar.disabled = true;
            }
    });

    function changeValor(valor) {
        if(valor > 0){
            valorCredito.textContent = 'R$ ' + valor;
            //btSalvar.disabled = false;
        }
        else {
            valorCredito.textContent = 'R$ 0.00';
            //btSalvar.disabled = true;
        }
    }

    function changeTipoPagamento(e) {
        tipoPagamento.textContent = e.options[e.selectedIndex].textContent;
    }
</script>


@endsection
