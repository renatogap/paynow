@extends('layouts.default')

@section('conteudo')

<h3>
    Editar Perfil

    <a href="{{url('admin/perfil')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
        keyboard_backspace
    </a> 
</h3>
<hr>
<form id="form" class="form-horizontal" action="{{url('admin/perfil/update')}}" method="POST" onsubmit="oController.salvar(event)">
    {{csrf_field()}}
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label for="nome">Nome do perfil:</label>
                <input type="text" id="nome" name="nome" class="form-control form-control-sm" required value="{{$oPerfil->nome}}">
            </div>
            <br>
        </div>
    </div>
    <legend>Permissões</legend>
    <div id="conteudo_grid">
        <table id="grid" class="table table-bordered table-hover table-sm" width="100%">
            <thead>
                <tr>
                    <th>
                        <select name="grupo" id="grupo" class="form-control form-control-sm" onchange="oController.filtrarGrupo(this)">
                            <option value="">Todos os grupos</option>
                            @foreach($aAcao as $grupo => $acao)
                            <option value="{{simplificarString($grupo)}}">{{$grupo}}</option>
                            @endforeach
                        </select>
                    </th>
                    <th class="flex-column">permissão</th>
                    <th>descrição</th>
                </tr>
            </thead>
            <tbody>
                @foreach($aAcao as $grupo => $acao)
                <tr class="{{simplificarString($grupo)}}">
                    <td class="grupo" rowspan="{{count($acao) +1}}" style="vertical-align: middle; background: whitesmoke;">{{$grupo}}</td>
                </tr>
                @foreach($acao as $valor)
                <tr class="{{simplificarString($grupo)}}">
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{ $valor->id }}" name="acao[]" value="{{ $valor->id }}" {{ in_array($valor->id, $aPermissoes) ? 'checked' : ''}}>
                            <label class="form-check-label" for="{{ $valor->id }}">
                                {{$valor->nome_amigavel}}
                            </label>
                        </div>
                    </td>
                    <td>{{$valor->descricao}}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="background: white"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>

    <div id="erro_form" class="alert alert-success fade show {{ isset($alerta) ? '' : 'd-none' }}" role="alert">
        <span id="msg">{{ $alerta ?? '' }}</span>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <button id="salvar" type="submit" class="btn btn-primary" data-url="{{url('admin/perfil/editar')}}">
                    Salvar
                </button>
                <a href="{{url('admin/perfil')}}" class="btn btn-secondary">Nova pesquisa</a>

            </div>
            <br>
        </div>
    </div>

    <input type="hidden" name="perfil" value="{{$oPerfil->id}}">
</form>
@endsection

@section('scripts')
<script src="{{asset('js/app/models/Ajax.js')}}"></script>
<script src="{{ asset('js/app/models/ListaErrors.js') }}"></script>
<script src="{{asset('js/app/controllers/PerfilController.js')}}"></script>
<script>
    oController = new PerfilController();

    @if($alerta)
    oController.exibirAlerta('{{$alerta}}');
    @endif
</script>
@endsection