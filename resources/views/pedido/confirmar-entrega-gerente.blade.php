@extends('layouts.default')
@section('conteudo')
    <div class="text-center" style="padding: 6em 0 10em 0;">
        <h4>
            <span class="material-icons icone text-success" style="font-size: 3em !important;">contact_support</span> 
            <div>Deseja realmente mudar a situação do pedido para <b>Entregue</b>?</div>
        </h4>
        <br>
        <form method="POST" action="{{url('pedido/salvar-entrega-gerente/'.$id_pedido.'/'.$tipo)}}">
            {{ @csrf_field() }}
            <button type="submit" class="btn btn-success" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
              <i class="material-icons">thumb_up</i> Sim
            </button> &nbsp;&nbsp;
            
            <a href="{{ url('pedido/historico-pedido-gerente/'. $id_pedido.'/'.$tipo) }}" class="btn btn-danger" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
                <i class="material-icons">thumb_down</i> Não
            </a>
        </form>
    </div>
@endsection