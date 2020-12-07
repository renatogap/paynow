@extends('layouts.default')


@section('conteudo')
    <h5>
        Cartão do Cliente
        <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
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

    <form id="form" method="get" action="{{url('cartao-cliente')}}">
        <div class="row">
            <div class="col-6 col-sm-3 col-md-3">
                <div class="form-group">
                    <label>Data</label>
                    <input type="date" name="data" class="form-control" value="{{ ($request->data ? $request->data : (!$request->cpf && !$request->nome ? date('Y-m-d') : '')) }}">
                </div>
            </div>
            <div class="col-6 col-sm-3 col-md-3">
                <div class="form-group">
                    <label>CPF</label>
                    <input type="tel" name="cpf" id="cpf" class="form-control" value="{{ ($request->cpf ?? '') }}" maxlength="14" onkeydown="fMasc(this, mCPF);">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control" value="{{ ($request->nome ?? '') }}">
                </div>
            </div>
            <div class="col-sm-12 col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-parque">
                        <span class="material-icons icone">search</span> Pesquisar
                    </button>
                    <a href="#" class="btn btn-primary " data-toggle="modal" data-target="#modal" onclick="lerQrCode()">
                        <span class="material-icons icone">qr_code_scanner</span> Ler cartão
                    </a>
                </div>
            </div>
        </div>
    </form>

    @if($lista->count() > 0)
        <div class="list-group">
            @foreach($lista as $i => $v)
                <a href="{{ url('cartao-cliente/edit/'.$v->id) }}" class="list-group-item">
                    <div style="color: #666;">
                        <span style="float: right; color: #666; font-size: 11px;">
                            {{ $v->data }}
                        </span>
                        <span style="font-size: 14px;"><strong>{{ $v->nome }}</strong></span>
                        <br>
                        <!--
                        @if($v->cpf)
                            {{ $v->cpf }}
                            <br>
                        @endif
                        -->
                        <span class="{{ ($v->valor_atual > 0 ? 'text-success' : 'text-danger') }}">R$ {{ $v->valor_atual }}</span>
                        <span class="float-right" style="font-size: 14px;">
                            {!! $v->status_desc !!}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Nenhum registro encontrado.</div>
    @endif


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
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>
<script type="text/javascript" src="{{url('js/app/controllers/EntradaClienteController.js')}}"></script>
<script>
    var oController = new EntradaClienteController();

    var indexCamera = 1;

    var audio = new Audio(BASE_URL+'beep1second.mp3');

    var scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });

    function play() {
        audio.play();
    }

    function pause(){
        audio.pause();
    }

    function lerQrCode() {
        play(); pause();

        scanner.addListener('scan', function(content) {
            scanner.stop();
            play();
            $('#div-aguarde').removeClass('d-none');
            document.getElementById('preview').classList.add('d-none');
            document.getElementById('mudarCamera').innerHTML = '';
            window.location = BASE_URL+'cartao-cliente/create/'+content;
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

    function fMasc(objeto,mascara) {
        obj=objeto
        masc=mascara
        setTimeout("fMascEx()",1)
    }
    function fMascEx() {
        obj.value=masc(obj.value)
    }
    function mTel(tel) {
        tel=tel.replace(/\D/g,"")
        tel=tel.replace(/^(\d)/,"($1")
        tel=tel.replace(/(.{3})(\d)/,"$1)$2")
        if(tel.length == 9) {
            tel=tel.replace(/(.{1})$/,"-$1")
        } else if (tel.length == 10) {
            tel=tel.replace(/(.{2})$/,"-$1")
        } else if (tel.length == 11) {
            tel=tel.replace(/(.{3})$/,"-$1")
        } else if (tel.length == 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        } else if (tel.length > 12) {
            tel=tel.replace(/(.{4})$/,"-$1")
        }
        return tel;
    }
    function mCPF(cpf){
        cpf=cpf.replace(/\D/g,"")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
        cpf=cpf.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
        return cpf
    }
</script>

@endsection
