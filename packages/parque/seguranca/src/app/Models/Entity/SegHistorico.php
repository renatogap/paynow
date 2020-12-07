<?php

namespace Parque\Seguranca\App\Models\Entity;

use Parque\Seguranca\App\Models\LocalModelAbstract;

class SegHistorico extends LocalModelAbstract
{
    protected $table = 'seg_historico';
    public $timestamps = false;

    protected $casts = [
        'antes' => 'array',
        'depois' => 'array',
    ];
}
