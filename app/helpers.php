<?php

use App\Models\Entity\CardapioTipo;
use App\Models\Entity\UsuarioTipoCardapio;
use Illuminate\Support\Facades\Auth;
use Parque\Seguranca\App\Models\Entity\SegGrupo;

if (!function_exists('getTiposDeCardapio')) {
     function getTiposDeCardapio()
     {
        $perfilUsuario = SegGrupo::where('usuario_id', Auth::user()->id)->get()->pluck('perfil_id')->toArray();
        
        if(in_array(4, $perfilUsuario) || in_array(5, $perfilUsuario)) {
            $tiposCardapios = UsuarioTipoCardapio::where('fk_usuario', Auth::user()->id)->get()->pluck('fk_tipo_cardapio')->toArray();
        }else {
            $tiposCardapios = CardapioTipo::orderBy('nome')->get()->pluck('id')->toArray();
        }

        return $tiposCardapios;
     }
 }

 if (!function_exists('arredondar')) {
    function arredondar($valorTotalPedido)
    {
        $taxaServico = ($valorTotalPedido * (10 / 100));

        $arr = explode('.', $taxaServico);

        if(count($arr) > 1) {

            $arr[1] = (strlen($arr[1]) < 2) ? intval($arr[1].'0') : $arr[1];

            if($arr[1] >= 51) {
                $taxaServico = ($arr[0] + 1);
            }else {
                $taxaServico = $arr[0];
            }
        }

       return $taxaServico;
    }
}


if (!function_exists('getQuantidadeGarrafas')) {
    function getQuantidadeGarrafas($qtd, $dose, $tipo)
    {
        $qtdGarrafas = $qtd / $dose; 

        $aGarrafas = explode(',', $qtdGarrafas);

        if(isset($aGarrafas[1]) && $aGarrafas[1] > 0) {
            $qtdGarrafas = $aGarrafas[0] + 1;
        }else {
            $qtdGarrafas = $aGarrafas[0];
        }

        if($tipo == 'E'){
            $quantidade = $qtdGarrafas;
        }else {
            $quantidade = $qtd;
        }       

       return $quantidade;
    }
}