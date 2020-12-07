@extends('layouts.default')
@section('conteudo')
    <div>
        <h4>
            Histórico do Pedido
            
            <a href="{{url('pedido/meus-pedidos')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>           
        </h4>
        
        <br>

        <!--
        <div class="row mt-2">
            <div class="col-12">
            <div class="text-center" style="margin-top: -1em; font-size: 12px; color: #666;">{{ date('d/m/Y H:i', strtotime($pedidos[0]->dt_pedido)) }}</div>
            </div>
        </div>
        -->

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

        @foreach($myDados as $tipo => $aPedidos)
            <h4 class="text-center"><b>{{ $tipo }}</b></h4>

            <div class="mb-3 text-center" style="font-size: 12px;">
                <div>Mesa: <b>{{ $pedidos[0]->mesa }}</b> | Pedido: <b>{{ $pedidos[0]->id }}</b></div>
                <div>Em: <b>{{date('d/m/Y H:i', strtotime($pedidos[0]->dt_pedido))}}</b> @if($pedidos[0]->dt_pronto) | Pedido Pronto: <b>{{date('H:i', strtotime($pedidos[0]->dt_pronto))}}</b>@endif</div>
            </div> 

            <table class="table">
                @foreach($aPedidos as $indice => $p)
                <tr>                    
                    <td width="95%">
                    <b>{{ $p->unid == 1 ? intval($p->quantidade) : $p->quantidade }} {{ $p->categoria }}: {{ $p->nome_item }}</b><br>
                        @if($p->observacao) 
                            <div class="badge badge-warning" style="font-size: 11px;">* {{ $p->observacao }}</div>
                        @endif
                    </td>
                    <td width="5%" align="right" {{ $p->status == 4 ? 'colspan="2"' : '' }}>
                        @if($p->status == 4)
                            <span class="badge badge-danger">Cancelado</span>
                        @else
                            {{ number_format($p->valor_total_item, 2, ',', '.') }}
                        @endif
                    </td>
                    @if($p->status != 4)
                        @if(in_array(6, $perfisUsuario))
                            <td width="5%">
                                <a href="{{url('pedido/confirmar-cancelamento/'.$p->id_item_pedido.'/'.$p->fk_tipo_cardapio)}}" class="text-danger" title="Cancelar Pedido"><i class="material-icons" style="font-size: 25px;">delete</i></a>
                            </td>
                        @endif
                    @endif
                </tr>      
                @endforeach
            </table>
        @endforeach
        <hr style="margin-top: -1em;">

        @if($pedidos[0]->status == 2)
            <div class="float-left" style="font-size: 1.5em;">
                <a href="{{ url('pedido/confirmar-entrega/'.$pedidos[0]->id.'/'.$pedidos[0]->fk_tipo_cardapio) }}" class="btn btn-parque" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Entregue</a>
            </div>
        @endif
        <br><br><br>
    </div>
@endsection
