@extends('layouts.default')


@section('conteudo')
    <h4>
        <span class="material-icons icone">style</span> Lista de Cartões
        <a href="#" class="float-right text-danger" data-toggle="modal" data-target="#modal" onclick="lerQrCode()">
            <span class="material-icons" style="font-size: 40px;">qr_code_scanner</span>
        </a>
    </h4>
    <br>
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

    @if($cartoes->count() > 0)
        <div class="list-group">
            @foreach($cartoes as $i => $cartao)
                <a href="{{ url('cartao/edit/'.$cartao->hash) }}" class="list-group-item">
                    <div class="float-right" style="clear: both; color: #666; font-size: 12px;">Criado em <strong>{{ date('d/m/y', strtotime($cartao->data)) }}</strong></div>
                    <span style="clear: both; font-size: 12px;" class="float-right badge {{($cartao->fk_situacao===1 ? 'badge-info' : ($cartao->fk_situacao===2 ? 'badge-success' : 'badge-danger'))}}" style="font-size: 17px;">{{ $cartao->situacao->nome }}</span>
                    
                    
                    <div  id="view-qrcode-{{$i+1}}" class="list-group-item-text" style="color: #666; font-size: 18px;">
                    <strong>{{$cartao->codigo}}</strong>
                    </div>

                    <!--
                    <div class="btn-group float-right" role="group" style="margin-top: -4em;">
                        <i class="material-icons">keyboard_arrow_right</i>
                    </div>
                    -->
                </a>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Nenhum cartão cadastrado.</div>
    @endif

    <a href="{{ url('cartao/create') }}" class="btn btn-primary  btn-circulo btn-flutuante" title="Cadastrar Cartão">
        <i class="material-icons icone" style="font-size: 2em;">add</i>
    </a>      

    <!-- Modal -->
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="material-icons icone" style="font-size: 30px;">qr_code_scanner</span>  Aproxime o cartão
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
                
                    <div id="mudarCamera" class="ml-1" onclick="mudarCamera()">
                        <span class="material-icons" style="font-size: 2.5em; position: absolute; z-index: 1; color: orange; cursor: pointer;">flip_camera_ios</span>
                    </div>

                    <video id="preview" style="width: 100%;"></video>

                    <button id="botao" style="display: none;" onclick="cliquei()"></button>
                </div>
            </div>
        </div>
    </div>

    <button id="botao" style="display: none;" onclick="cliquei()"></button>
@endsection

@section('scripts')

<script type="text/javascript" src="{{url('js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{url('js/QRCode.js')}}"></script>
<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>

<script>    
    
    <?php //foreach($cartoes as $i => $cartao): ?>
        /*new QRCode("view-qrcode-{{$i+1}}", {
            text: '{{ $cartao->codigo }}',
            width: 60,
            height: 60,
            colorDark: "black",
            colorLight: "white",
            correctLevel : QRCode.CorrectLevel.H
        });*/
    <?php //endforeach; ?>

    var indexCamera = 1;

    var scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });

    function lerQrCode() {
        scanner.addListener('scan', function(content) {
            scanner.stop();
            $('#div-aguarde').removeClass('d-none');
            document.getElementById('preview').classList.add('d-none');
            document.getElementById('mudarCamera').innerHTML = '';
            window.open(BASE_URL+'cartao/edit/'+content);
        });

        Instascan.Camera.getCameras().then(cameras => 
        {
            if(cameras.length == 1){
                scanner.start(cameras[0]);
            }
            else if(cameras.length > 1){
                scanner.start(cameras[1]);
            }
            else {
                alert("There is no camera on the device!");
            }
        });
    }

    function mudarCamera() {
        scanner.stop();
        indexCamera++;
        
        Instascan.Camera.getCameras().then(cameras => 
        {
            if(cameras.length >= indexCamera){
                scanner.start(cameras[indexCamera]);
            }
            else {
                indexCamera = 0;
                scanner.start(cameras[indexCamera]);
            }
        });
    }

    var audio = new Audio(BASE_URL+'beep1second.mp3');

    function play() {
        audio.play();
    }

    function pause(){
        audio.pause();
    }

    function cliquei() {
        play(); pause();
    }

    $('#botao').trigger('click');  //simula a interação com o cliente, já que o audio só é ativado depois de uma interação
        
</script>

@endsection