@extends('layouts.default')
@section('conteudo')
<h5>
    Pesquisar Usuários

    <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
    keyboard_backspace
    </a> 
</h5>
<hr>

<form id="form" method="get" action="{{url('admin/usuario')}}">
    {{ csrf_field() }}
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="{{ $nome }}" class="form-control form-control-sm">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ $email }}" class="form-control form-control-sm">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-4 mr-5">
                <button type="submit" id="pesquisar" class="btn btn-primary">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="row mt-3 justify-content-end mr-1">
        <div class="btn-group btn-group-sm" role="group">
            <button id="novo" type="button" class="btn btn-secondary" onclick="oController.criar(this)" data-url="{{ url('') }}" title="Criar novo" data-toggle="tooltip" data-placement="top">
                <i class="material-icons icone">add</i>
            </button>
            <!--
            <button id="editar" type="button" class="btn btn-secondary" onclick="oController.editar(this)" data-url="{{ url('') }}" title="Edita" data-toggle="tooltip" data-placement="top">
                <i class="material-icons icone">edit</i>
            </button>
            <button id="excluir" type="button" class="btn btn-secondary" onclick="oController.excluir(this)" data-url="{{ url('') }}" title="Remove" data-toggle="tooltip" data-placement="top">
                <i class="material-icons icone">delete</i>
            </button>
            
            <button id="reativar" type="button" class="btn btn-secondary" onclick="oController.reativar(this)" data-url="{{ url('') }}" title="Reativa" data-toggle="tooltip" data-placement="top">
                <i class="material-icons icone">cached</i>
            </button>
            -->
            <a href="{{url('admin/perfil')}}" class="btn btn-primary">Tela de Perfil</a>
        </div>
    </div>
    <table id="grid" class="table table-striped table-bordered table-hover table-sm" width="100%">
        <thead>
            <tr>
                <th width="">Nome</th>
                <th width="">E-mail</th>
                <th></th>
            </tr> 

            </thead> 
                <tbody>
                    @foreach($usuarios as $user)
                    <tr>
                        <td>{{ $user->nome }}</td>
                        <td>{{ $user->email }}</td>
                        <td width="10%">
                            <a href="{{ url('admin/usuario/editar/'.$user->id) }}" class="btn btn-sm btn-primary" title="Edita" data-toggle="tooltip" data-placement="top">
                                <i class="material-icons icone">edit</i>
                            </a>
                            <button id="excluir" type="button" class="btn btn-sm btn-danger" onclick="oController.excluir(<?= $user->id ?>)" title="Remove" data-toggle="tooltip" data-placement="top">
                                <i class="material-icons icone">delete</i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
    </table>
</form>
@endsection
@section('scripts')
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/app/models/Ajax.js')}}"></script>
<script src="{{asset('js/app/controllers/AdminUsuarioController.js')}}"></script>
<!--@include('layouts.datatables', ['carregamento_inicial' => true, 'colunas' => ['nome', 'email']])-->
<script>
    oController = new AdminUsuarioController();
</script>
@endsection