<?php

namespace App\Models\Entity;

class Cardapio extends AbstractModelSkeleton
{
    protected $table = "cardapio";
    protected $guarded = [];

    public function tipo()
    {
        return $this->belongsTo(\App\Models\Entity\CardapioTipo::class, 'fk_tipo_cardapio', 'id');
    }

    public function categoria()
    {
        return $this->belongsTo(\App\Models\Entity\CardapioCategoria::class, 'fk_categoria', 'id');
    }

}
