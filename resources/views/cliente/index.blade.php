@extends('layouts.default')
@section('conteudo')
    <h4>
        <span class="material-icons icone">qr_code_scanner</span> Aproxime o cartão
        <a href="{{url('')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>  
    </h4>
    <hr>

    @if (session('error'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {!! session('error') !!}
        </div>
    @endif

    <video id="preview" class="col-md-12" style="height: 25em;"></video>

@endsection

@section('scripts')

<script type="text/javascript" src="{{url('js/instascan.min.js')}}"></script>

<script>    
        var scanner = new Instascan.Scanner({
            video: document.getElementById('preview')
        });

        scanner.addListener('scan', function(content) {
            window.location = BASE_URL+'cliente/login/'+content;
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
</script>

@endsection