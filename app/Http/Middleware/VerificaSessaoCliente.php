<?php

namespace App\Http\Middleware;

use Closure;

class VerificaSessaoCliente
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
        if(!session('cliente')) {
            return redirect('cliente')->with('error', 'Tempo expirado, por favor entre novamente.');
        }

        return $next($request);
    }
}
