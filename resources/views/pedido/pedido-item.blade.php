@extends('layouts.default')


@section('conteudo')
    <h4>
        Pedido
        <a href="#" class="float-right text-dark">
            R$ <span id="valorTotalTitulo">{{$cardapio->valor}}</span>
        </a>
    </h4>
    <hr>

    <form id="formPedido" method="post" action="{{ url('pedido/cardapio/add-pedido-cliente') }}">
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
                        <img src="{{url('cardapio/ver-foto/'.$cardapio->id)}}" onerror="this.src='<?= url('images/foto-error.png') ?>'" width="100%">
                    </div>
                </div>
            </div>
        @endif

        <h4 class="text-center">{{ $cardapio->categoria->nome }}: {{ $cardapio->nome_item }}</h4>
        <br>
        <!--
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div><label>Ponto de Venda</label></div>
                                {{$cardapio->tipo->nome}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        -->
        <div class="row">
        
            <div class="col-md-6">
                @if($cardapio->detalhe_item)
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div><label>Detalhes do item</label></div>
                            {{$cardapio->detalhe_item}}
                        </div>
                    </div>
                </div>
                @endif
                
                @if(in_array(1, $perfisUsuario) || in_array(4, $perfisUsuario))
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="mb-1"><label>Entregar agora?</label></div>
                                <input type="radio" name="entregue" onclick="mudarEntrega(this)" value="S"> <span style="font-size: 20px; font-weight: bold;">Sim</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="entregue" onclick="mudarEntrega(this)" onclick="" value="N" checked> <span style="font-size: 20px; font-weight: bold;">Não</span>
                            </div>
                        </div>
                    </div>
                    
                @endif
                
                <div class="row">
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="form-group">
                            <div><label>Quantidade</label></div>
                            <input type="text" name="quantidade" id="quantidade" class="form-control form-control-lg" value="{{ ($cardapio->unid==1 ? intval($cardapio->unid) : $cardapio->unid) }}" onkeyup="mudarValor(event)" required>
                            <div class="mt-1">
                                <a href="#" id="btnAdd" onclick="incrementa()" class="btn btn-success"><i class="material-icons">add</i></a> 
                                <a href="#" id="btnSub" onclick="decrementa()" class="btn btn-danger"><i class="material-icons">remove</i></a>
                            </div>
                        </div>
                            
                    </div>
                    
                    @if($cardapio->cozinha == 1)
                        <div id="divMesa" class="col-6 col-sm-6 col-md-6">
                            <div class="form-group">
                                <div><label>Mesa</label></div>
                                <input type="number" name="mesa" id="mesa" class="form-control form-control-lg" value="{{ $mesa ?? '' }}" required>
                            </div>
                        </div>
                    @endif
                </div>

                <div id="divObservacao" class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div><label>Observação do cliente</label></div>
                            <textarea name="observacao" id="observacao" class="form-control" rows="2">@if(session('observacao')) {{ session('observacao') }} @endif</textarea>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6 d-none d-md-block">
                <div style="width: 100%; height: 17.5em; padding: 1em;">
                    <img src="{{url('cardapio/ver-foto/'.$cardapio->id)}}" width="100%" height="100%" onerror="this.src='<?= url('images/foto-error.png') ?>'" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
                </div>
            </div>
            
        </div>
        
        <div class="row">
            <div class="col-7 col-md-6">
                <button type="submit" onclick="aguarde(this)" class="btn btn-parque btn-block" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Adicionar pedido</button>
            </div>
            <div class="col-5 col-md-6">
                <a href="{{url('pedido/cardapio/'.$cardapio->fk_tipo_cardapio)}}" class="btn btn-secondary btn-block" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Cancelar</a>
            </div>
        </div>
    </form>
@endsection


@section('scripts')
<script>
    var mesa = document.getElementById('mesa');
    var divMesa = document.getElementById('divMesa');
    var divObservacao = document.getElementById('divObservacao');
    var unidade = document.getElementById('unidade').value;
    var btnAdd = document.getElementById('btnAdd');
    var btnSub = document.getElementById('btnSub');

    if(unidade != 1){
        btnAdd.classList.add('d-none');
        btnSub.classList.add('d-none');
    }

    function mudarEntrega(e) {
        if(e.value == 'S'){
            mesa.required = false;
            divMesa.classList.add('d-none');
            divObservacao.classList.add('d-none');
        }else {
            mesa.required = true;
            divMesa.classList.remove('d-none');
            divObservacao.classList.remove('d-none');
        }
    }


    function incrementa() {
        var qtd = document.getElementById('quantidade');
        if(qtd.value > 0) {
            qtd.value = (parseInt(qtd.value) + 1);
            mudarValor();
        }else {
            qtd.value = (unidade==1 ? parseInt(unidade) : unidade);
        }
        
    }

    function decrementa() {
        var qtd = document.getElementById('quantidade');
        if(qtd.value > 1) {
            qtd.value = (parseInt(qtd.value) - 1);
            mudarValor();
        }else {
            qtd.value = (unidade==1 ? parseInt(unidade) : unidade);
        }
    }

    function mudarValor(event) {
        //var regra = /^[0-9]+$/;

        var qtd = document.getElementById('quantidade').value;        
        var valorCardapio = document.getElementById('valorCardapio');
        var valor = document.getElementById('valor');
        var valorTitulo = document.getElementById('valorTotalTitulo');

        if(unidade == 1) {
            //if (qtd.match(regra) && qtd > 0) {
            if (qtd > 0) {
                var total = (qtd * valorCardapio.value);

                valor.value = round(total, 2); //arredonda para 2 casas

                var aValor = valor.value.split('.');

                if(aValor.length > 1) {

                    if(aValor[1].length == 1) {
                        aValor[1] = aValor[1]+'0';
                    }

                    //console.log('Valor real: R$ ' + aValor[0]+','+aValor[1]);

                    if(parseInt(aValor[1]) >= 51){
                        total = (parseInt(aValor[0]) + 1);
                    }else {
                        total = parseInt(aValor[0]);
                    }
                }

                valor.value = round(total, 2); //arredonda para 2 casas

                console.log('Valor arredondado: R$ '+ valor.value);

                valorTitulo.textContent = round(total, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});
            }else {
                valor.value = 0;
                valorTitulo.textContent = round(0, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});
            }
        }else {
            if (qtd > 0) {
                var total = (qtd * valorCardapio.value / unidade);

                valor.value = round(total, 2); //arredonda para 2 casas

                var aValor = valor.value.split('.');

                if(aValor.length > 1) {

                    if(aValor[1].length == 1) {
                        aValor[1] = aValor[1]+'0';
                    }

                    //console.log('Valor real: R$ ' + aValor[0]+','+aValor[1]);

                    if(parseInt(aValor[1]) >= 51){
                        total = (parseInt(aValor[0]) + 1);
                    }else {
                        total = parseInt(aValor[0]);
                    }
                }


                valor.value = round(total, 2); //arredonda para 2 casas

                console.log('Valor arredondado: R$ ' + valor.value);

                valorTitulo.textContent = round(total, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});

                //console.log(qtd+' * '+valorCardapio.value+' / '+unidade, total);
            }
            else {
                valor.value = 0;
                valorTitulo.textContent = round(0, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});
            }
        }
    }

    function aguarde(e) {
        if(mesa.value){
            e.disabled = false;
            e.textContent = 'Aguarde...';
            e.classList.remove('btn-parque');
            e.style.background = '#5ead99';
        }
        //$('#formPedido').submit();
    }

    /*function mudarValorChange() {
        var unidade = document.getElementById('unidade').value;
        var qtd = document.getElementById('quantidade').value;
        var valorCardapio = document.getElementById('valorCardapio');
        var valor = document.getElementById('valor');
        var valorTitulo = document.getElementById('valorTotalTitulo');
        
        if(unidade == 1) {
            if (qtd > 0) {
                var total = (qtd * valorCardapio.value);
                valor.value = round(total, 2);
                valorTitulo.textContent = round(total, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});
            }
        }else {            
            var total = (qtd * valorCardapio.value / unidade);
            valor.value = round(total, 2);
            valorTitulo.textContent = round(total, 2).toLocaleString('pt-br', {minimumFractionDigits: 2});

            console.log(qtd+' * '+valorCardapio.value+' / '+unidade, total.toLocaleString('pt-br', {minimumFractionDigits: 2}));
        }


    }*/



    function round(num, places) {
        if (!("" + num).includes("e")) {
            return +(Math.round(num + "e+" + places)  + "e-" + places);
        } else {
            let arr = ("" + num).split("e");
            let sig = ""
            if (+arr[1] + places > 0) {
                sig = "+";
            }

            alert(+(Math.round(+arr[0] + "e" + sig + (+arr[1] + places)) + "e-" + places));

            return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + places)) + "e-" + places);
        }
    }


</script>
@endsection
