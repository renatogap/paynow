@extends('layouts.default')


@section('conteudo')
    <h4>
        Aproxime o cartão de orígem
        <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h4>
    <hr>
    @if (session('sucesso'))
        <div class="alert alert-danger">
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

@endsection

@section('scripts')

<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>

<script>   
    var indexCamera = 1;

    var scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });

    scanner.addListener('scan', function(content) {
        scanner.stop();
        $('#div-aguarde').removeClass('d-none');
        document.getElementById('preview').classList.add('d-none');
        document.getElementById('mudarCamera').innerHTML = '';
        play();
        window.location = BASE_URL+'cartao-cliente/dados-transferencia/'+content;
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