@extends('layouts.default')
@section('conteudo')
        <h3>
            <i class="material-icons icone">person</i> Área do Cliente
        </h3>
        <hr>
        
        <div class="row" style="padding: 3em 0 10em 0;">
            <div class="col-4 col-md-4 text-center">
                <a href="{{ url('cliente/pedidos') }}" class="col-4 pb-5" style="color: #333;">
                    <span class="material-icons" style="font-size: 4em; color: green;">receipt_long</span>
                    <div>
                        <strong>Ver meus pedidos</strong>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-4 text-center">
                <a href="{{ url('cliente/saldo') }}" class="col-4 pb-5" style="color: #333;">
                    <span class="material-icons" style="font-size: 4em; color: green;">attach_money</span>
                    <div>
                        <strong>Consultar saldo do cartão</strong>
                    </div>
                </a>
            </div>
            <div class="col-4 col-md-4 text-center">
                <a href="{{ url('cliente/cardapios') }}" class="col-4 pb-5" style="color: #333;">
                    <span class="material-icons" style="font-size: 4em; color: green;">receipt_long</span>
                    <div>
                        <strong>Ver cardápios</strong>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-12 text-center mt-2">
                <a href="{{ url('cliente/logout') }}" class="col-4 pb-5" style="color: #333;">
                    <span class="material-icons" style="font-size: 4em; color: green;">power_settings_new</span>
                    <div>
                        <strong>Sair</strong>
                    </div>
                </a>
            </div>
        </div>
@endsection