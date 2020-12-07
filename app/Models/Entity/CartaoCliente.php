<?php

namespace App\Models\Entity;

class CartaoCliente extends AbstractModelSkeleton
{
    protected $table = "cartao_cliente";
    //public $timestamps = false;
    protected $guarded = [];

   

    public function situacao()
    {
        return $this->belongsTo(\App\Models\Entity\SituacaoCartao::class, 'status', 'id');
    }

}
