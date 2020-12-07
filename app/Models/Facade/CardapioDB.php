<?php

namespace App\Models\Facade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CardapioDB extends Model
{
    public static  function pesquisar($id_tipo_cardapio = null)
    {
        $db = DB::table('cardapio as c')
            ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
            ->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
            ->select([
                'ct.nome as tipo_cardapio',
                'cc.nome as categoria',
                'c.*'
            ])
            ->where('c.status', 1);

        if(!is_array($id_tipo_cardapio)){
            $db->where('c.fk_tipo_cardapio', $id_tipo_cardapio);
        }else {
            $db->whereIn('c.fk_tipo_cardapio', $id_tipo_cardapio);
        }
        
        $cardapio = $db->get();

        $myItems = [];
        $myCardapio = [];

        foreach($cardapio as $i => $c) {
            $myItems[$c->tipo_cardapio][] = $c;
        }

        foreach($myItems as $tipo => $itens) {
            foreach($itens as $i => $item){
                $myCardapio[$tipo][$item->categoria][] = $item;
            }
        }

        return $myCardapio;
    }

    public static  function pesquisarAdmin($id_tipo_cardapio = null)
    {
        $db = DB::table('cardapio as c')
            ->join('cardapio_tipo as ct', 'ct.id', '=', 'c.fk_tipo_cardapio')
            ->join('cardapio_categoria as cc', 'cc.id', '=', 'c.fk_categoria')
            ->select([
                'ct.nome as tipo_cardapio',
                'cc.nome as categoria',
                'c.*'
            ]);
            //->where('c.status', 1);

        if(!is_array($id_tipo_cardapio)){
            $db->where('c.fk_tipo_cardapio', $id_tipo_cardapio);
        }else {
            $db->whereIn('c.fk_tipo_cardapio', $id_tipo_cardapio);
        }
        
        $cardapio = $db->get();

        $myItems = [];
        $myCardapio = [];

        foreach($cardapio as $i => $c) {
            $myItems[$c->tipo_cardapio][] = $c;
        }

        foreach($myItems as $tipo => $itens) {
            foreach($itens as $i => $item){
                $myCardapio[$tipo][$item->categoria][] = $item;
            }
        }

        return $myCardapio;
    }
    
}
