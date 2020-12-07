@extends('layouts.default')
@section('conteudo')
<style>
    label {font-weight: bold;}
    .linha:hover {
        background: #eee;
    }
</style>
    <div>
        <h4>
            <span class="material-icons icone" style="font-size: 1.5em">print</span> Relatórios

            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
                keyboard_backspace
            </a>
        </h4>
        <hr>

        <div class="row">
            <div class="col-md-12 ml-3 mb-3">
                * Informe o período do relatório
            </div>
        </div>
        <div class="row">
            <div class="row col-md-8"  style="margin: 0 auto;">
                <div class="col-6 col-md-6">
                    <div class="form-group">
                    <label for="">Data Início</label>
                    <input type="date" name="dtInicio" id="dtInicio" class="form-control" value="{{date('Y-m-d')}}">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="form-group">
                    <label for="">Data Término</label>
                    <input type="date" name="dtTermino" id="dtTermino" class="form-control" value="{{date('Y-m-d')}}">
                    </div>
                </div>
                <br>
            </div>
            <div class="row col-md-8"  style="margin: 0 auto;">
                <div class="col-md-6">
                    <div class="form-group">
                    <button class="btn btn-success btn-block" data-url="{{ url('relatorio/resumo/pdv') }}" onclick="abrirRelatorio(this)">
                        <i class="material-icons icone">print</i> Faturamento resumido do Promotor
                    </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <button class="btn btn-success btn-block" data-url="{{ url('relatorio/detalhado/pdv') }}" onclick="abrirRelatorio(this)">
                    <i class="material-icons icone">print</i> Faturamento do Promotor por PDV
                    </button>
                    </div>
                </div>
                
            </div>
            <div class="row col-md-8"  style="margin: 0 auto;">
                <div class="col-md-6">
                    <div class="form-group">
                    <button class="btn btn-success btn-block" data-url="{{ url('relatorio/taxa-servico') }}" onclick="abrirRelatorio(this)">
                        <i class="material-icons icone">print</i> Faturamento da Comissão de Vendas
                    </button>
                    </div>
                </div> 
                <div class="col-md-6">
                    <div class="form-group">
                    <button class="btn btn-success btn-block" data-url="{{ url('relatorio/fechamento-caixa') }}" onclick="abrirRelatorio(this)">
                    <i class="material-icons icone">print</i> Fechamento de Caixa
                    </button>
                    </div>
                </div>               
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var dtInicio = document.getElementById('dtInicio');
    var dtTermino = document.getElementById('dtTermino');

    function abrirRelatorio(e) {
        window.location = e.dataset.url+'?dtInicio='+dtInicio.value+'&dtTermino='+dtTermino.value;
    }

</script>
@endsection