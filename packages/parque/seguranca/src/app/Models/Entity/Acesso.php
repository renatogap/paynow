<?php

namespace Parque\Seguranca\App\Models\Entity;

use Parque\Seguranca\App\Models\SegurancaModelAbstract;

class Acesso extends SegurancaModelAbstract
{
    public $timestamps = false;
    public $table = 'acesso';

    /**
     * Acesso constructor.
     */
    public function __construct()
    {
        $this->table = "$this->table";
    }


}
