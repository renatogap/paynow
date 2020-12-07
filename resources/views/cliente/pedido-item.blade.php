@extends('layouts.default')


@section('conteudo')
    <h4>
        Pedido
        <a href="#" class="float-right text-dark">
            R$ <span id="valorTotalTitulo">{{$cardapio->valor}}</span>
        </a>
    </h4>
    <hr>

    <form id="formPedido" method="post" action="{{ url('cliente/cardapio/add-pedido-cliente') }}">
        {{ @csrf_field() }}

        <input type="hidden" name="id_cardapio" value="{{$cardapio->id}}">
        <input type="hidden" name="id_tipo_cardapio" value="{{$cardapio->fk_tipo_cardapio}}">
        <input type="hidden" name="valor" id="valor" value="{{ $cardapio->valor }}">
        <input type="hidden" name="valorCardapio" id="valorCardapio" value="{{ $cardapio->valor }}">
        <input type="hidden" name="unidade" id="unidade" value="{{ $cardapio->unid }}">

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


        @if($fotoCardapio)
            <div class="col-md-12 d-block d-md-none">
                <div class="form-group">
                    <div class="col-md-12" style="text-align: center;">
                        <img src="{{url('cliente/cardapio/ver-foto/'.$cardapio->id)}}" onerror="this.src='<?= url('images/foto-error.png') ?>'" width="100%">
                    </div>
                </div>
            </div>
        @endif

        <h4 class="text-center">{{ $cardapio->nome_item }}</h4>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="col-md-12">Ponto de Venda</label>
                            <div class="col-md-12">
                                {{$cardapio->tipo->nome}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="col-md-12">Detalhes do item</label>
                            <div class="col-md-12">
                                {{$cardapio->detalhe_item}}
                            </div>
                        </div>
                    </div>
                </div>

                <!--
                <div class="row">
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label for="" class="col-md-12">Quantidade</label>
                            <div class="col-md-12">
                            <input type="number" name="quantidade" id="quantidade" class="form-control" value="1" onkeyup="mudarValor(event)" onchange="mudarValorChange()" required>
                            <div class="mt-1">
                                <a href="#" onclick="incrementa()" class="btn btn-success"><i class="material-icons">add</i></a> 
                                <a href="#" onclick="decrementa()" class="btn btn-danger"><i class="material-icons">remove</i></a>
                            </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <label for="" class="col-md-12">Mesa</label>
                            <div class="col-md-12">
                            <input type="number" name="mesa" id="mesa" class="form-control" value="{{ $mesa ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="col-md-12">Observação do cliente</label>
                            <div class="col-md-12">
                            <textarea name="observacao" class="form-control" rows="3">@if(session('observacao')) {{ session('observacao') }} @endif</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                -->
            </div>
            <div class="col-md-6 d-none d-md-block">
                <div style="width: 100%; height: 19em; background: #333; padding: 1em;">
                    <img src="{{url('cliente/cardapio/ver-foto/'.$cardapio->id)}}" width="100%" height="100%" onerror="this.src='<?= url('images/foto-error.png') ?>'">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <!--
                        <button type="submit" onclick="validaMesa()" class="btn btn-parque ml-3" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Adicionar pedido</button>
                    -->
                    <a href="{{url('cliente/cardapio/'.$cardapio->fk_tipo_cardapio)}}" class="btn btn-secondary" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Voltar</a>
                </div>
            </div>
        </div>
    </form>
@endsection


@section('scripts')
<script>
    function validaMesa() {
        if(!document.getElementById('mesa').value) {
            alert('Informe o número da mesa'); return false;
        }
    }

    function incrementa() {
        var qtd = document.getElementById('quantidade');
        qtd.value = (parseInt(qtd.value) + 1);
        mudarValorChange();
    }

    function decrementa() {
        var qtd = document.getElementById('quantidade');
        qtd.value = (parseInt(qtd.value) - 1);
        mudarValorChange();
    }

    function mudarValor(event) {
        var regra = /^[0-9]+$/;

        var qtd = document.getElementById('quantidade').value;
        var unidade = document.getElementById('unidade').value;
        var valorCardapio = document.getElementById('valorCardapio');
        var valor = document.getElementById('valor');
        var valorTitulo = document.getElementById('valorTotalTitulo');

        if(unidade == 1) {
            if (qtd.match(regra) && qtd > 0) {
                var total = (qtd * valorCardapio.value);
                valor.value = total;
                valorTitulo.textContent = total.toLocaleString('pt-br', {minimumFractionDigits: 2});
            }
        }else {
            
            var total = (qtd * valorCardapio.value / unidade);
            valor.value = total;
            valorTitulo.textContent = total.toLocaleString('pt-br', {minimumFractionDigits: 2});

            console.log(qtd+' * '+valorCardapio.value+' / '+unidade, total);
        }
    }

    function mudarValorChange() {
        var unidade = document.getElementById('unidade').value;
        var qtd = document.getElementById('quantidade').value;
        var valorCardapio = document.getElementById('valorCardapio');
        var valor = document.getElementById('valor');
        var valorTitulo = document.getElementById('valorTotalTitulo');
        
        if(unidade == 1) {
            if (qtd > 0) {
                var total = (qtd * valorCardapio.value);
                valor.value = total;
                valorTitulo.textContent = total.toLocaleString('pt-br', {minimumFractionDigits: 2});
            }
        }else {            
            var total = (qtd * valorCardapio.value / unidade);
            valor.value = total;
            valorTitulo.textContent = total.toLocaleString('pt-br', {minimumFractionDigits: 2});

            console.log(qtd+' * '+valorCardapio.value+' / '+unidade, total.toLocaleString('pt-br', {minimumFractionDigits: 2}));
        }


    }

</script>

@endsection