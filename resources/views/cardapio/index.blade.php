@extends('layouts.default')

<style>
   
</style>

@section('conteudo')
    <h5>
        Gerenciar Cardápio
        <a href="{{url('cardapio/create')}}" class="btn btn-primary btn-circulo btn-flutuante">
            <span class="material-icons icone" style="font-size: 2em;">receipt_long</span>
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


    @if(count($myCardapio) > 0)
        <div class="list-group">

            <?php $key = 1; ?>

            @foreach($myCardapio as $tipo => $categorias)

                <h4 onclick="detalhesCardapio(<?= $key ?>)" class="mt-0 mb-0 text-center p-3 text-white" style="background: #295233; font-size: 17px; font-weight: bold; border-radius: 5px; cursor: pointer;">
                    {{ $key }}. {{ $tipo }}
                </h4>

                <div id="detalhes_cardapio_<?= $key ?>" style="display: none;">
                
                    @foreach($categorias as $categoria => $itens)
                        <a href="#" class="list-group-item bg-success" style="cursor: default;">
                            <div style="text-align: center; color: #666;">
                                <span style="font-size: 16px; color: #fff;"><strong>{{ $categoria }}</strong></span>
                            </div>
                        </a>

                        @foreach($itens as $item)
                            <a href="{{ url('cardapio/edit/'.$item->id) }}" class="list-group-item">
                              
                                <div style="color: #666;">
                                    <span style="float: right; color: #666; font-size: 13px; font-weight: bold;">
                                        R$ {{ $item->valor }}
                                    </span>
                                    <span style="font-size: 14px;">
                                        <span class="badge {{ $item->status == 1 ? 'badge-success' : 'badge-danger' }}" style="height: 8px; float: left; margin-left: -1.2em; margin-top: 5px;"> </span>
                                        <strong>{{ $item->nome_item }}</strong>
                                    </span>
                                    <br>
                                    
                                    @if($item->detalhe_item)
                                        {{ $item->detalhe_item }}
                                        <br>
                                    @endif
                                </div>
                            </a>

                        @endforeach

                    @endforeach

                </div>
                <br />

                <?php $key++; ?>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">Nenhum registro encontrado.</div>
    @endif

@endsection


@section('scripts')
<script>
    function detalhesCardapio(key) {
        if(!$('#detalhes_cardapio_'+key).hasClass('show')){
            $('#detalhes_cardapio_'+key).fadeIn();
            $('#detalhes_cardapio_'+key).addClass('show');
        }else {
            $('#detalhes_cardapio_'+key).fadeOut();
            $('#detalhes_cardapio_'+key).removeClass('show');
        }
    }
</script>
@endsection