@extends('layouts.default')
@section('conteudo')
    <h3>
        <i class="material-icons icone">person</i> Área do Cliente
    </h3>
    <hr>
    <div style="color: #777; margin-bottom: 1em;">Consulte aqui seus pedidos e seu saldo em créditos no cartão</div>

    @if (session('error'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {!! session('error') !!}
        </div>
    @endif

    <form method="post" action="{{url('cliente/login')}}">
    {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                <label for="">CPF</label>
                <input type="tel" name="cpf" id="cpf" class="form-control form-control-lg" maxlength="14" onkeydown="fMasc(this, mCPF);">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                <button type="submit" class="btn btn-parque btn-block btn-lg">Entrar</button>
                </div>
            </div>
        </div>
        <br><br>
    </form>

@endsection

@section('scripts')

<script>    
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