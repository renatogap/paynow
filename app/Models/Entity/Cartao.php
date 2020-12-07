<?php

namespace App\Models\Entity;

class Cartao extends AbstractModelSkeleton
{
    protected $table = "cartao";
    public $timestamps = false;
    protected $guarded = [];

    public function situacao()
    {
        return $this->belongsTo(\App\Models\Entity\SituacaoCartao::class, 'fk_situacao', 'id');
    }

}
