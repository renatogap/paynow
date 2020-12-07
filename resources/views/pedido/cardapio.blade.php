@extends('layouts.default')

<style>
    a {text-decoration: none !important;}
    a:hover {background: #e1e1e1e1;}
</style>

@section('conteudo')
    <h4>
        <span class="material-icons icone">receipt_long</span>
        Cardápio

        <a href="{{url('pedido/cardapios')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
        keyboard_backspace
        </a>
    </h4>
    <hr>
    @if (session('sucesso'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {!! session('sucesso') !!}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {!! session('error') !!}
        </div>
    @endif


    @if(count($myCardapio) > 0)
        <div class="list-group">
            @foreach($myCardapio as $tipo => $categorias)
                <h4 class="mt-3">{{ $tipo }}</h4>
                
                @foreach($categorias as $categoria => $itens)
                    <a href="#" class="list-group-item bg-success text-light" style="cursor: default;">
                        <div style="text-align: center;">
                            <span style="font-size: 14px;"><strong>{{ $categoria }}</strong></span>
                        </div>
                    </a>

                    @foreach($itens as $item)
                        <a href="{{ url('pedido/cardapio/item/'.$item->id) }}" class="list-group-item">
                            <!--<div style="float: left; width: 65px; height: 65px; border: 2px solid #333; margin-right: 0.5em; text-align: center;">
                                <img src="{{ url('cardapio/ver-thumb/'.$item->id) }}" width="65" height="65" onerror="this.src='<?= url('images/foto-error.png') ?>'" />
                            </div>-->
                            <div style="color: #666;">
                                <span style="float: right; color: #666; font-size: 13px; font-weight: bold;">
                                    R$ {{ $item->valor }}
                                </span>
                                <span style="font-size: 14px;"><strong>{{ $item->nome_item }}</strong></span>
                                <br>
                                
                                @if($item->detalhe_item)
                                    {{ $item->detalhe_item }}
                                    <br>
                                @endif
                            </div>
                        </a>

                    @endforeach
                @endforeach
                <br><br>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Nenhum registro encontrado.</div>
    @endif

@endsection