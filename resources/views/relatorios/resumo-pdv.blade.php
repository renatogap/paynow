@extends('layouts.default')
@section('conteudo')
<style>
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h5>
            @if(in_array(4, $perfisUsuario))
                <span class="material-icons icone" style="font-size: 1.5em;">print</span> Meus Faturamentos
            @else
                <span class="material-icons icone" style="font-size: 1.5em">print</span> Faturamento dos PDVs
            @endif            
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h5>
        <hr>

        <?php $valorTotal = 0; ?>

        
        <form method="GET" action="{{ url('relatorio/resumo/pdv') }}">
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
                @if($dados)
                    <table class="table table-hover table-striped mt-3" style="border: 0;" width="100%">
                        @foreach($dados as $tipo => $valor)
                            <tr class="">
                                <td class="pl-2"><b>{{ $tipo }}</b></td>                                
                                <td width="25%" align="right" class="pr-2">
                                    {{ number_format($valor, 2, ',', '.') }}
                                </td>
                            </tr>
                            <?php $valorTotal = $valorTotal + $valor; ?>
                        @endforeach
                    </table>
                @else
                        <div class="alert alert-info mt-3">Nenhum registro encontrado</div>
                @endif
            </div>
        </div>
        <hr style="margin: 0;">
        
        <div class="float-right" style="font-size: 1.5em;">
            <strong>Total: R$ {{ number_format($valorTotal, 2, ',', '.') }}</strong>
        </div>
        <br><br><br>
    </div>
@endsection
