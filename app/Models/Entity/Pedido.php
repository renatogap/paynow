<?php

namespace App\Models\Entity;

class Pedido extends AbstractModelSkeleton
{
    protected $table = "pedido";
    protected $guarded = [];

    public function itens()
    {
        return $this->hasMany(\App\Models\Entity\PedidoItem::class, 'fk_pedido', 'id');
    }
}
