@extends('layouts.default')
@section('conteudo')
        <h4>
            Meus Pedidos
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
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

        @if($pedidos->count() > 0)
        <div class="row text-center" style="padding: 0 0 0 0;">
            @foreach($pedidos as $pedido)
                <div class="col-3 pb-5" style="color: #333; ">
                    <a href="{{ url('pedido/historico-pedido/'.$pedido->id.'/'.$pedido->fk_tipo_cardapio) }}">
                        <div class="{{ $pedido->status==1 ? 'text-danger' : 'text-success' }}">
                            <span class="material-icons" style="font-size: 2.5em;">deck</span>
                        </div>
                        <div class="badge badge-{{ $pedido->status==1 ? 'danger' : 'success' }}">Mesa {{ $pedido->mesa }}</div>
                        <?php 
                            $datetime1 = new DateTime($pedido->dt_pedido);
                            $datetime2 = new DateTime(date('Y-m-d H:i:s'));
                            $interval = date_diff($datetime1, $datetime2);
                        ?>
                        <div class="text-center" style="color: #444; font-weight: bold; font-size: 12px;">
                            {{ $interval->h."h ".$interval->i.'min' }}
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
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