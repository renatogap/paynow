@extends('layouts.default')
@section('conteudo')
    <h4>
        <span class="material-icons icone">payment</span> Detalhes do Cartão

        <a href="{{url('cartao')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a> 
    </h4>
    <hr>

    <form method="post" action="{{ url('cartao/bloqueia-desbloqueia') }}">
        {{ @csrf_field() }}

        <input type="hidden" id="id" name="id" value="{{$cartao->id}}">
        <input type="hidden" id="codigo" name="codigo" value="{{$cartao->codigo}}">

        @if (session('error'))
            <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            
            <div class="form-group col-md-4">
                <div><label>ID</label></div>
                <span style="font-size: 17px;">
                    {{ $cartao->codigo }}
                </span>
            </div>
        
            <!--
            <div class="form-group col-md-4">
                <div><label>Data cadastro</label></div>
                <span style="font-size: 17px;">
                    {{ date('d/m/Y', strtotime($cartao->data)) }}
                </span>
            </div>
            -->
            
            <div class="col-md-4">
                <div><label>Situação</label></div>
                <span style="font-size: 13px;" class="badge {{($cartao->fk_situacao===1 ? 'badge-info' : ($cartao->fk_situacao===2 ? 'badge-success' : (in_array($cartao->fk_situacao, [3,4]) ? 'badge-danger' : '')))}}" style="font-size: 17px;">{{ $cartao->situacao->nome }}</span>
            </div>
        </div>
        <div class="row">
            <div id="view-qrcode" class="d-md-block" style="margin: 2em auto;"></div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="form-group">
                    @if($cartao->fk_situacao!=1)
                        @if($cartao->fk_situacao==2)
                            <button type="submit" class="btn btn-danger ml-3 btn">Bloquear cartão</button>
                        @else
                            <button type="submit" class="btn btn-success ml-3 btn">Desbloquear cartão</button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </form>
    <!--<form id="form-cartao-delete" action="{{ url('admin/cartao/delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="id" value="{{ isset($cartao)? $cartao->id : '' }}">
    </form>
-->

@endsection

@section('scripts')
<script type="text/javascript" src="{{url('js/QRCode.js')}}"></script>
<script>
    new QRCode("view-qrcode", {
        text: document.getElementById('codigo').value,
        width: 100,
        height: 100,
        colorDark: "black",
        colorLight: "white",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>
@endsection