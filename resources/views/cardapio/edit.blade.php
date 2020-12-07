@extends('layouts.default')


@section('conteudo')

    <h5>Editar Item

        <span class="badge {{ $cardapio->status == 1 ? 'badge-success' : 'badge-danger' }} text-light float-right">
            {{ $cardapio->status == 1 ? 'Ativo' : 'Inativo' }}
        </span>
    </h5>
    <hr>

    <form method="post" action="{{ url('cardapio/store') }}" enctype="multipart/form-data">
        {{ @csrf_field() }}

        <input type="hidden" name="id" id="id" value="{{$cardapio->id}}">

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
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Tipo de Cardápio</label>
                    <div class="col-md-12">
                        <select name="tipo" class="form-control float-left" onchange="changeTipoCardapio(this.value)" style="width: 86%;">
                            @if($id_tipo_cardapio)
                                <option value="TODOS">VER TODOS...</option>
                            @else
                                <option value="">SELECIONE...</option>
                            @endif
                            
                            @foreach($comboTipo as $c)
                            <option {{ ($id_tipo_cardapio == $c->id ? 'selected' : ($cardapio->fk_tipo_cardapio == $c->id ? 'selected' : '')) }} value="{{$c->id}}">{{$c->nome}}</option>
                            @endforeach                            
                        </select>
                        <a href="{{ url('cardapio/tipo-cardapio?action=edit/'.$cardapio->id).'&id='.$cardapio->id }}" class="btn btn-primary float-right">
                            +
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-none d-md-block">
                <div class="form-group">
                    <label for="" class="col-md-2">&nbsp;</label>
                    <div class="col-md-12">
                        <div style="width: 90%; height: 10em; position: absolute;">
                            <img src="{{url('cardapio/ver-foto/'.$cardapio->id)}}" width="80%" height="100%" onerror="this.src='<?= url('images/foto-error.png') ?>'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Categoria</label>
                    <div class="col-md-12">
                        <select name="categoria" class="form-control">
                        @if($comboCategoria)
                            <option value="">SELECIONE...</option>
                        @else
                            <option value="">SELECIONE O TIPO PRIMEIRO...</option>
                        @endif
                        
                        @if($comboCategoria)
                            @foreach($comboCategoria as $c)
                            <option {{ $cardapio->fk_categoria === $c->id ? 'selected' : '' }} value="{{$c->id}}">{{$c->nome}}</option>
                            @endforeach
                        @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="" class="col-md-12">Descrição do item *</label>
                    <div class="col-md-12">
                        <input type="text" name="nomeItem" class="form-control" value="{{$cardapio->nome_item}}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="" class="col-md-12">Detalhes do item</label>
                    <div class="col-md-12">
                        <input type="text" name="detalheItem" class="form-control" value="{{$cardapio->detalhe_item}}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="" class="col-md-12">Valor *</label>
                    <div class="col-md-12">
                        <input type="tel" name="valor" class="form-control" value="{{$cardapio->valor}}" maxlength="5">
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Nova Foto</label>
                    <div class="col-md-12">
                    <input type="file" name="foto[]" multiple accept="image/*" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <button type="submit" class="btn btn-parque ml-3 btn">Salvar</button>
                    @if($cardapio->status == 1)
                        <button type="button" class="btn btn-danger btn" onclick="inativarItemCardapio()">Inativar Item</button>
                    @else
                        <button type="button" class="btn btn-success btn" onclick="ativarItemCardapio()">Ativar Item</button>
                    @endif
                    <a href="{{url('cardapio')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    function changeTipoCardapio(value){
        if(value == 'TODOS') {
            window.location='edit/'+<?= $cardapio->fk_tipo_cardapio; ?>;
        }else {
            window.location="?id_tipo_cardapio="+value;
        }
    }

    function inativarItemCardapio() {
        if(confirm('Deseja realmente Inativar este item?')){
            $.ajax({
                type: 'POST',
                url: BASE_URL+'cardapio/inativar-item',
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

    function ativarItemCardapio() {
        if(confirm('Deseja realmente ativar este item?')){
            $.ajax({
                type: 'POST',
                url: BASE_URL+'cardapio/ativar-item',
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
</script>


@endsection