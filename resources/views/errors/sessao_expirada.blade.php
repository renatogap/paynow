@extends('layouts.default')
@section('conteudo')
<section class="container">
    <h1 class="page-header"><img src="{{asset('images/sesao-expirada.png')}}" alt="icone de relógio" width="80"> Sessão expirada</h1>
    <p class="lead">Sua sessão expirou por um dos seguintes motivos:</p>
    <ul>
        @if(config('app.debug'))
        <li><b>Verifique se o token foi inserido corretamente no formulário</b></li>
        @endif
        <li>Você ficou ocioso no sistema por mais de {{config('session.lifetime')}} minutos</li>
        <li>Você tentou acessar esta página por um link inválido</li>
    </ul>
    <p>Clique <a href="{{url('')}}">aqui</a> e tente novamente</p>
</section>
@endsection
