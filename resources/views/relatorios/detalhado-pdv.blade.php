@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h5>
            Faturamento dos PDVs por Promotor
            
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h5>
        <hr>

        <form method="GET" action="{{ url('relatorio/detalhado/pdv') }}">
            <div class="row mb-2">
                <div class="col-6 col-md-4">
                    <label for="">Data início: </label>
                    <input type="date" name="dtInicio" value="{{ $dtInicio }}" max="{{ date('Y-m-d') }}" class="form-control">
                </div>
                <div class="col-6 col-md-2">
                    <label for="">Hora início: </label>
                    <input type="time" name="horaInicio" value="{{ $horaInicio }}" class="form-control"> 
                </div>
                <div class="col-6 col-md-4">
                    <label for="">Data término: </label>
                    <input type="date" name="dtTermino" value="{{$dtTermino}}" max="{{ date('Y-m-d') }}" class="form-control"> 
                </div>
                <div class="col-6 col-md-2">
                    <label for="">Hora término: </label>
                    <input type="time" name="horaTermino" value="{{ $horaTermino }}" class="form-control"> 
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block btn-parque"><i class="material-icons icone">search</i> Buscar</button>
                </div>
            </div>
        </form>
        <br>

        
        @if(COUNT($myDados) > 0)

            <?php $valorTotal = 0; ?>
            

            @foreach($myDados as $tipoCardapio => $promotores)
                <?php $valorTotalItem = 0; ?>

                <div class="row mb-3">
                
                    <div class="col-md-12">
                        <table class="table" width="100%" style="margin: 0;">
                            <tr>
                                <td colspan="4" class="bg-success text-white p-2 pl-3" style="font-size: 16px; font-weight: bold;">
                                    <?php list($idTipo, $tipo) = explode('_', $tipoCardapio); ?>
                                    {{ $tipo }}
                                </td>
                            </tr>
                            @foreach($promotores as $userPromotor => $itensPedido)
                                <?php list($idUsuario, $promotor) = explode('_', $userPromotor); ?>


                                <tr onclick="detalhesPromotor({{ $idTipo }}, {{ $idUsuario }})" style="background: #eee; cursor: pointer;">
                                    <td width="70%" class="pl-2">
                                        <i class="material-icons icone">person</i> <b>{{ $promotor }}</b>
                                    </td>
                                    <td align="right"><b>R$ {{ number_format(array_sum(array_column($itensPedido, 'valor_item')), 2, ',', '.') }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="10" class="p-0">
                                    <table class="detalhes_pedidos_{{ $idTipo }}_{{ $idUsuario }}" class="table table-sm show" style="width: 100%; display: none;">
                                        @foreach($itensPedido as $i => $item)
                                            @if($i == 0)
                                                <tr style="background: #ccc;">
                                                    <th class="pl-2 pt-1 pb-1">Item</th>
                                                    <th class="text-center pt-1 pb-1">Pedido</th>
                                                    <th class="text-center pt-1 pb-1">Mesa</th>
                                                    <th class="text-center pt-1 pb-1">Data/Hora</th>
                                                    <th class="text-right  pt-1 pb-1">Valor</th>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="pl-2 pt-1 pb-1" width="40%">{{ $item['quantidade'] }} {{ $item['nome_item'] }}</td>
                                                <td class="pt-1 pb-1" align="center" width="10%">{{ $item['id_pedido'] }}</td>
                                                <td class="pt-1 pb-1" align="center" width="10%">{{ $item['mesa'] }}</td>
                                                <td class="pt-1 pb-1" align="center" width="15%">{{ date('d/m H:i', strtotime($item['dt_pedido'])) }}</td>
                                                <td class="pt-1 pb-1" width="10%" align="right">
                                                    {{ $item['valor_item'] }}
                                                </td>
                                            </tr>
                                            <?php $valorTotal = $valorTotal + $item['valor_item']; ?>
                                            <?php $valorTotalItem = $valorTotalItem + $item['valor_item']; ?>
                                        @endforeach        
                                    </table>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                
            @endforeach
            <hr>
            
            <div class="float-right" style="font-size: 1.5em;">
                <strong>Total: R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
            </div>
        @else
            <div class="alert alert-info">Nenhum registro encontrado.</div>
        @endif
        <br><br><br>
    </div>
@endsection

@section('scripts')
<script>
    function detalhesPromotor(idTipo, idUser) {
        if(!$('.detalhes_pedidos_'+idTipo+'_'+idUser).hasClass('show')){
            $('.detalhes_pedidos_'+idTipo+'_'+idUser).fadeIn();
            $('.detalhes_pedidos_'+idTipo+'_'+idUser).addClass('show');
        }else {
            $('.detalhes_pedidos_'+idTipo+'_'+idUser).fadeOut();
            $('.detalhes_pedidos_'+idTipo+'_'+idUser).removeClass('show');
        }
    }
</script>
@endsection