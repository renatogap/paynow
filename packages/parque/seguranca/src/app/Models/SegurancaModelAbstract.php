<?php

namespace Parque\Seguranca\App\Models;

class SegurancaModelAbstract extends AbstractModel
{
    //protected $schema;
    protected $connection = 'mysql';

    public function __construct()
    {
        $conexao = config('connections.mysql');
        Historico::getInstance()->addConnection($conexao);//este é o apeliado criado em /config/database.php
    }
}
