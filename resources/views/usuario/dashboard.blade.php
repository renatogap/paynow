@extends('layouts.default')
@section('conteudo')
<div class="mt-3 pl-3 pr-3">
    <h3><i class="glyphicon glyphicon-chevron-right"></i> Bem-vindo ao Sistema <b>{{config('parque.slogan')}}</b></h3>
    <div class="text-center mt-5">
        <!--<img class="logo" src="{{ config('parque.logo') }}" alt="">-->

        <div class="row">
            @if(isset($menu))
                @foreach($menu as $raiz)
                    <div class="col-4 col-md-4 mb-5" style="margin: 0 auto;">
                        <a href="{{url($raiz->acao)}}">
                            {!! $raiz->icone !!}
                            <div class="text-dark" style="font-weight: bold;">{!! $raiz->nome !!}</div>
                        </a>
                    </div>
                @endforeach
            @endif
            <div class="col-4 col-md-4 mb-5" style="margin: 0 auto;">
                    <a href="{{url('seguranca/usuario/logout')}}">
                        <span class="material-icons" style="font-size: 4em; color: darkgreen;">power_settings_new</span>
                        <div class="text-dark" style="font-weight: bold;">Sair</div>
                    </a>
                </div>
        </div>
    </div>
</div>
@endsection