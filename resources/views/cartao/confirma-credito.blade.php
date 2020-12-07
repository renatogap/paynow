@extends('layouts.default')


@section('conteudo')
    <h3>
        <span class="material-icons" style="font-size: 30px;">check</span> Crédito confirmado
    </h3>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div style="text-align: center; padding: 5em 0 15em 0;">
                <div class="alert alert-success" style="font-size: 16px;">
                    {!! session('sucesso') !!}
                </div>    
                <a href="{{url('cartao-cliente/leitor')}}" class="btn btn-parque btn-block">Ler outro cartão</a>            
            </div>
        </div>
    </div>

@endsection