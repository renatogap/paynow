<?php

namespace Parque\Seguranca\App\Models\Exception;

class PreCadastroNaoEncontradoException extends \Exception
{
    public function __construct($message = 'Cadastro não encontrado')
    {
        parent::__construct($message);
    }
}
