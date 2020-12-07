@extends('layouts.default')
@section('conteudo')
    <h5>
        Gerenciador de Estoque
        <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>
    </h5>
    <hr>

    <form id="formEstoque" method="post" action="{{ url('estoque/store') }}" autocomplete="off">
        {{ @csrf_field() }}

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

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="col-md-12">PDV</label>
                    <div class="col-md-12">
                        <select name="tipo_cardapio" id="tipo_cardapio" class="form-control" onchange="changeTipoCardapio(this.value)">
                            @if($id_tipo_cardapio)
                                <option value="TODOS">VER TODOS...</option>
                            @else
                                <option value="">SELECIONE...</option>
                            @endif
                            @foreach($comboTipo as $c)
                            <option {{ $id_tipo_cardapio == $c->id ? 'selected' : '' }} value="{{$c->id}}">{{$c->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 col-6">
                <div class="form-group">
                    <label for="" class="col-md-12">Tipo de Movimento</label>
                    <div class="col-md-12">
                        <select name="tipoMovimento" id="tipoMovimento" class="form-control" onchange="changeTipoMovimento(this.value)">
                            <option value="E">ENTRADA</option>
                            <option value="S">SAIDA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!--<div id="div-pdv-destino" class="col-md-6 mb-3 d-none">
                <div class="form-group">
                    <label for="" class="col-md-12">PDV de Destino</label>
                    <div class="col-md-12">
                        <select name="pdv_destino" id="pdv_destino" class="form-control float-left">
                            <option value="">SELECIONE...</option>
                            @foreach($comboPDVsDestino as $c)
                            <option value="{{$c->id}}">{{$c->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>-->

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="" class="col-md-12">Produto</label>
                    <div class="col-md-12">
                        <select name="item_cardapio" class="form-control float-left" onchange="getDadosEstoque(this.value)">
                            @if($itensCardapio)
                                <option value="">SELECIONE...</option>
                            @else
                                <option value="">SELECIONE O PDV PRIMEIRO...</option>
                            @endif

                            @if($itensCardapio)
                                @foreach($itensCardapio as $categoria => $itensDaCategoria)
                                <optgroup label="{{ $categoria }}">
                                    @foreach($itensDaCategoria as $id => $produto)
                                        <option {{ (old('item_cardapio') && old('item_cardapio') == $id ? 'selected' : '')  }} value="{{ $id }}">{{ $produto }}</option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="form-group">
                    <label class="col-md-12">Quantidade *</label>
                    <div class="col-md-12">
                        <input type="tel" name="quantidade" class="form-control" value="{{old('quantidade')}}" required>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="form-group">
                    <label class="col-md-12">Quantidade atual</label>
                    <div class="col-md-12">
                        <input type="tel" name="qtd_atual" id="qtd_atual" class="form-control" value="{{old('qtd_atual')}}" readOnly>
                    </div>
                </div>
            </div>
            


            <div id="div-unidade-medida" class="col-md-3 col-6">
                <div class="form-group">
                    <label for="" class="col-md-12">Unidade medida</label>
                    <div class="col-md-12">
                        <select name="tipoUnidadeMedida" class="form-control" onchange="changeTipoUnidadeMedida(this.value)">
                            @foreach($tipoUnidadeMedida as $t)
                                <option {{ (old('tipoUnidadeMedida') && old('tipoUnidadeMedida') == $c->id ? 'selected' : '')  }} value="{{$t->id}}">{{$t->nome}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div id="div-tipo-dose" class="col-md-3 col-6 d-none">
                <div class="form-group">
                    <label class="col-md-12">Doses por Garrafa</label>
                    <div class="col-md-12">
                        <input type="tel" name="qtdDosePorGarrafa" id="qtdDosePorGarrafa" class="form-control" value="{{old('qtdDosePorGarrafa')}}">
                    </div>
                </div>
            </div>
            <div id="div-valor" class="col-md-3 col-6">
                <div class="form-group">
                    <label for="" class="col-md-12">Valor Unitário</label>
                    <div class="col-md-12">
                        <input type="tel" name="valor" id="valor" class="form-control" value="{{old('valor')}}">
                    </div>
                </div>
            </div>
        </div>






        <div class="row">
            <div class="col-md-3 col-6 div-estoque-min-max">
                <div class="form-group">
                    <label class="col-md-12">Estoque mínimo</label>
                    <div class="col-md-12">
                        <input type="tel" name="estoque_minimo" id="estoque_minimo" class="form-control" value="{{old('estoque_minimo')}}">
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 div-estoque-min-max">
                <div class="form-group">
                    <label class="col-md-12">Estoque máximo</label>
                    <div class="col-md-12">
                        <input type="tel" name="estoque_maximo" id="estoque_maximo" class="form-control" value="{{old('estoque_maximo')}}">
                    </div>
                </div>
            </div>
            
        </div>  

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="" class="col-md-12">Observação</label>
                    <div class="col-md-12">
                        <textarea name="observacao" id="observacao" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>      
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <button type="submit" class="btn btn-parque ml-3 btn">Salvar</button>
                    <a href="{{url('cardapio')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>

        @if($id_tipo_cardapio)
            <table class="table table-striped table-sm">
            <thead>
                <tr style="background: green; color: white;">
                    <th width="">Produto</th>
                    <th width="10%">Qtd Atual</th>
                    <th style="text-align: center;" width="10%">E.Min/ E.Max</th>
                    <th width="10%">Última atualização</th>
                </tr>
            </thead>
            <body>
                
                <hr>
                <h5>Estoque atual do <span id="nomePDV"></span></h5>
                @if($itensEstoquePDV->count() > 0)
                    @foreach($itensEstoquePDV as $e)
                    <tr>
                        <td class="align-middle">{{ $e->nome_item }}</td>
                        <td class="align-middle"><b>{{ $e->qtd_atual }}</b> {{ "({$e->unidade_medida})" }}</td>
                        <td class="align-middle" align="center">{{ $e->estoque_minimo }}/{{ $e->estoque_maximo }}</td>
                        <td class="align-middle">{{ date('d/m/y H:i', strtotime($e->dt_ultima_atualizacao)) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" class="text-center">
                            Nenhum registro encontrado.
                        </td>
                    </tr>
                @endif 
            </body>
            </table>
        @endif
    </form>
@endsection

@section('scripts')
<script>
    var form = document.getElementById('formEstoque');
    var tipoCardapio = document.getElementById('tipo_cardapio');
    var divPdvDestino = document.getElementById('div-pdv-destino');
    var qtd = document.getElementById('qtd_atual');
    var estoque_minimo = document.getElementById('estoque_minimo');
    var estoque_maximo = document.getElementById('estoque_maximo');
    var divTipoDose = document.getElementById('div-tipo-dose');
    var qtdDosePorGarrafa = document.getElementById('qtdDosePorGarrafa');
    var divUnidadeMedida = document.getElementById('div-unidade-medida');
    var divValor = document.getElementById('div-valor');
    var valor = document.getElementById('valor');
    
    if(tipoCardapio.value) {
        document.getElementById('nomePDV').innerHTML = '<b>'+tipoCardapio.options[tipoCardapio.selectedIndex].textContent+'</b>';
    }

    function getDadosEstoque(id_item) {
        $.ajax({
            type: 'get',
            url: BASE_URL+'estoque/get-estoque-item/'+id_item,
            dataType: 'json',
            success: function(resp) {
                estoque_minimo.value = resp.estoque_minimo ? resp.estoque_minimo : '';
                estoque_maximo.value = resp.estoque_maximo ? resp.estoque_maximo : '';
                if(resp.qtd_atual){
                    qtd.value = resp.qtd_atual;
                }else {
                    qtd.value = 0;
                }
            }
        })
    }

    function changeTipoCardapio(value){
        if(value == 'TODOS') {
            window.location=BASE_URL+'estoque';
        }else {
            window.location='?id_tipo_cardapio='+value;
        }
    }

    function changeTipoUnidadeMedida(value) {
        qtdDosePorGarrafa.value = '';

        if(value==2){
            divTipoDose.classList.remove('d-none');
        }else {
            divTipoDose.classList.add('d-none');
        }
    }

    function changeTipoMovimento(value) {
        form.reset();

        form.tipoMovimento.value = value;

        if(value == 'S') {            
            $('.div-estoque-min-max').addClass('d-none');
            divUnidadeMedida.classList.add('d-none');
            divValor.classList.add('d-none');
        }        
        else {
            $('.div-estoque-min-max').removeClass('d-none');
            divUnidadeMedida.classList.remove('d-none');
            divValor.classList.remove('d-none');
        }
    }
</script>

@endsection