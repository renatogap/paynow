@extends('layouts.default')
@section('conteudo')
    <div>
        <h4>
            Histórico do Pedido
            
            <a href="{{url('pedido/visualizacao-gerente')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
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

        <?php  $total = 0 ?>

        @foreach($myDados as $tipo => $aPedidos)
            <h4 class="text-center"><b>{{ $tipo }}</b></h4>

            <div class="mb-3 text-center" style="font-size: 12px;">
                <div>Data <b>{{date('d/m/Y', strtotime($pedidos[0]->dt_pedido))}}</b></div>
                <div>Mesa <b>{{ $pedidos[0]->mesa }}</b> | Pedido <b>{{ $pedidos[0]->id }}</b></div>
                <div>Hora Pedido: <b>{{date('H:i', strtotime($pedidos[0]->dt_pedido))}}</b> @if($pedidos[0]->dt_pronto) | Pedido Pronto: <b>{{date('H:i', strtotime($pedidos[0]->dt_pronto))}}</b>@endif</div>
                <div><b>Promotor: {{ $pedidos[0]->usuario }}</b></div>
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
                    <td width="5%" align="right" {{ $p->status == 4 ? 'colspan=2' : '' }}>
                        @if($p->status == 4)
                            <span class="badge badge-danger">Cancelado</span>
                        @else
                            {{ number_format($p->valor_total_item, 2, ',', '.') }}
                        @endif
                    </td>
                    @if($p->status != 4)
                        @if(in_array(6, $perfisUsuario) || in_array(1, $perfisUsuario))
                            <td width="5%">
                                <a href="{{url('pedido/confirmar-cancelamento-gerente/'.$p->id_item_pedido.'/'.$p->fk_tipo_cardapio)}}" class="text-danger" title="Cancelar Pedido"><i class="material-icons" style="font-size: 25px;">delete</i></a>
                            </td>
                        @endif
                    @endif
                </tr>    
                
                <?php $total = $total + $p->valor_total_item; ?>

                @endforeach
            </table>
        @endforeach
        <hr style="margin-top: -1em;">

        <?php $taxaServico = 0; ?><!-- Ainda não foi pensado como ficará aqui a comissão -->

        <div style="font-size: 1.5em; text-align: right;">
            <strong>
                Total: R$ <span id="valorComTaxa">{{ number_format(($total + $taxaServico), 2, ',', '.') }}</span>
                <span id="valorSemTaxa" class="d-none">{{ number_format(($total), 2, ',', '.') }}</span>
            </strong>
        </div>

       

        @if($pedidos[0]->status_pedido == 2)
            <div class="float-left" style="font-size: 1.5em;">
                <a href="{{ url('pedido/confirmar-entrega-gerente/'.$pedidos[0]->id.'/'.$pedidos[0]->fk_tipo_cardapio) }}" class="btn btn-parque" style="text-shadow: 5px 5px 5px rbga(0,0,0,0.5); box-shadow: 5px 5px 5px rgba(0,0,0,0.5);">Entregue</a>
            </div>
        @endif
        <br><br><br>
    </div>
@endsection
