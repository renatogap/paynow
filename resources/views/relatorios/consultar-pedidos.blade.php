@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h5>
            Consultar Cartão
            
            <a href="{{url('relatorio/consultar-pedidos')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h5>
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

        <div class="alert alert-info" style="font-weight: bold;">
            Saldo: R$ {{ number_format($cartaoCliente->valor_atual, 2, ',', '.') }}
        </div>

        @if($historicoCartao->count() > 0) 
            <h5 class="bg-success m-0 p-2 text-white">Extrato do Cartão</h5>
            <table class="table table-sm">
                @foreach($historicoCartao as $h)
                <tr>
                    <td>{{ date('d/m/Y H:i', strtotime($h->data)) }}</td>
                    <td style="color: <?= $h->valor > 0 ? 'green' : 'red' ?>;">{{ $h->valor }}</td>
                    <td>{{ $h->tipo_pagamento }}</td>
                    <td>{{ $h->observacao }}</td>
                </tr>
                @endforeach
            </table>
        @endif

        @if(COUNT($itensPedidoCliente) > 0)
            <hr>
            <h5 class="bg-success m-0 p-2 text-white">Histórico de Pedidos</h5>

            <?php $valorTotal = 0; ?>

            @foreach($itensPedidoCliente as $id_pedido => $pedidos)

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3" style="font-size: 12px;">
                            <div>
                                <b>{{ $pedidoCliente[$id_pedido]['dt_pedido'] }} {{ $pedidoCliente[$id_pedido]['hora_pedido'] }}</b>
                                <span class="float-right"><b>{{ $pedidoCliente[$id_pedido]['usuario'] }}</b></span>
                            </div>
                            <div>@if($pedidoCliente[$id_pedido]['mesa']) Mesa <b>{{ $pedidoCliente[$id_pedido]['mesa'] }}</b> | @endif Pedido <b>{{ $pedidoCliente[$id_pedido]['id'] }}</b></div>                            
                            
                        </div> 
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <table class="table table-hover table-sm" width="100%" style="margin: 0;">
                            <!--
                            <tr class="bg-light">
                                <th width="5%">Ítem pedido</th>
                                <th width="25%" style="text-align: right;">Valor R$</th>
                            </tr>
                            -->
                            @foreach($pedidos as $item)
                                <tr>
                                    <td width="95%" class="pl-2">
                                    @if($item->status != 4 && (in_array(6, $perfisUsuario) || in_array(1, $perfisUsuario)))
                                        <a href="{{url('pedido/confirmar-cancelamento-gerente2/'.$item->id_item.'/'.$codigo)}}" class="text-danger material-icons icone" style="font-size: 25px !important; margin-left: -5px; margin-top: -5px;" title="Cancelar Pedido">delete</a>
                                    @endif
                                        <!-- <a href="#" class="text-danger material-icons icone" style="font-size: 25px !important; margin-left: -5px;">delete</a> -->
                                        <b>{{ ($item->quantidade >= 1 ? intval($item->quantidade) : $item->quantidade)  }} {{ $item->categoria }}: {{ $item->nome_item }}</b> {!! ($item->status != 4 ? '' : '<span class="badge badge-danger">Cancelado</span>') !!}
                                    </td>                                        
                                    <td align="right" class="{{ ($item->status != 4 ? '' : 'text-danger') }}">{{ ($item->status != 4 ? number_format($item->valor_total_item, 2, ',', '.') : '0,00') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td align="right"><b>Subtotal</b></td>
                                <td align="right">{{ number_format(($pedidoCliente[$id_pedido]['valor_total'] - $pedidoCliente[$id_pedido]['taxa_servico']), 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td align="right" class="text-danger">Comissão</td>
                                <td align="right" class="text-danger">{{ number_format($pedidoCliente[$id_pedido]['taxa_servico'], 2, ',', '.') }}</td>
                            </tr>
                            <tr bgColor="#eee">
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
            <div class="alert alert-warning">Nenhum pedido registrado.</div>
        @endif
        <br><br><br>
    </div>
@endsection
