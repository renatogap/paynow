@extends('layouts.default')


@section('conteudo')
    <h5>
        <span class="material-icons icone" style="font-size: 30px;">check</span> Crédito Confirmado

	    <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h5>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info" style="text-align: center;">
                <h5>
                    <div>{!! session('sucesso') !!}</b></div>
                </h5>
            </div>
            <a href="{{url('cartao-cliente/leitor')}}" class="btn btn-parque btn-block">Ler outro cartão</a>            
        </div>
    </div>

@endsection
