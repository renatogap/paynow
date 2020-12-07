@extends('layouts.default')

@section('conteudo')
<style>
select[readonly] {
  background: #eee; /*Simular campo inativo - Sugestão @GabrielRodrigues*/
  pointer-events: none;
  touch-action: none;
}
</style>
    <h4>
        Cartão do Cliente
            
        <a href="{{url('cartao-cliente')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>    
    </h4>
    <span class="badge {{ ($entrada->status==1 ? 'badge-info' : ($entrada->status==2 ? 'badge-success' :  'badge-danger')) }}" style="font-size: 14px;">
    {{ ($entrada->status==1 ? 'DEVOLVIDO' : ($entrada->status==2 ? 'EM USO' : ($entrada->status==3 ? 'BLOQUEADO' : 'PERDIDO'))) }}
    </span>
    <hr>

    <form method="post" action="{{ url('cartao-cliente/store') }}">
        {{ @csrf_field() }}

        <input type="hidden" name="id" id="id" value="{{$entrada->id}}">
        <input type="hidden" name="tipo" value="1">
        <input type="hidden" name="hash" value="{{$cartao->hash}}">

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
            <!--
            <div class="col-6 col-md-6">
                <div class="form-group">
                    <label for="" class="col-md-12">Tipo Cliente</label>
                    <div class="col-md-12">
                        <select name="tipo" class="form-control" rerquired>
                            @foreach($tipo as $v)
                            <option {{ ($entrada->fk_tipo_cliente == $v->id ? 'selected' : '') }} value="{{$v->id}}">{{$v->nome}}</option>
                            @endforeach
                            
                        </select>
                    </div>
                </div>
            </div>
            -->
            
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <div><label>Data que o Cliente recebeu o Cartão</label></div>
                    <input type="text" name="data" value="{{date('d/m/Y H:i', strtotime($entrada->created_at))}}"  class="form-control" readonly>                        
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <div><label>Nome *</label></div>
                    <input type="text" name="nome" class="form-control" value="{{$entrada->nome}}" required>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>CPF</label></div>
                    <input type="tel" name="cpf" class="form-control" value="{{$entrada->cpf}}"  maxlength="14" onkeydown="fMasc(this, mCPF);">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Telefone</label></div>
                        <input type="tel" name="telefone" class="form-control" value="{{$entrada->telefone}}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Saldo disponível *</label></div>
                    <input type="text" name="valor" class="form-control" value="{{$entrada->valor_atual}}" disabled>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Tipo de pagamento *</label></div>
                    <select name="tipo_pagamento" class="form-control" required>
                        @foreach($formaPagamento as $p)
                            <option {{ $entrada->fk_tipo_pagamento == $p->id ? 'selected' : '' }} value="{{ $p->id }}">{{ $p->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-4">
                <div class="form-group">
                    <div><label>Valor da Caução *</label></div>
                    <input type="tel" name="valorCartao" class="form-control" required readonly value="{{$entrada->valor_cartao}}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="form-group">
                    <div><label>Observacao</label></div>
                    <textarea name="observacao" class="form-control">{{$entrada->observacao}}</textarea>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">

                @if($entrada->status != 1)
                    <button type="submit" class="btn btn-parque ml-3 btn">
                        <!--<i class="material-icons">save</i>--> Salvar
                    </button>
                    
                    @if($entrada->status == 2)
                        <a href="#" data-toggle="modal" data-target="#modalDevolucao" class="btn btn-info btn">
                        <!--<i class="material-icons">how_to_vote</i>--> Devolução
                        </a>
                        
                        <a href="#" onclick="bloqueiaDesbloqueiaCartao(3, '{{$cartao->codigo}}')" class="btn btn-danger">
                            <i class="material-icons icone">lock</i> Bloquear
                        </a>      
                    @elseif($entrada->status == 3 && !$entrada->fk_cartao_transferido)
                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modal" onclick="lerQrCode()">
                                Transferir
                            </a>

                            <a href="#" onclick="bloqueiaDesbloqueiaCartao(2, '{{$cartao->codigo}}')" class="btn btn-success">
                                Desbloquear
                            </a>
                    @endif

                @endif

                @if($entrada->devolvido == 'S' && $entrada->valor_atual > 0 && in_array(6, $perfisUsuario))
                    <a href="#" onclick="zerarCartao()" class="btn btn-dark ml-3">
                        Zerar Cartão
                    </a>
                @endif
            </div>
        </div>

       

    </form>

    <!-- Modal -->
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="material-icons" style="font-size: 30px;">qr_code_scanner</span>  Aproxime o cartão
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <video id="preview" class="col-md-12"></video>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal Devolução -->
     <div id="modalDevolucao" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-weight: bold;">
                        Devolução
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""><b>Crédito do Cartão</b></label>
                                <input type="tel" name="valorCartao" id="valorCartao" value="{{$entrada->valor_atual}}" class="form-control form-control-lg" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""><b>Devolução do Caução</b></label>
                                <input type="tel" name="valorCaucao" id="valorCaucao" value="{{$entrada->valor_cartao}}" class="form-control form-control-lg" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""><b>O cartão foi devolvido para o Caixa ?</b></label>
                                <div>
                                    <input type="radio" name="rdCartaoDevolvido" id="rdCartaoDevolvido" value="S" onclick="isDevolveuCartao(this)" checked> Sim &nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="rdCartaoDevolvido" id="rdCartaoDevolvido" value="N" onclick="isDevolveuCartao(this)"> Não, o cliente levou
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for=""><b>Valor da Devolução</b></label>
                                <input type="tel" name="valorDevolvido" id="valorDevolvido" value="{{ number_format(($entrada->valor_atual + $entrada->valor_cartao),2) }}" class="form-control form-control-lg">
                                <br>
                                <div style="font-size: 13px">Limite máximo de devolução: <b class="text-danger">R$ {{ config('parque.limite_devolucao') }}.00</b></div>
                            </div>
                        </div>
                    </div>
                   

                    <div id="errorModalDevolucao" class="alert alert-danger d-none"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-parque" onclick="devolverCartao('{{$cartao->codigo}}')">Salvar</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    
    <!--<form id="form-cartao-delete" action="{{ url('admin/cartao/delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="id" value="{{ isset($cartao)? $cartao->id : '' }}">
    </form>
-->

@endsection

@section('scripts')
<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>
<script>
    var valorCartao = document.getElementById('valorCartao');
    var valorCaucao = document.getElementById('valorCaucao');
    var valorDevolvido = document.getElementById('valorDevolvido');
    

    function lerQrCode() {
        var scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });

        scanner.addListener('scan', function(content) {
            window.location = BASE_URL+'cartao-cliente/transferir-credito/'+content+'?id='+document.getElementById('id').value;
            //window.open(BASE_URL+'cartao/edit/'+content, "_blank");
        });

        Instascan.Camera.getCameras().then(cameras => 
        {
            if(cameras.length == 1){
                scanner.start(cameras[0]);
            }
            else if(cameras.length > 0){
                scanner.start(cameras[1]);
            }
            else {
                alert("There is no camera on the device!");
            }
        });
    }

    function isDevolveuCartao(e) {
        if(e.value == 'S'){
            valorCaucao.value = '{{ config("parque.valor_cartao") }}';
            valorDevolvido.value = (parseFloat(valorCartao.value) + parseFloat('{{ config("parque.valor_cartao") }}'));
        }else {
            valorCaucao.value = '0.00';
            valorDevolvido.value = valorCartao.value;
        }
    }

    function devolverCartao(codigo) {
        $.ajax({
            type: 'POST',
            url: BASE_URL+'cartao-cliente/devolver-cartao',
            data: {
                _token: document.getElementsByName('_token')[0].value,
                codigo: codigo,
                valorDevolvido: document.getElementById('valorDevolvido').value,
                rdCartaoDevolvido: $('input[name="rdCartaoDevolvido"]:checked')[0].value
            },
            dataType: 'json',
            async: false,
            success: function(resp) {
                window.location.reload();
            },
            error: function(error) {
                var msgerro = document.getElementById('errorModalDevolucao');
                msgerro.innerHTML = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+error.responseJSON.message;
                msgerro.classList.remove('d-none');
            }
        });          
    }

    function bloqueiaDesbloqueiaCartao(status, codigo) {
        if(confirm('Deseja realmente mudar o status deste cartão?')){
            $.ajax({
                type: 'POST',
                url: BASE_URL+'cartao-cliente/bloqueia-desbloqueia',
                data: {
                    _token: document.getElementsByName('_token')[0].value,
                    codigo: codigo,
                    status: status
                },
                dataType: 'json',
                success: function(resp) {
                    window.location.reload();
                }
            })            
        }
    }

    function zerarCartao() {
        if(confirm('Deseja realmente zerar este cartão?')){
            $.ajax({
                type: 'POST',
                url: BASE_URL+'cartao-cliente/zerar-cartao',
                data: {
                    _token: document.getElementsByName('_token')[0].value,
                    id: document.getElementById('id').value
                },
                dataType: 'json',
                success: function(resp) {
                    window.location.reload();
                }
            })            
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
        tel=tel.replace(/(.{})(\d)/,"$1)$2")
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