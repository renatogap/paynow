@extends('layouts.default')

@section('conteudo')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3">Atualizar Senha</h1>
        {{--                <div class="btn-toolbar mb-2 mb-md-0">--}}
        {{--                    <div class="btn-group mr-2">--}}
        {{--                        <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>--}}
        {{--                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>--}}
        {{--                    </div>--}}
        {{--                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">--}}
        {{--                        <span data-feather="calendar"></span>--}}
        {{--                        This week--}}
        {{--                    </button>--}}
        {{--                </div>--}}
    </div>
    <main class="container-fluid">
        <form id="formulario" action="{{ url('seguranca/usuario/atualizar-senha') }}" method="post"
              onsubmit="salvar(event)">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="senha_atual">Senha atual</label>
                    <input type="password" name="senha_atual" id="senha_atual" class="form-control" minlength="6"
                           required>
                    <small class="form-text text-muted">
                        Mínimo 6 caracteres
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="nova_senha">Nova senha</label>
                    <input type="password" name="nova_senha" id="nova_senha" class="form-control" required minlength="6">
                    <small class="form-text text-muted">
                        Mínimo 6 caracteres
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="nova_senha_confirmation">Confirmar senha</label>
                    <input type="password" name="nova_senha_confirmation" id="nova_senha_confirmation"
                           class="form-control" required minlength="6">
                </div>
            </div>

            {{--            <div class="alert alert-danger mt-3" v-if="exibirErro">--}}
            {{--                <ul>--}}
            {{--                    <li v-for="erro in erros" v-text="erro"></li>--}}
            {{--                </ul>--}}
            {{--            </div>--}}
            <br>

            <div id="msgErrors" class="alert alert-danger" style="display: none;"></div>

            <div class="row">
                <div class="col-md-3 mb-3 mt-3">
                    <button typeof="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </main>
@endsection

@section('scripts')
<!--
    <script>
        function salvar(e) {
            e.preventDefault();

            $.ajax({
                url: e.action,
                data: $('#formulario').serialize(),
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    alert(response.message);
                    window.location = `${BASE_URL}`;
                },
                error: function (response) {
                    console.log(response);

                    $('#msgErros').html(response.message).show();

                }
            });
        }        
    </script>
    -->
@endsection