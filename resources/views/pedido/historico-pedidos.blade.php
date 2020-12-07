@extends('layouts.default')
@section('conteudo')
    <div>
        <h4>
            Pedido mesa <b>{{ $mesa }}</b>
            <a href="{{url('pedido/minhas-mesas')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
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

        <?php $valorTotal = 0; ?>
        
        @if(COUNT($myDados) > 0)
            @foreach($myDados as $tipo => $pedidos)
                <table class="table table-hover table-sm" width="100%" style="margin: 0;">
                    <tr>
                        <td colspan="4" class="bg-success text-white p-2 pl-3" style="font-size: 16px; font-weight: bold;">
                            {{ $tipo }}
                        </td>
                    </tr>
                </table>
                <table class="table">
                    @foreach($pedidos as $indice => $p)
                    <tr>
                        <td width="5%">
                            <a href="{{url('pedido/confirmar-cancelamento/'.$p->id_item_pedido)}}" class="btn btn-danger btn-sm" title="Cancelar Pedido">
                                <i class="material-icons" style="font-size: 19px;">delete</i>
                            </a>
                        </td>
                        <td width="95%">
                            <b>{{ $p->quantidade }} {{ $p->nome_item }}</b> <br>
                            @if($p->observacao) 
                                <div class="badge badge-warning" style="font-size: 11px;">* {{ $p->observacao }}</div>
                            @endif
                        </td>
                    </tr>      
                    <?php $valorTotal = $valorTotal + $p->valor_total_item; ?>
                    @endforeach
                </table>
            @endforeach
        @endif

        <hr>
        <div class="float-right" style="font-size: 1.5em;">
            <strong>Total: R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
        </div>
        <br><br><br>
    </div>
@endsection
