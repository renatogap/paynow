@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h4>
            Meus Pedidos
            
            <a href="{{url('cliente/home')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h4>
        <hr>

        @if(COUNT($itensPedidoCliente) > 0)

            <?php $valorTotal = 0; ?>

            @foreach($itensPedidoCliente as $id_pedido => $pedidos)
                <!--
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="bg-success text-light p-1 pl-2" style="font-size: 16px; font-weight: bold;">{{ $pedidoCliente[$id_pedido]['tipo_cardapio'] }}</div>
                    </div>
                </div>
                -->
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="bg-success text-light p-1 pl-2 pr-2">
                            <b>Pedido nº: {{ $pedidoCliente[$id_pedido]['id'] }}</b>

                            <span class="float-right"><i class="material-icons icone">access_time</i> {{ $pedidoCliente[$id_pedido]['hora_pedido'] }}</span>
                        </div>
                    </div>
                </div>
                <!--
                <div class="row mb-1">
                    <div class="col-4">
                        <div class="badge badge-warning" style="font-size: 12px;">
                            <i class="material-icons icone">access_time</i> Pedido: {{ $pedidoCliente[$id_pedido]['hora_pedido'] }}
                        </div>
                    </div>

                    @if($pedidoCliente[$id_pedido]['hora_pronto'])
                    <div class="col-4">
                        <span class="badge badge-success" style="font-size: 12px;">
                            <i class="material-icons icone">access_alarms</i> Pronto: {{ $pedidoCliente[$id_pedido]['hora_pronto'] }}
                        </span>
                    </div>
                    @endif
                        
                    @if($pedidoCliente[$id_pedido]['hora_entrega'])
                    <div class="col-4">
                        <span class="badge badge-info p-1 float-right" style="font-size: 12px;">
                        Entregue: {{ $pedidoCliente[$id_pedido]['hora_entrega'] }}
                        </span>
                    </div>
                    @endif                        
                </div>
                -->

                <div class="row mb-3">
                    <div class="col-md-12">
                        <table class="table table-hover table-striped table-bordered table-sm" width="100%" style="margin: 0;">
                            <!--
                            <tr class="bg-light">
                                <th width="5%">Ítem pedido</th>
                                <th width="25%" style="text-align: right;">Valor R$</th>
                            </tr>
                            -->
                            @foreach($pedidos as $item)
                                <tr>
                                    <td width="80%" class="pl-2">{{ $item->quantidade }} {{ $item->nome_item }} {!! ($item->status != 4 ? '' : '<span class="badge badge-danger">Cancelado</span>') !!}</td>                                        
                                    <td align="right" class="{{ ($item->status != 4 ? 'text-success' : 'text-danger') }}">{{ ($item->status != 4 ? number_format($item->valor_total_item, 2, ',', '.') : '0,00') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td align="right"><b>Subtotal</b></td>
                                <td align="right">{{ number_format(($pedidoCliente[$id_pedido]['valor_total'] - $pedidoCliente[$id_pedido]['taxa_servico']), 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td align="right" class="text-danger">Comissão (10%)</td>
                                <td align="right" class="text-danger">{{ number_format($pedidoCliente[$id_pedido]['taxa_servico'], 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td align="right" style="font-size: 14px;"><b>Total Geral</b></td>
                                <td align="right" style="font-size: 14px;"><b>{{ number_format($pedidoCliente[$id_pedido]['valor_total'], 2, ',', '.') }}</b></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php $valorTotal = $valorTotal + $item->valor_total; ?>
            @endforeach
            
            <div class="float-right" style="font-size: 1.5em;">
                <strong>Total: R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
            </div>
        @else
            <div class="alert alert-info">Nenhum registro encontrado.</div>
        @endif
        <br><br><br>
    </div>
@endsection
