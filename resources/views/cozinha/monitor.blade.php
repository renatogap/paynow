@extends('layouts.default')

@section('conteudo')
    <h4>
        Cozinha

        <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a> 
    </h4>
    <hr>

    {{ @csrf_field() }}

    @if (session('sucesso'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('sucesso') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('error') }}
        </div>
    @endif    

    <?php $visto_pela_cozinha = null; ?>

    @if($pedidos->count() > 0)
        <div class="row">
            @foreach($pedidos as $i => $p)
            <div class="col-md-3 col-sm-6">
                <div class="card-deck">
                    <div class="card bg-light mb-3" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
                        <div class="card-header pl-3 pr-3">
                            <span class="badge badge-primary p-2">Nº <b>{{$p->id}}</b></span> &nbsp;
                            Mesa: <b>{{ $p->mesa }}</b></b> 
                            <span class="float-right text-muted" style="font-size: 11px;">
                                <i class="material-icons" style="font-size: 12px;">access_time</i> {{ date('H:i', strtotime($p->dt_pedido)) }}h
                            </span>
                        </div>
                        <div class="card-body pl-3 pr-3 pt-1" style="background: white;">

                            <?php

                                $itens = \Parque\Seguranca\App\Models\DB::table('pedido_item as pi')
                                                        ->join('cardapio as c', 'c.id', '=', 'pi.fk_item_cardapio')
                            							->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
                                                        ->whereIn('c.fk_tipo_cardapio', $tipoCardapioCozinha)
                                                        ->where('pi.fk_pedido', $p->id)
                                                        ->where('pi.status', 1)
                                                        //->where('pi.created_at', '>=', date('Y-m-d 00:00:00'))
                                                        //->where('pi.created_at', '<=', date('Y-m-d 23:59:59'))
                                                        ->select(['pi.*', 'cc.nome as categoria', 'c.nome_item', 'c.unid', 'c.cozinha'])
                                                        ->get(); 

                                //$aVistoCozinha = $itens->pluck('visto_pela_cozinha')->toArray();
                            ?>

                            @foreach($itens as $i)
                                <div class="card-text mt-2" style="font-weight: bold; color: #444;">
                                    {{ $i->unid == 1 ? intval($i->quantidade) : $i->quantidade }} {{ $i->categoria }}: {{ $i->nome_item }}
                                </div>

                                @if($i->observacao)
                                <div class="badge badge-warning" style="font-size: 11px;">* {{ $i->observacao }}</div>
                                @endif

                                @if($i->status == 1 && $i->visto_pela_cozinha == 0)
                                    <?php $visto_pela_cozinha = 1; ?>

                                    <?php App\Models\Entity\PedidoItem::where('id', $i->id)->update(['visto_pela_cozinha' => 1]) ?>
                                @endif
                            @endforeach
                        </div>
                        <div class="card-footer">
                            <a href="{{ url('cozinha/confirma/'.$p->id) }}" class="btn btn-success" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">
                                Pronto!
                            </a>

                            <span class="float-right"><i class="material-icons icone">person</i> {{ \Parque\Seguranca\App\Models\Entity\Usuario::find($p->fk_usuario)->nome }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button id="botao" style="display: none;" onclick="cliquei()"></button>        
    @else
        <div class="alert alert-info">Nenhum pedido registrado.</div>
    @endif

@endsection

@section('scripts')

<script>    
    $('#container-fluid').css({'max-width': '100%'});

    setTimeout(function() {
        window.location.reload();
    }, 5000); // 5 segundos


    var audio = new Audio(BASE_URL+'beep1second.mp3');

    function play() {
        audio.play();
    }

    function pause(){
        audio.pause();
    }

    function cliquei() {
        play(); pause();
    }

    $('#botao').trigger('click');  //simula a interação com o cliente, já que o audio só é ativado depois de uma interação
        
    @if($visto_pela_cozinha)
        navigator.vibrate(200);
        play();
    @endif
    
</script>

@endsection
