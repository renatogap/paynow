<?php

return [
    'nome' => 'Parque dos Igarapés', //nome do sistema
    'codigo' => 1, //código deste sistema na tabela de produção "sistema" no schema segurança. Não deixe como 35 no seu projeto
    'dashboard' => 'admin/home', //aqui pode ser um endereço da que decidirá qual será a página inicial do usuário
    'favicon' => url('images/favicon.ico'),
    'logo' => url('images/logo-parque.jpg'),
    'slogan' => 'PayNow',
    'expiracao_login' => 86400, //tempo em dias para usuário perder acesso ao sistema por falta de uso

    'limite_devolucao' => 70,
    'valor_cartao' => '5.00',

    'background' => '#8bb315 !important',
    'btn-parque' => '#004735 !important',
    'btn-parque-hover' => '#033328 !important',
    'btn-secondary' => '#e3e0e0 !important'
];
