<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Parque\Seguranca\App\Models\Entity\SegHistorico;
use Parque\Seguranca\App\Models\Historico;

class AcessoLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
    {
//        $oUsuario = Auth::user();

        //ip do usuario
        $ip = $request->ip();

        //pega a rota da url acessada
        $url = Route::getFacadeRoot()->current()->uri();

        $acao = DB::table("seg_acao")
            ->where('nome', 'LIKE', $url)
            ->where('log_acesso', '=', true)
            ->first();

        //verifica se a rota acessada necessita a gravação do log de acesso
        if (!empty($acao)) {

            $oHistorico = Historico::getInstance();
            $oHistorico->read(Route::getFacadeRoot()->current()->parameters);
            $oHistorico->commitAndClean();

        }

        return $next($request);
    }
}
