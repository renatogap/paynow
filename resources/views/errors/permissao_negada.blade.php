@extends('layouts.default2')
@section('conteudo')
    <section class="container mt-5">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="page-header"><i class="material-icons" style="color:red;font-size: 0.8em">block</i> Acesso Negado</h1>
        </div>
        <p class="lead">{{$oUsuario->nome}}, seu usuário não possui permissão para acessar o recurso solicitado.</p>
        <p class="lead">Não é {{$oUsuario->nome}}? <a href="{{url('seguranca/usuario/logout')}}">Clique aqui</a></p>
    </section>
@endsection
