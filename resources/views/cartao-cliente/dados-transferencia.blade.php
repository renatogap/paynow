@extends('layouts.default')
@section('conteudo')
    <h4>
        <span class="material-icons icone" style="font-size: 1.5em;">compare_arrows</span> 
        Transferir Crédito
        <a href="{{url('cartao-cliente/leitor-transferencia')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h4>
    <hr>

    <form id="formulario">
        {{ @csrf_field() }}

        <input type="hidden" name="id_cartao_cliente" id="id_cartao_cliente" value="{{ $cartaoCliente->id }}">

        @if (session('sucesso'))
            <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('sucesso') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <div><label>Cliente que fará a transferência</label></div>
                    <input type="text" value="{{$cartaoCliente->nome}}" class="form-control" readonly>
                </div>
            </div>
        </div>
      
        <div class="row">
            <div class="col-md-4 col-6">
                <div class="form-group">
                    <div><label>Saldo atual</label></div>
                    <input type="text" value="{{ $cartaoCliente->valor_atual }}" disabled class="form-control">
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="form-group">
                    <div><label>Valor da Transferência</label></div>
                    <input type="number" name="valorTransferencia" id="valorTransferencia" class="form-control" required value="{{ (old('valorTransferencia') ?? '') }}" onkeyup="changeValor(this.value)">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <button type="button" id="btnSalvar" class="btn btn-parque ml-3 btn" data-toggle="modal" data-target="#modal">Continuar</button>
                <a href="{{url('cartao-cliente/leitor-transferencia')}}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        <!-- Modal -->
        <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Confirmação
                        </h5>
                    </div>
                    <div class="modal-body text-center mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>
                                    <span class="material-icons icone text-success" style="font-size: 3em !important;">contact_support</span> 
                                    <div>Transferir o valor de<br><b id="valorCreditoCliente" style="font-size: 1.5em;"></b><br>Confirma ?</div>
                                </h5>
                                <hr>
                                <button type="button" data-toggle="modal" data-target="#modalLeitor" onclick="lerQrCode(this)" class="btn btn-parque">Confirmar</button>
                                <a href="#" class="btn btn-danger" data-dismiss="modal">Cancelar</a>

                                <div id="msgError" class="alert alert-danger d-none mt-3"></div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div id="modalLeitor" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <span class="material-icons" style="font-size: 30px;">qr_code_scanner</span>  Aproxime o cartão de destino
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.location.reload()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="d-none" id="div-aguarde" style="text-align: center">
                            <h5 style="font-weight: normal;" class="mt-3 mb-0">
                                <div class="material-icons" style="color: darkgreen; font-size: 2em;">
                                    hourglass_full
                                </div>
                                <div>Aguarde, carregando...</div>
                            </h5>
                            <br><br><br><br>
                        </div>
                        
                        <video id="preview" class="col-md-12"></video>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('scripts')
<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>
<script>
    var valorCredito = document.getElementById('valorCreditoCliente');
    var valorTransferencia = document.getElementById('valorTransferencia');
    var id_cartao_cliente = document.getElementById('id_cartao_cliente');
    var btSalvar = document.getElementById('btnSalvar');

    valorCredito.textContent = 'R$ 0.00'.toLocaleString('pt-br', {minimumFractionDigits: 2});


    function changeValor(valor) {
        if(valor > 0){
            valorCredito.textContent = 'R$ ' + parseFloat(valor).toLocaleString('pt-br', {minimumFractionDigits: 2});
            //btSalvar.disabled = false;
        }
        else {
            valorCredito.textContent = 'R$ 0.00'.toLocaleString('pt-br', {minimumFractionDigits: 2});
            //btSalvar.disabled = true;
        }
    }

    var audio = new Audio(BASE_URL+'beep1second.mp3');

    function play() {
        audio.play();
    }

    function pause(){
        audio.pause();
    }

    function lerQrCode() {
        play(); pause();

        var scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });

        scanner.addListener('scan', function(content) {
            scanner.stop();
            play();
            $('#div-aguarde').removeClass('d-none');
            document.getElementById('preview').classList.add('d-none');
            window.location = BASE_URL+'cartao-cliente/salvar-transferencia?id_cartao_origem='+id_cartao_cliente.value+'&cartao_destino='+content+'&valorTransferencia='+valorTransferencia.value;
        });

        Instascan.Camera.getCameras().then(cameras => 
        {
            if(cameras.length == 1){
                scanner.start(cameras[0]);
            }
            else if(cameras.length > 0){
                scanner.start(cameras[1]);
            }
            else {
                alert("There is no camera on the device!");
            }
        });
    }
</script>


@endsection
