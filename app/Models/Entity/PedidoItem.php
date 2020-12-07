<?php

namespace App\Models\Entity;

class PedidoItem extends AbstractModelSkeleton
{
    protected $table = "pedido_item";
    protected $guarded = [];

    public function pedido()
    {
        return $this->belongsTo(\App\Models\Entity\Pedido::class, 'fk_pedido', 'id');
    }

    public function cardapio()
    {
        return $this->belongsTo(\App\Models\Entity\Cardapio::class, 'fk_item_cardapio', 'id');
    }
}
