@extends('layouts/default')

@section('conteudo')
<h3>
    <i class="material-icons icone">person</i> Cadastrar Usuário

    <a href="{{url('admin/usuario')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
    keyboard_backspace
    </a> 
</h3>
<hr>

{{ csrf_field() }}
@if(isset($oPreCadastro))
<form id="form" action="{{url('admin/solicitar-acesso/store')}}" class="form-horizontal" onsubmit="oController.salvar(event)">
    @else
    <form id="form" action="{{url('admin/usuario/store')}}" class="form-horizontal" onsubmit="oController.salvar(event)">
        @endif

        <div id="msg-info" class="alert alert-info d-none"></div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">Nome *</label>
                    <input class="form-control form-control-sm" id="nome" name="nome" required value="{{ isset($oPreCadastro) ? $oPreCadastro->nome : null }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" class="form-control form-control-sm" required value="{{ isset($oPreCadastro) ? $oPreCadastro->email : null }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 col-md-3">
                <div class="form-group">
                    <label for="senha">Senha
                        {{ !isset($oPreCadastro) ? '*' : null }}</label>
                    <input type="password" id="senha" name="senha" class="form-control form-control-sm" required>
                    @if(isset($oPreCadastro))
                    <span class="help-block">
                        Para manter a senha que o usuário já escolheu mantenha este campo em branco
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="form-group">
                    <label label for="senha_confirmation">Confirmar Senha
                        {{ !isset($oPreCadastro) ? '*' : null }}</label>
                    <input type="password" id="senha_confirmation" name="senha_confirmation" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dt_nascimento">Data de nascimento</label>
                    <input type="date" class="form-control form-control-sm" id="dt_nascimento" name="dt_nascimento" value="{{ isset($oPreCadastro) ? $oPreCadastro->nascimento : null }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <legend>Perfil(s)</legend>
                    <select id="perfil2" class="form-control form-control-sm">
                        <option value="">selecione</option>
                        @foreach($aPerfisCadastrados as $p)
                        <option value="{{$p->id}}">{{$p->nome}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <legend>Cardápio(s)</legend>
                    <select id="cardapio" class="form-control form-control-sm">
                        <option value="">selecione</option>
                        @foreach($aCardapio as $p)
                        <option value="{{$p->id}}">{{$p->nome}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="btn-group btn-group-sm" role="group">
                        <button title="atribuir perfil ao usuário" class="btn btn-secondary" type="button" class="btn btn-default" id="adicionar" onclick="oController.adicionarPerfil()">
                            <i class="material-icons icone">add</i>
                        </button>
                        <button title="remover perfil do usuário" class="btn btn-secondary" type="button" class="btn btn-default" id="remover" onclick="oController.removerPerfil()">
                            <i class="material-icons icone">delete</i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="btn-group btn-group-sm" role="group">
                        <button title="atribuir o Cardápio ao usuário" class="btn btn-secondary" type="button" class="btn btn-default" id="adicionar" onclick="oController.adicionarCardapio()">
                            <i class="material-icons icone">add</i>
                        </button>
                        <button title="remover o Cardápio do usuário" class="btn btn-secondary" type="button" class="btn btn-default" id="remover" onclick="oController.removerCardapio()">
                            <i class="material-icons icone">delete</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <div id="conteudo_grid">
                        <table id="grid" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Perfil</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <div id="conteudo_grid">
                        <table id="grid2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Cardápio</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>            
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="renovar_senha" name="renovar_senha" value="1">
                            Forçar troca de senha no próximo login
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    <div id="snackbar" role="alert">
                        <button type="button" class="close">
                            <span aria-hidden="true"></span></button>
                        <div id="msg"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    <button class="btn btn-primary">Salvar</button>
                    <a href="{{url('admin/usuario')}}" class="btn btn-secondary">Nova pesquisa</a>
                </div>
            </div>
        </div>
        {{csrf_field()}}
    </form>
</form>
@endsection

@section('scripts')
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/app/models/Ajax.js')}}"></script>
<script src="{{asset('js/app/controllers/CriarUsuarioController.js')}}"></script>
@include('layouts.datatables-simples', ['colunas' => ['perfil']])
@include('Seguranca::layouts.datatables-simples2', ['colunas' => ['cardapio'], 'id' => 'grid2'])
<script>
    oController = new CriarUsuarioController();
</script>
@endsection