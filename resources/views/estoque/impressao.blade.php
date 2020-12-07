@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h5>
            <span class="material-icons icone" style="font-size: 1.3em; color: #333;">print</span>
            Movimentação do Estoque
            
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h5>
        <hr>
        <form method="GET" action="{{ url('estoque/relatorio') }}">
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
            <div class="row mb-2">
                <div class="col-12">
                    <input type="checkbox" name="soEntradas" {{ ($soEntradas ? 'checked' : (!$soEntradas && !$soSaidas ? 'checked' : '')) }}> <label for="">Mostrar somente as Entradas: </label>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12">
                    <input type="checkbox" name="soSaidas" {{ ($soSaidas ? 'checked' : (!$soEntradas && !$soSaidas ? 'checked' : '')) }}> <label for="">Mostrar somente as Saídas: </label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block btn-parque"><i class="material-icons icone">search</i> Buscar</button>
                </div>
            </div>
        </form>
        <br>

        <div class="row">
            <div class="col-md-12">
                @if($estoquePdvs)
                    @foreach($estoquePdvs as $pdv => $estoquePdv)

                        <?php list($nomePDV, $id_pdv) = explode('_', $pdv);  ?>

                        <table class="table table-hover mb-0" width="100%">
                                <tr class="bg-success text-white" onclick="showDetalhes({{ $id_pdv }})" style="font-size: 14px; font-weight: bold; cursor: pointer;">
                                    <th colspan="2" class="pt-2 pb-2">{{ $nomePDV }}</th>
                                    <th width="25%"  style="text-align: right;"> &nbsp; </th>
                                </tr>
                        </table>

                        @foreach($estoquePdv as $produto => $itens)

                        <?php list($nome, $qtd, $id_item) = explode('__', $produto);  ?>

                        <div class="detalhes-{{ $id_pdv }}" style="display: none;">

                            <table class="table table-hover mb-0" width="100%">
                                <tr class="p-2 pl-3" onclick="showDetalhesItem({{ $id_item }})" style="font-size: 12px; font-weight: bold; background: #eee;">
                                    <th colspan="2">{{ $nome }}</th>
                                    <th width="35%"  style="text-align: right;">
                                        QTD: {{ $qtd }}
                                    </th>
                                </tr>
                            </table>
                        </div>

                        <div class="detalhes-item-{{ $id_item }}" style="display: none;">
                            <table class="table table-hover table-sm" width="100%">
                                <tr>
                                    <th width="15%">Data</th>
                                    <th>Detalhes</th>
                                    <th>Qtd</th>
                                    <th style="text-align: right;">V.Unit</th>
                                    <th style="text-align: right;">V.Total</th>
                                    <!-- <th width="8%">Usuário</th> -->
                                </tr>
                                @foreach($itens as $item)

                                <?php $usuario = explode(' ', $item->usuario); ?>

                                <tr>
                                    <td style="font-size: 13px;">{{ date('d/m H:i', strtotime($item->data)) }}</td>
                                    <td style="font-size: 13px;">{{ $item->observacao }}</td>
                                    <td style="font-size: 13px; color: {{$item->tipo == 'E' ? 'green' : 'red'}};">{{ $item->quantidade }}</td>
                                    <td style="font-size: 13px; color: {{$item->tipo == 'E' ? 'green' : 'red'}};" align="right" class="pr-2">
                                        {{ number_format($item->valor_unitario, 2, ',', '.') }}
                                    </td>
                                    <td style="font-size: 13px; color: {{$item->tipo == 'E' ? 'green' : 'red'}};" align="right" class="pr-2">
                                        {{ number_format($item->valor_total, 2, ',', '.') }}
                                    </td>
                                    <!-- <td style="font-size: 13px;">{{ $usuario[0] }}</td> -->
                                </tr>
                                @endforeach

                            </table>
                        </div>
                        @endforeach    
                        {{-- <div id="total" class="float-right mr-2" style="margin-top: -1.8em; font-weight: bold; color: #fff; ">R$ {{ number_format($totalUsuario, 2, ',', '.') }}</div> --}}

                    @endforeach
                @else
                    <div class="alert alert-info mt-3">Nenhum registro encontrado para este filtro</div>
                @endif
            </div>
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
            $('#total-'+idUser).css({"color": '#000', "margin-top": "0px"});
        }else {
            $('.detalhes-'+idUser).fadeOut();
            $('.detalhes-'+idUser).removeClass('show');
            $('#total-'+idUser).css({"color": '#fff', "margin-top": "-1.7em"});
        }
    }

    function showDetalhesItem(idUser) {
        if(!$('.detalhes-item-'+idUser).hasClass('show')){
            $('.detalhes-item-'+idUser).fadeIn();
            $('.detalhes-item-'+idUser).addClass('show');
            //$('#total-'+idUser).css({"color": '#000', "margin-top": "0px"});
        }else {
            $('.detalhes-item-'+idUser).fadeOut();
            $('.detalhes-item-'+idUser).removeClass('show');
            //$('#total-'+idUser).css({"color": '#fff', "margin-top": "-1.7em"});
        }
    }
</script>
@endsection
