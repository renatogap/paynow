@extends('layouts.default')


@section('conteudo')
    <h4><span class="material-icons icone">payment</span> Novo Cartão</h4>
    <hr>

    <form method="post" action="{{ url('cartao/store') }}">
        {{ @csrf_field() }}

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
            <div class="col-md-6" style="margin: 0 auto;">
                <div class="form-group">
                    <label for="" class="col-md-12">Quantidade de cartões</label>
                    <div class="col-md-12">
                        <input type="tel" name="quantidade" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" style="margin: 0 auto;">
                <div class="form-group">
                    <button type="submit" class="btn btn-parque ml-3 btn">Gerar cartão</button>
                    <a href="{{url('cartao')}}" class="btn btn-secondary">Cancelar</a>
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

<!--<script src="{{ asset('js/jquery.min.js') }}"></script>-->


@endsection