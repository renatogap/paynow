@extends('layouts.default')
@section('conteudo')
        <h4>
            Cardápios
            <a href="{{url('cliente/home')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
                keyboard_backspace
            </a>  
        </h4>
        <hr>
        @if($tipo_cardapios->count() > 0)
            <div class="row text-center" style="padding: 0em 0 0em 0;">
            
                @foreach($tipo_cardapios as $cardapio)
                    <a href="{{ url('cliente/cardapio/'.$cardapio->id) }}" class="col-4 pb-5" style="color: #333;">
                    <img src="{{ url('cardapio/tipo-cardapio/thumb/'.$cardapio->id) }}" alt="" width="60px" style="border: none;  text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
                        <div class="mt-2">
                            <strong>{{ $cardapio->nome }}</strong>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">Você não pode visualizar nenhum cardápio.</div>
                </div>
            </div>
            
        @endif    
@endsection