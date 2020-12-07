<?php

namespace Parque\Seguranca\App\Http\Controllers;

use App\Http\Controllers\Controller;

class ConfiguracaoController extends Controller
{
    public function index()
    {
        return view('Seguranca::configuracao.index');
    }
}
