<?php

namespace App\Models\Entity;

use Parque\Seguranca\App\Models\AbstractModel;
use Parque\Seguranca\App\Models\Historico;

abstract class AbstractModelSkeleton extends AbstractModel
{
    protected $connection = 'mysql';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $conexao = config("connections.$this->connection"); //este é o apelido criado em /config/database.php
        Historico::getInstance()->addConnection($conexao);
    }
}
