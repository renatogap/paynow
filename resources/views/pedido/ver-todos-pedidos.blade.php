@extends('layouts.default')
@section('conteudo')
        <h4>
            Pedidos Pendentes
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
                keyboard_backspace
            </a>
        </h4>
        
        @if (session('sucesso'))
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! session('sucesso') !!}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! session('error') !!}
            </div>
        @endif

        @if(count($pedidosAll) > 0)
            @foreach($pedidosAll as $tipo => $pedidos)
                <div class="text-center mt-3" style="font-size: 1.2em; font-weight: bold; border-bottom: 1px solid #ccc;">{{ $tipo }}</div>

                <div class="row text-center" style="padding: 1em 0 0 0;">

                    @foreach($pedidos as $pedido)            
                        <div class="col-3 pb-0" style="color: #333; ">
                            <a href="{{ url('pedido/historico-pedido-gerente/'.$pedido->id.'/'.$pedido->fk_tipo_cardapio) }}">
                                <div class="{{ $pedido->status==1 ? 'text-danger' : 'text-success' }}">
                                    <span class="material-icons" style="font-size: 3em;">deck</span>
                                </div>
                                <!--<div class="badge badge-{{ $pedido->status==1 ? 'danger' : 'success' }}">Pedido: {{ $pedido->id }}</div>-->
                                <div class="badge badge-{{ $pedido->status==1 ? 'danger' : 'success' }}">Mesa {{ $pedido->mesa }}</div>
                                <?php 
                                    $datetime1 = new DateTime($pedido->dt_pedido);
                                    $datetime2 = new DateTime(date('Y-m-d H:i:s'));
                                    $interval = date_diff($datetime1, $datetime2);
                                ?>
                                <div class="text-center" style="color: #444; font-weight: bold; font-size: 12px;">
                                    <div style="font-size: 11px;">
                                        <?php $arr = explode(' ', trim($pedido->usuario)); ?>
                                        {{ $arr[0] }}
                                    </div>
                                    {{ $interval->h."h ".$interval->i.'min' }}
                                </div>
                            </a>
                        </div>
                    @endforeach

                </div>

            @endforeach
        
        
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">Não há pedidos pendentes no momento.</div>
                </div>
            </div>
        @endif
@endsection

@section('scripts')
<script>
    setTimeout(function() {
        window.location.reload();
    }, 5000); // 5 segundos
</script>
@endsection