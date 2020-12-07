<?php

namespace Parque\Seguranca\App\Models;

class LocalModelAbstract extends AbstractModel
{
    protected $schema;
    protected $connection = 'mysql';

    public function __construct()
    {
        $conexao = config('connections.mysql'); //este é o apelido criado em /config/database.php
        Historico::getInstance()->addConnection($conexao);
    }
}
