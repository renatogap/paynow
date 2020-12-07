@extends('layouts.default')
@section('conteudo')
    <div>
        <h4>
            <span class="material-icons icone" style="color: green;">check_circle_outline</span> 
            Confirmar pedido
            <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
            </a>
        </h4>
        <br>

        @if (session('error'))
            <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {!! session('error') !!}
            </div>
        @endif

        <?php $valorTotal = array_sum(array_column($pedido, 'valor')); ?>

        <table class="table" width="100%">
            @foreach($pedido as $indice => $p)
                <?php $cardapio = App\Models\Entity\Cardapio::find($p->id_cardapio) ?>

                <tr>
                    <td width="1%" style="padding: 0;">
                        <a href="{{ url('pedido/confirmar-pedido?remove='.$indice) }}" class="btn btn-sm float-left" title="Remover pedido">
                            <i class="material-icons" style="color: darkred;">delete</i>
                        </a>
                    </td>
                    <td width="20%" style="padding-top: 4px;">
                        <b>{{ $p->quantidade }} {{ $cardapio->categoria->nome }}: {{ $cardapio->nome_item }}</b>
                        @if($p->observacao)
                            <br>{{ $p->observacao }}
                        @endif
                    </td>
                    <td width="2%" align="right" style="padding-top: 4px;">
                        {{ number_format($p->valor, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>
        
        <div style="font-size: 1.2em; text-align: right;">
            <div>Subtotal: R$ {{ number_format($valorTotal, 2, ',', '.') }}</div>
        </div>

        <?php 
            $taxaServico = (in_array(3, $perflUsuario) ? ($valorTotal * (10 / 100)) : 0);
            $arr = explode('.', $taxaServico);

            if(count($arr) > 1) {

                $arr[1] = (strlen($arr[1]) < 2) ? intval($arr[1].'0') : $arr[1];

                if($arr[1] >= 51) {
                    $taxaServico = ($arr[0] + 1);
                }else {
                    $taxaServico = $arr[0];
                }
            }
        ?>

        <div style="font-size: 1.2em; text-align: right;">
            
            <input type="checkbox" name="taxaServico" id="taxaServico" value="1" {{ (in_array(3, $perflUsuario) ? 'checked' : 'disabled') }}  onclick="changeTaxa(this)">
            
            Comissão de venda: R$ 
            <span id="valorTaxaServico">{{ number_format($taxaServico, 2, ',', '.') }}</span>
            <span class="d-none" id="zeroTaxa">0,00</span>
        </div>

        <div style="font-size: 1.5em; text-align: right;">
            <strong>
                Total Geral: R$ <span id="valorComTaxa">{{ number_format(($valorTotal + $taxaServico), 2, ',', '.') }}</span>
                <span id="valorSemTaxa" class="d-none">{{ number_format(($valorTotal), 2, ',', '.') }}</span>
            </strong>
        </div>

        <br><br><br>
        <div style="clear: both;">
            <a href="#" data-url="{{ url('pedido/finalizar/leitor') }}" onclick="abrirLeitorCartao(this)" class="btn btn-success btn-lg btn-block">Finalizar pedido</a>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    var valorTaxaServico = document.getElementById('valorTaxaServico');
    var zeroTaxa = document.getElementById('zeroTaxa');
    var valorComTaxa = document.getElementById('valorComTaxa');
    var valorSemTaxa = document.getElementById('valorSemTaxa');


    function abrirLeitorCartao(e) {
        window.location = e.dataset.url+'?taxaServico='+document.getElementById('taxaServico').checked;
    } 

    function changeTaxa(e) {
        if(e.checked) {
            valorTaxaServico.classList.remove('d-none');
            zeroTaxa.classList.add('d-none');

            valorSemTaxa.classList.add('d-none');
            valorComTaxa.classList.remove('d-none');
        }else {
            valorTaxaServico.classList.add('d-none');
            zeroTaxa.classList.remove('d-none');

            valorSemTaxa.classList.remove('d-none');
            valorComTaxa.classList.add('d-none');
        }
    }
</script>
@endsection
