@extends('layouts.default')

@section('conteudo')
<h3>
    Pesquisar Perfis

    <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
        keyboard_backspace
    </a> 
</h3>

<form id="form" onsubmit="oController.pesquisar(event)" action="{{url('admin/perfil/grid')}}">
    {{ csrf_field()}}
    <div class="row">
        <div class="col-sm-5">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-sm-3 col-sm-offset-2">
                <button type="submit" id="pesquisar" class="btn btn-primary" onclick="oController.pesquisar(event)">Pesquisar</button>
            </div>
        </div>
    </div>

    <div class="row mt-3 justify-content-end mr-1">
        <div>
            <div class="btn-group btn-group-sm" role="group">
                <button id="novo" type="button" class="btn btn-secondary" onclick="oController.criar(this)" title="Criar novo" data-url="{{ url('') }}" data-toggle="tooltip" data-placement="top">
                    <i class="material-icons icone">add</i>
                </button>
                <button id="editar" type="button" class="btn btn-secondary" onclick="oController.editar(this)" title="alterar" data-url="{{ url('') }}" data-toggle="tooltip" data-placement="top">
                    <i class="material-icons icone">edit</i>
                </button>
                <button id="excluir" type="button" class="btn btn-secondary" onclick="oController.excluir(this)" title="excluir" data-url="{{ url('') }}" data-toggle="tooltip" data-placement="top">
                    <i class="material-icons icone">delete</i>
                </button>
            </div>
        </div>
    </div>

    <table id="grid" class="table table-striped table-bordered" width="100%">
        <thead>
            <tr>
                <th class="col-3">Nome</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</form>
@endsection

@section('scripts')
<script src="{{asset('js/jquery.min.js')}}"></script>
@include('layouts.datatables', ['carregamento_inicial' => true, 'colunas' => ['nome']])
<script src="{{asset('js/app/models/Ajax.js')}}"></script>
<script src="{{asset('js/app/controllers/PesquisaPerfilController.js')}}"></script>
<script>
    oController = new PesquisaPerfilController();
</script>
@endsection