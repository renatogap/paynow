@extends('layouts.default')


@section('conteudo')
    <h3>
        Entrada do Cliente

        <a href="{{url('cartao-cliente')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h3>
    <hr>

    <form id="formulario" method="post" action="{{ url('cartao-cliente/store') }}">
        {{ @csrf_field() }}

        <input type="hidden" name="id" value="">
        <input type="hidden" name="tipo" value="1">
        <input type="hidden" name="hash" value="{{$cartao->hash}}">

        @if (session('sucesso'))
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! session('sucesso') !!}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! session('error') !!}
            </div>
        @endif
        
        <!--
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Hash Cartão</label>
                    <div class="col-md-12">
                        <input type="text" name="hash" value="{{$cartao->hash}}" class="form-control" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>
        -->
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div><label>Data</label></div>
                    <input type="text" name="data" value="{{ date('d/m/Y H:i')}}"  class="form-control" readonly>                        
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <div><label>Nome *</label></div>
                    <input type="text" name="nome" class="form-control" required value="{{ (old('nome')) ?? '' }}">
                </div>
            </div>
            
            
            <!--
            <div class="col-md-4">
                <div class="form-group">
                    <div><label>Tipo Cliente *</label></div>
                    <select name="tipo" class="form-control" rerquired>
                        </select>
                    </div>
                </div>
            </div>
            -->
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>CPF</label></div>
                    <input type="tel" name="cpf" class="form-control" value="{{ (old('cpf')) ?? '' }}" maxlength="14" onkeydown="fMasc(this, mCPF);">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Telefone</label></div>
                    <input type="tel" name="telefone" class="form-control" value="{{ (old('telefone')) ?? '' }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Valor Pago *</label></div>
                    <input type="tel" name="valor" id="valorPago" class="form-control" onkeyup="changeValor(this.value)" required value="{{ (old('valor')) ?? '' }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Tipo de pagamento *</label></div>
                    <select name="tipo_pagamento" class="form-control" required>
                            @foreach($formaPagamento as $p)
                                <option {{ (old('tipo_pagamento'))==$p->id ? 'selected' : '' }} value="{{ $p->id }}">{{ $p->nome }}</option>
                            @endforeach
                        </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Valor do Caução</label></div>
                    <input type="tel" name="valorCartao" id="valorCartao" readonly class="form-control" value="{{ (old('valorCartao')) ?? config('parque.valor_cartao') }}" required>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Crédito do Cartão</label></div>
                    <input type="tel" name="creditoCartao" id="creditoCartao" readonly class="form-control" value="{{ (old('creditoCartao')) ?? '0.00' }}">
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-12 col-md-8">
                <div class="form-group">
                    <div><label>Observacao</label></div>
                    <textarea name="observacao" class="form-control">Entrada do cliente no parque...</textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <span class="text-danger col-md-12" style="font-size: 13px;">Os campos com ' * ' são obrigatórios</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <button type="button" id="btnSalvar" class="btn btn-parque ml-3 btn" data-toggle="modal" data-target="#modal" disabled><i class="material-icons icone">save</i> Salvar</button>
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
                                <h3>
                                    <span class="material-icons icone text-success" style="font-size: 3em !important;">contact_support</span> 
                                    <div>Valor a ser Pago<br><b id="valorCreditoCliente" style="font-size: 1.5em;"></b><br>confirma ?</div>
                                </h3>
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
    var valorCaucao = document.getElementById('valorCartao');
    var valorPago = document.getElementById('valorPago');
    var valorCredito = document.getElementById('valorCreditoCliente');
    var creditoCartao = document.getElementById('creditoCartao');
    var btSalvar = document.getElementById('btnSalvar');

    valorCredito.textContent = 'R$ '+valorPago.value;

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
    })

    


    function changeValor(valor) {
        if(valor > 0){
            valorCredito.textContent = 'R$ ' + valor;
            creditoCartao.value = (valor - valorCaucao.value);
            //btSalvar.disabled = false;
        }
        else {
            //btSalvar.disabled = true;
            valorCredito.textContent = '0.00';
            creditoCartao.value = '0.00';
        }
    }

    function fMasc(objeto,mascara) {
        obj=objeto
        masc=mascara
        setTimeout("fMascEx()",1)
    }
    function fMascEx() {
        obj.value=masc(obj.value)
    }
    /*
    function mTel(tel) {
        tel=tel.replace(/\D/g,"")
        tel=tel.replace(/^(\d)/,"($1")
        tel=tel.replace(/(.{3})(\d)/,"$1)$2")
        if(tel.length == 9) {
            tel=tel.replace(/(.{1})$/,"-$1")
        } else if (tel.length == 10) {
            tel=tel.replace(/(.{2})$/,"-$1")
        } else if (tel.length == 11) {
            tel=tel.replace(/(.{3})$/,"-$1")
        } else if (tel.length == 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        } else if (tel.length > 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        }
        return tel;
    }
    */
    function mCPF(cpf){
        cpf=cpf.replace(/\D/g,"")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
        return cpf
    }
</script>
@endsection
