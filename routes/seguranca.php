<?php

/*
|--------------------------------------------------------------------------
| Rotas seguras
|--------------------------------------------------------------------------
|
| Cadastre aqui apenas as rotas que precisam de usuário logado para acessá-las
|
 */

Route::middleware(['tela-seguranca', 'acesso.log'])->group(function () {

    Route::get('admin/home', 'UsuarioLocalController@dashboard');

    Route::get('cartao', 'CartaoController@index');
    Route::get('cartao/create', 'CartaoController@create');
    Route::post('cartao/store', 'CartaoController@store');
    Route::get('cartao/edit/{codigo}', 'CartaoController@edit');
    Route::post('cartao/bloqueia-desbloqueia', 'CartaoController@bloqueiaDesbloqueia');

    Route::get('cartao/gerar-qrcode/{id}', 'CartaoController@gerarQrCode');
    Route::get('cartao/gerar-cartoes', 'CartaoController@gerarCartoes');

    Route::get('cartao-cliente', 'CartaoClienteController@index');
    Route::get('cartao-cliente/pesquisar', 'CartaoClienteController@pesquisar');
    Route::get('cartao-cliente/create/{codigo}', 'CartaoClienteController@create');
    Route::post('cartao-cliente/store', 'CartaoClienteController@store');
    Route::get('cartao-cliente/edit/{id}', 'CartaoClienteController@edit');
    Route::post('cartao-cliente/bloqueia-desbloqueia', 'CartaoClienteController@bloqueiaDesbloqueia');
    Route::post('cartao-cliente/devolver-cartao', 'CartaoClienteController@devolverCartao');
    Route::post('cartao-cliente/zerar-cartao', 'CartaoClienteController@zerarCartao');
    Route::get('cartao-cliente/leitor-transferencia', 'CartaoClienteController@leitorTransferencia');
    Route::get('cartao-cliente/dados-transferencia/{codigo}', 'CartaoClienteController@dadosTransferencia');
    Route::get('cartao-cliente/salvar-transferencia', 'CartaoClienteController@salvarTransferencia');
    Route::get('cartao-cliente/confirma-transferencia', 'CartaoClienteController@confirmaTransferencia');
    

    Route::get('cartao-cliente/leitor', 'CartaoClienteController@leitorCartao'); 
    Route::get('cartao-cliente/add-credito/{cartao}', 'CartaoClienteController@addCredito');   
    Route::post('cartao-cliente/salvar-credito', 'CartaoClienteController@salvarCredito');
    Route::get('cartao-cliente/confirma-credito', 'CartaoClienteController@confirmaCredito');
    Route::get('cartao-cliente/localizar-cartao/{codigo}', 'CartaoClienteController@localizarCartao');
    Route::get('cartao-cliente/transferir-credito/{codigo}', 'CartaoClienteController@transferirCredito');

    
    
    //Cardápio ADM
    Route::get('cardapio', 'CardapioController@index');
    Route::get('cardapio/create', 'CardapioController@create');
    Route::get('cardapio/edit/{id}', 'CardapioController@edit');
    Route::post('cardapio/store', 'CardapioController@store');
    Route::get('cardapio/ver-foto/{id}', 'CardapioController@verFoto');
    Route::get('cardapio/ver-thumb/{id}', 'CardapioController@verThumb');
    Route::post('cardapio/salvar-categoria', 'CardapioController@salvarCategoria');
    Route::get('cardapio/tipo-cardapio', 'CardapioController@tipoCardapio');
    Route::get('cardapio/tipo-cardapio/thumb/{id}', 'CardapioController@verThumbTipoCardapio');

    Route::post('cardapio/salvar-tipo-cardapio', 'CardapioController@salvarTipoCardapio');
    Route::post('cardapio/ativar-item', 'CardapioController@ativarItem');
    Route::post('cardapio/inativar-item', 'CardapioController@inativarItem');
    Route::get('cardapio/delete/{id}', 'CardapioController@deletarCardapio');


    //Cardápio PDV
    Route::get('pedido/cardapios', 'PedidoController@cardapios');
    Route::get('pedido/cardapio/{id_tipo_cardapio}', 'PedidoController@cardapio');
    Route::get('pedido/cardapio/item/{id}', 'PedidoController@pedidoItem');
    Route::post('pedido/cardapio/add-pedido-cliente', 'PedidoController@addPedidoCliente');
    Route::get('pedido/confirmar-pedido', 'PedidoController@confirmarPedido');
    Route::get('pedido/finalizar/leitor', 'PedidoController@leitor');
    Route::get('pedido/finalizar/{codigo}', 'PedidoController@finalizarPedido');

    Route::get('pedido/visualizacao-gerente', 'PedidoController@visualizacaoGerente');
    Route::get('pedido/cancelar', 'PedidoController@cancelar');


    //Pedido
    Route::get('pedido/meus-pedidos', 'PedidoController@meusPedidos');
    Route::get('pedido/historico-pedidos/{mesa}', 'PedidoController@historicoPedidos');
    Route::get('pedido/historico-pedido/{id_pedido}/{tipo}', 'PedidoController@historicoPedido');
    Route::get('pedido/historico-pedido-gerente/{id_pedido}/{tipo}', 'PedidoController@historicoPedidoGerente');
    Route::get('pedido/confirmar-cancelamento/{item}/{id_tipo_cardapio}', 'PedidoController@confirmarCancelamento');
    Route::post('pedido/cancelar/{item}/{id_tipo_cardapio}', 'PedidoController@cancelarItem');

    Route::get('pedido/confirmar-cancelamento-gerente/{item}/{id_tipo_cardapio}', 'PedidoController@confirmarCancelamentoGerente');
    Route::get('pedido/confirmar-cancelamento-gerente2/{item}/{codigo}', 'PedidoController@confirmarCancelamentoGerente2');
    Route::post('pedido/cancelar-gerente/{item}/{id_tipo_cardapio}', 'PedidoController@cancelarItemGerente');
    Route::post('pedido/cancelar-gerente2/{item}/{codigo}', 'PedidoController@cancelarItemGerente2');

    Route::get('pedido/confirmar-entrega/{id_pedido}/{tipo}', 'PedidoController@confirmarEntrega');
    Route::post('pedido/salvar-entrega/{id_pedido}/{tipo}', 'PedidoController@salvarEntrega');

    Route::get('pedido/confirmar-entrega-gerente/{id_pedido}/{tipo}', 'PedidoController@confirmarEntregaGerente');
    Route::post('pedido/salvar-entrega-gerente/{id_pedido}/{tipo}', 'PedidoController@salvarEntregaGerente');
    
    
    //Cozinha
    Route::get('cozinha/monitor', 'CozinhaController@monitor');
    Route::get('cozinha/confirma/{id_pedido}', 'CozinhaController@confirma');
    Route::post('cozinha/pedido-pronto/{id_pedido}', 'CozinhaController@pedidoPronto');



    //Estoque
    Route::get('estoque', 'EstoqueController@index');
    Route::get('estoque/get-estoque-item/{id}', 'EstoqueController@getEstoqueItem');
    Route::post('estoque/store', 'EstoqueController@store');
    Route::get('estoque/relatorio', 'EstoqueController@impressao');


    
    //Relatórios
    Route::get('relatorios', 'RelatoriosController@index');
    Route::get('relatorio/resumo/pdv', 'RelatoriosController@resumoPdv');
    Route::get('relatorio/detalhado/pdv', 'RelatoriosController@detalhadoPdv');
    Route::get('relatorio/taxa-servico', 'RelatoriosController@taxaServico');
    Route::get('relatorio/fechamento-caixa', 'RelatoriosController@fechamentoCaixa');
    Route::get('relatorio/consultar-pedidos', 'RelatoriosController@consultarPedidos');
    Route::get('relatorio/fechamento-conta/{codigo}', 'RelatoriosController@fechamentoConta');
    Route::get('relatorio/devolucao-cartoes', 'RelatoriosController@devolucaoCartoes');
    Route::get('relatorio/cancelamento', 'RelatoriosController@cancelamento');


    //administração de usuários locais (feita por usuário comum)
    Route::get('admin/usuario', 'UsuarioLocalController@index');
    Route::get('admin/usuario/grid', 'UsuarioLocalController@grid');
    Route::get('admin/usuario/criar', 'UsuarioLocalController@criar');
    Route::post('admin/usuario/store', 'UsuarioLocalController@store');
    Route::get('admin/usuario/editar/{usuario}', 'UsuarioLocalController@editar');
    Route::post('admin/usuario/update', 'UsuarioLocalController@update');
    Route::post('admin/usuario/excluir/{usuario}', 'UsuarioLocalController@excluir');
    Route::post('admin/usuario/info', 'UsuarioLocalController@info');
    Route::post('admin/usuario/reativar/{usuario}', 'UsuarioLocalController@reativar');

    //administração de perfis (feita por usuário comum)
    Route::get('admin/perfil', 'PerfilController@index');
    Route::get('admin/perfil/grid', 'PerfilController@grid');
    Route::get('admin/perfil/novo', 'PerfilController@novo');
    Route::post('admin/perfil/store', 'PerfilController@store');
    Route::get('admin/perfil/editar/{perfil}', 'PerfilController@editar');
    Route::post('admin/perfil/update', 'PerfilController@update');
    Route::post('admin/perfil/excluir/{perfil}', 'PerfilController@destroy');

    //administração de solicitação de login
    Route::get('admin/solicitar-acesso', '\Parque\Seguranca\App\Http\Controllers\SolicitacaoAcessoController@index');
    Route::get('admin/solicitar-acesso/grid', '\Parque\Seguranca\App\Http\Controllers\SolicitacaoAcessoController@grid');
    Route::get('admin/solicitar-acesso/editar/{id}', '\Parque\Seguranca\App\Http\Controllers\SolicitacaoAcessoController@editar')->where('id', '\d+');
    Route::post('admin/solicitar-acesso/store', '\Parque\Seguranca\App\Http\Controllers\SolicitacaoAcessoController@store');
    Route::post('admin/solicitar-acesso/excluir', '\Parque\Seguranca\App\Http\Controllers\SolicitacaoAcessoController@excluir');

});
