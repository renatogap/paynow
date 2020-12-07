@extends('layouts.default')
@section('conteudo')
    <div class="text-center" style="padding: 6em 0 10em 0;">
        <h4>
            <span class="material-icons icone" style="font-size: 2em; color: red;">block</span> 
            <div>Deseja realmente cancelar o item <b>{{$pedido->nome_item}}</b>?</div>
        </h4>
        <br>
        <form method="POST" action="{{url('pedido/cancelar-gerente/'.$item.'/'.$id_tipo_cardapio)}}">
            {{ @csrf_field() }}
            <button type="submit" class="btn btn-danger">Sim</button> &nbsp;&nbsp;
            
            <a href="#" onclick="history.go(-1)" class="btn btn-primary">Não</a>
            <!--<a href="{{ url('pedido/historico-pedido/'. $pedido->id.'/'.$id_tipo_cardapio) }}" class="btn btn-primary">Não</a>-->
        </form>
    </div>
@endsection