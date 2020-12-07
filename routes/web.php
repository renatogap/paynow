<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', '\Parque\Seguranca\App\Http\Controllers\UsuarioController@index');
//Route::post('seguranca/usuario/login', '\Parque\Seguranca\App\Http\Controllers\UsuarioController@login');

Route::get('cadastro', '\Parque\Seguranca\App\Http\Controllers\FormularioAcessoController@passo1');
Route::post('cadastro/passo2', '\Parque\Seguranca\App\Http\Controllers\FormularioAcessoController@passo2');
Route::post('cadastro/store', '\Parque\Seguranca\App\Http\Controllers\FormularioAcessoController@store');


//Cliente
Route::get('cliente', 'ClienteController@index'); //Publico
Route::get('cliente/login/{codigo}', 'ClienteController@login'); //Publico


Route::get('cardapio/tipo-cardapio/thumb/{id}', 'CardapioController@verThumbTipoCardapio'); //Publico


Route::middleware(['session.cliente'])->group(function () {
    Route::get('cliente/home', 'ClienteController@home');
    Route::get('cliente/pedidos', 'ClienteController@pedidos');
    Route::get('cliente/cardapios', 'ClienteController@cardapios');
    Route::get('cliente/cardapio/ver-foto/{id}', 'ClienteController@verFoto');
    Route::get('cliente/cardapio/ver-thumb/{id}', 'ClienteController@verThumb');
    Route::get('cliente/cardapio/{id_tipo_cardapio}', 'ClienteController@cardapio');
    Route::get('cliente/cardapio/item/{id}', 'ClienteController@pedidoItem');
    Route::get('cliente/saldo', 'ClienteController@saldo');
    Route::get('cliente/logout', 'ClienteController@logout');
});


