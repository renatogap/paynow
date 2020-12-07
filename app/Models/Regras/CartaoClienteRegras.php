<?php

namespace App\Models\Regras;

use App\Models\Entity\Cartao;
use App\Models\Entity\CartaoCliente;
use App\Models\Entity\EntradaCredito;
use Illuminate\Support\Facades\Auth;

class CartaoClienteRegras
{
    public static function salvar(\stdClass $p)
    {
        if(!isset($p->id)) {

            $cartaoCliente = CartaoCliente::create([
                'fk_cartao' => $p->id_cartao,
                'nome' => $p->nome,
                'cpf' => $p->cpf ? preg_replace('/[^0-9]/', '', $p->cpf) : null,
                'telefone' => $p->telefone ?? null,
                'fk_tipo_cliente' => $p->tipo,
                'fk_tipo_pagamento' => $p->tipo_pagamento,
                'valor_atual' => formatarMoeda(($p->valor - $p->valorCartao)),
                'valor_cartao' => formatarMoeda($p->valorCartao),
                'observacao' => $p->observacao ?? null,
                'devolvido' => 'N',
                'status' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);

            EntradaCredito::create([
                'fk_cartao_cliente' => $cartaoCliente->id,
                'valor' => formatarMoeda($p->valor),
                'fk_tipo_pagamento' => $p->tipo_pagamento,
                'observacao' => 'Crédito de entrada do cliente',
                'data' => date('Y-m-d H:i:s'),
                'fk_usuario' => Auth::user()->id
            ]);

            Cartao::where('id', $p->id_cartao)->update(['fk_situacao' => 2]);
        }else {
            $cartaoCliente = CartaoCliente::find($p->id);
            $cartaoCliente->nome = $p->nome;
            $cartaoCliente->cpf = $p->cpf ? preg_replace('/[^0-9]/', '', $p->cpf) : null;
            $cartaoCliente->telefone = $p->telefone ?? null;
            $cartaoCliente->fk_tipo_cliente = $p->tipo;
            $cartaoCliente->fk_tipo_pagamento = $p->tipo_pagamento;
            $cartaoCliente->observacao = $p->observacao ?? null;
            $cartaoCliente->updated_at = date('Y-m-d H:i:s');
            $cartaoCliente->save();
        }
    }

}