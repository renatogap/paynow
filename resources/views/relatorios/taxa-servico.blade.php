@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h5>
        <span class="material-icons icone" style="font-size: 1.5em">print</span> Comissão do Promotor
            
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h5>
        <hr>

        <?php $valorTotal = 0; ?>

        <form method="GET" action="{{ url('relatorio/taxa-servico') }}">
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

        <div class="row">
            
            <div class="col-md-12">

                @if(count($pedidosAll) > 0)
                    <table class="table table-hover table-striped table-sm mt-3" width="100%">
                        @foreach($pedidosAll as $usuario => $pedidos)

                            <tr class="bg-success text-white p-2 pl-3" onclick="showDetalhes(<?= $pedidos[0]->fk_usuario ?>)" style="font-size: 14px; font-weight: bold; cursor: pointer;">
                                <th colspan="2" class="pt-2 pb-2"><span class="material-icons icone">person</span> {{ $usuario }}</th>
                                <th width="25%"  style="text-align: right;">
                                    R$ {{ number_format( array_sum(array_column($pedidos, 'taxa_servico')), 2, ',', '.') }}
                                </th>
                            </tr>

                            @foreach($pedidos as $pedido)
                            <tr class="detalhes-{{ $pedido->fk_usuario }}" style="display: none; font-size: 13px;">
                                <td class="pl-2">Pedido: <b>{{ $pedido->id }}</b> | mesa: <b>{{ $pedido->mesa }}</b></td>
                                <td width="15%" align="center">{{ date('d/m/y H:i', strtotime($pedido->dt_pedido)) }}</td>
                                <td width="25%" align="right" class="pr-2" style="color: green;">
                                    {{ number_format($pedido->taxa_servico, 2, ',', '.') }}
                                </td>
                            </tr>
                            <?php $valorTotal = $valorTotal + $pedido->taxa_servico; ?>

                            @endforeach
                            
                        @endforeach
                    </table>
                @else
                    <div class="alert alert-info mt-2">Nenhum registro encontrado.</div>
                @endif
            </div>
        </div>
        <hr style="margin-bottom: 1em; margin-top: 0em;">
        
        <div class="float-right" style="font-size: 1.5em;">
            <strong>Total geral: R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
        </div>
        <br><br><br>
    </div>
@endsection
@section('scripts')
<script>
    function showDetalhes(idUser) {
        if(!$('.detalhes-'+idUser).hasClass('show')){
            $('.detalhes-'+idUser).fadeIn();
            $('.detalhes-'+idUser).addClass('show');
        }else {
            $('.detalhes-'+idUser).fadeOut();
            $('.detalhes-'+idUser).removeClass('show');
        }
    }
</script>
@endsection