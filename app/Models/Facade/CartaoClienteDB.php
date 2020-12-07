<?php

namespace App\Models\Facade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Parque\Seguranca\App\Models\Entity\Usuario;
use Parque\Seguranca\App\Models\Entity\UsuarioSistema;
use stdClass;

class CartaoClienteDB extends Model
{
    public function pesquisa($p)
    {
        return DB::table('cartao_cliente as cc')
                ->join('tipo_cliente as tc', 'tc.id', '=', 'cc.fk_tipo_cliente')
                ->join('cartao as c', 'c.id', '=', 'cc.fk_cartao')   
                ->join('situacao_cartao as s', 's.id', '=', 'c.fk_situacao') 
                ->select([
                    'cc.id', 
                    'cc.nome', 
                    'cc.cpf', 
                    'cc.valor_atual', 
                    'tc.nome as tipo', 
                    DB::raw("date_format(cc.created_at, '%d/%m/%Y %H:%i') as data"), 
                    //'cc.status',
                    DB::raw("CASE WHEN c.fk_situacao = 1 THEN '<span class=\"badge badge-info\">DISPONÍVEL NO CAIXA</span>'
                                WHEN c.fk_situacao = 2 THEN '<span class=\"badge badge-success\">EM USO</span>'
                                WHEN c.fk_situacao = 3 THEN '<span class=\"badge badge-danger\">BLOQUEADO</span>'
                                WHEN c.fk_situacao = 4 THEN '<span class=\"badge badge-danger\">PERDIDO</span>'
                            END AS status_desc")
                ])
                ->where(DB::raw("date_format(cc.created_at, '%Y-%m-%d')"), date('Y-m-d'))
                ->get();
    }

    public static function grid($request)
    {
        $sql = DB::table('cartao_cliente as cc')
                //>join('tipo_cliente as tc', 'tc.id', '=', 'cc.fk_tipo_cliente')
                ->join('cartao as c', 'c.id', '=', 'cc.fk_cartao')   
                ->join('situacao_cartao as s', 's.id', '=', 'cc.status')             
                ->select([
                    'cc.id', 
                    'cc.nome', 
                    'cc.cpf', 
                    'cc.valor_atual', 
                    //'tc.nome as tipo', 
                    DB::raw("date_format(cc.created_at, '%d/%m/%Y %H:%i') as data"), 
                    //'s.nome as status_desc'
                    DB::raw("CASE WHEN cc.status = 1 THEN '<span class=\"badge badge-info\">DEVOLVIDO</span>'
                                WHEN cc.status = 2 THEN '<span class=\"badge badge-success\">EM USO</span>'
                                WHEN cc.status = 3 THEN '<span class=\"badge badge-danger\">BLOQUEADO</span>'
                            END AS status_desc")
                ]);

        if(!$request->data && !$request->cpf && !$request->nome) {
            $sql->where(DB::raw("date_format(cc.created_at, '%Y-%m-%d')"), date('Y-m-d'));
        }
        else if($request->data) {
            $sql->where(DB::raw("date_format(cc.created_at, '%Y-%m-%d')"), $request->data);
        }

        if($request->nome) {
            $sql->where('cc.nome', 'LIKE', "%{$request->nome}%");
        }

        if($request->cpf) {
            $sql->where('cc.cpf', preg_replace('/[^0-9]/', '', $request->cpf));
        }

        $sql->orderBy('cc.created_at', 'DESC');

        return $sql->get();
    }

    public static function extratoCartaoCliente($id_cartao_cliente)
    {
        $sql1 = DB::table('entrada_credito as e')
            ->join('tipo_pagamento as tp', 'tp.id', '=', 'e.fk_tipo_pagamento')
            //->join('usuario as u', 'u.id', '=', 'e.fk_usuario')
            ->select(['e.observacao', 'e.data', 'tp.nome as tipo_pagamento', 'e.valor'])
            ->where('e.fk_cartao_cliente', $id_cartao_cliente)
            ->orderBy('e.id');



        $sql2 = DB::table('saida_credito as s')
            //->join('usuario as u', 'u.id', '=', 's.fk_usuario')
            ->select(['s.observacao', 's.data', DB::raw("'DINHEIRO' as tipo_pagamento"), DB::raw("(-1 * s.valor) as valor")])
            ->where('s.fk_cartao_cliente', $id_cartao_cliente)
            ->orderBy('s.id');


        $dados = $sql1->union($sql2)->orderBy('data')->get();

        return $dados;
    }
}
