<?php

namespace Parque\Seguranca\App\Models\Entity;

use Parque\Seguranca\App\Models\SegurancaModelAbstract;

class UsuarioSistema extends SegurancaModelAbstract
{
    protected $table = 'usuario_sistema';
    public $timestamps = false;
}
