@extends('layouts.default')

@section('conteudo')
    <h5>Gerar Impressão de Cartões
        <a href="{{url('cartao')}}" class="material-icons float-right" style="font-size: 1.3em; color: #333;">
            keyboard_backspace
        </a>
    </h5>
    <hr>

    <form method="post" action="{{ url('cartao/gerar-cartoes') }}">
        {{ @csrf_field() }}

        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="action" value="{{ $action }}">

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

        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-12">Nome do Cardápio</label>
                <div class="col-md-12">
                    <input name="tipo" id="tipo" value="{{ old('tipo') ?? '' }}" class="form-control">
                </div>
            </div>
        </div>
        
       
        <div class="row">
            <div class="form-group">
                <label for="" class="col-md-12">Foto</label>
                <div class="col-md-12">
                    <input type="file" name="foto[]" id="foto" multiple accept="image/*" class="form-control">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="form-group">
                <button type="submit" class="btn btn-parque ml-3 btn">Salvar</button>
                <button type="reset" onclick="limparForm()" class="btn btn-secondary">Limpar</button>
            </div>
        </div>
    </form>

    @if($tiposCardapios->count() > 0)
        <table class="table table-hover" width="100%">
            <tr bgColor="#eee">
                <th colspan="3" style="text-align: center;">Tipos de Cardápio</th>
            </tr>            
            @foreach($tiposCardapios as $item)
                <tr>
                    <td width="5%" class="p-0 pt-1 pb-1">
                        <img width="50px" src="{{ url('cardapio/tipo-cardapio/thumb/'.$item->id) }}" onerror="this.src='<?= url('images/foto-error.png') ?>'" alt="">
                    </td>
                    <td class="p-0 pt-1 pl-1"><b>{{ $item->nome }}</b></td>
                    <td width="1%"><div class="btn btn-primary btn-sm" onclick="editar({{$item->id}}, '{{ $item->nome }}')"><i class="material-icons icone">edit</i></div></td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info">Nenhum registro encontrado</div>
    @endif
@endsection

@section('scripts')
<script>
    function editar(id, nome) {
        document.getElementById('id').value = id;
        document.getElementById('tipo').value = nome;

        //document.body.scrollTop = 0; // For Safari
        //document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        $('html, body').animate({scrollTop:0}, '300');
    }

    function limparForm() {
        document.getElementById('id').value = '';
        document.getElementById('tipo').value = '';
        document.getElementById('foto').value = '';
    }
</script>
@endsection
