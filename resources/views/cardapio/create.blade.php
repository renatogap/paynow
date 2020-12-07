@extends('layouts.default')


@section('conteudo')
    <h5>Gerenciar Cardápio
        <a href="{{url('cardapio')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>
    </h5>
    <hr>

    <form method="post" action="{{ url('cardapio/store') }}" enctype="multipart/form-data">
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

        <div class="row mb-2">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Nome do Cardápio</label>
                    <div class="col-md-12">
                        <select name="tipo" class="form-control float-left" onchange="changeTipoCardapio(this.value)" style="width: 86%;">
                            @if($id_tipo_cardapio)
                                <option value="TODOS">VER TODOS...</option>
                            @else
                                <option value="">SELECIONE...</option>
                            @endif
                            @foreach($comboTipo as $c)
                            <option {{ $id_tipo_cardapio == $c->id ? 'selected' : '' }} value="{{$c->id}}">{{$c->nome}}</option>
                            @endforeach
                        </select>
                        <a href="{{ url('cardapio/tipo-cardapio?action=create') }}" class="btn btn-primary float-right">
                            <span class="material-icons icone" style="font-size: 1.1em !important;">
                                add_business
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Categoria</label>
                    <div class="col-md-12">
                        <select name="categoria" class="form-control float-left" style="width: 86%;">
                            @if($comboCategoria)
                                <option value="">SELECIONE...</option>
                            @else
                                <option value="">SELECIONE O TIPO PRIMEIRO...</option>
                            @endif

                            @if($comboCategoria)
                                @foreach($comboCategoria as $c)
                                <option value="{{$c->id}}">{{$c->nome}}</option>
                                @endforeach
                            @endif
                        </select>
                        <a href="#" class="btn btn-primary float-right" data-toggle="modal" data-target="#modal" onclick="limparModal()">
                            <span class="material-icons icone" style="font-size: 1.1em !important;">
                                more_horiz
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="" class="col-md-12">Descrição do item *</label>
                    <div class="col-md-12">
                        <input type="text" name="nomeItem" class="form-control" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="" class="col-md-12">Detalhes do item</label>
                    <div class="col-md-12">
                        <input type="text" name="detalheItem" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="" class="col-md-12">Valor *</label>
                    <div class="col-md-12">
                        <input type="tel" name="valor" class="form-control" maxlength="11">
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="" class="col-md-12">Foto</label>
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
                    <a href="{{url('cardapio')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </div>
    </form>


    <!-- Modal -->
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Cadastrar Categoria
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modalCategoria" action="{{ url('cardapio/salvar-categoria') }}" onsubmit="salvarModal(event, this)">
                        {{ @csrf_field() }}
                        
                        <div id="error" class="alert alert-danger d-none">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <span id="msgError">{!! session('error') !!}</span>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-md-12">Tipo de Cardápio</label>
                                    <div class="col-md-12">
                                        <select name="tipo" id="tipo" class="form-control" required>
                                            @foreach($comboTipo as $c)
                                            <option value="{{$c->id}}">{{$c->nome}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-md-12">Categoria</label>
                                    <div class="col-md-12">
                                        <input type="text" name="categoria" id="categoria" class="form-control" placeholder="Ex: Pratos Executivos" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-parque">Salvar</button>
                                        <button class="btn btn-secondary" data-dismiss="modal" onclick="limparModal()">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var tipo = document.getElementById('tipo');
    var categoria = document.getElementById('categoria');



    function limparModal() {
        categoria.value = '';
    }

    function changeTipoCardapio(value){
        if(value == 'TODOS') {
            window.location='create';
        }else {
            window.location='create?id_tipo_cardapio='+value;
        }
    }

    function salvarModalTipoCardapio(event, e) {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: e.action,
            data: {
                _token: document.getElementsByName('_token')[0].value,
                tipo: tipo.value
            },
            dataType: 'JSON',
            success: function(resp) {
                alert(resp.message);

                window.location.reload();
            },
            error: function (request) {
                var error = document.getElementById('error');
                var msgError = document.getElementById('msgError');
                msgError.innerHTML = request.responseJSON.message;
                error.classList.remove('d-none');
                //alert(request.responseJSON.message);
            }
        })
    }

    function salvarModal(event, e) {
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: e.action,
            data: {
                _token: document.getElementsByName('_token')[0].value,
                tipo: tipo.value,
                categoria: categoria.value
            },
            dataType: 'JSON',
            success: function(resp) {
                alert(resp.message);

                window.location.reload();
            },
            error: function (request) {
                var error = document.getElementById('error');
                var msgError = document.getElementById('msgError');
                msgError.innerHTML = request.responseJSON.message;
                error.classList.remove('d-none');
                //alert(request.responseJSON.message);
            }
        })
    }
</script>

@endsection