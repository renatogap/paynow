<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <title>{{ config('parque.slogan') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="{{ url('images/android-icon-96x96.png') }}">
	<link rel="apple-touch-icon" sizes="76x76" href="{{ url('images/apple-icon-76x76.png') }}">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('iconfont/material-icons.css') }}">
    <style>
        html, body {
            height: 100%;
            background: {{ config('parque.background') }};
            width: 100%;
        }

        .container-fluid {
            background: #ffffff;
            /* margin-top: 4.5em; */
            padding-top: 1em;
            padding-bottom: 1em;
            border-radius: 5px;
            width: 96%;
            text-shadow: 5px 5px 5px rbga(0,0,0,0.5); 
            box-shadow: 5px 5px 5px rgba(0,0,0,0.5);
        }
        .bg-dark {
            background: {{ config('parque.background') }};
        }

        .btn-parque {
            background: {{ config('parque.btn-parque') }};
            color: white;
        }

        .btn-parque:hover {
            background: {{ config('parque.btn-parque-hover') }};
            color: white;
        }

        .btn-secondary {
            color: #333;
            background: {{ config('parque.btn-secondary') }};
        }
        .btn-secondary:hover {
            background: #ccc !important;
            color: #333;
        }

        form label {font-weight: bold;}   
        

        .icone {
            font-size: 1.2em !important;
            display: inline-flex;
            vertical-align: top;
        }

        .btn-flutuante {
            /*background: #033328;
            border: 1px solid #033328;*/
            position: fixed;
            float: bottom;
            bottom: 15px;
            right: 15px;
            z-index: 100;
            font-size: 30px;
            padding: 15px 20px 15px 22px;
        }

        .btn-circulo {
            border-radius: 50px;
            -webkit-box-shadow: 9px 7px 5px rgba(50, 50, 50, 0.77);
            -moz-box-shadow: 9px 7px 5px rgba(50, 50, 50, 0.77);
            box-shadow: 9px 7px 5px rgba(50, 50, 50, 0.77);
        }

        .navbar-brand {
            background: none !important;
        }
        
    </style>
    <script> var BASE_URL = "{{url('')}}/"; </script>

    @yield('cabecalho')
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark">
        <!--<button class="navbar-toggler" onclick="clickBtnMenuLateral(this)" type="button" data-toggle="collapse" data-target="#navMenuLateral" aria-controls="navMenuLateral" aria-expanded="false" aria-label="Toggle navigation">
            <i class="material-icons icone" style="font-size: 33px !important; margin-top: 0px; padding: 0 0 0;">expand_more</i>
        </button>-->
        <a class="navbar-brand" href="{{url('')}}">
            <i class="material-icons icone" style="font-size: 1.5em !important;">home</i> <b>PayNow</b>
            <!--<b>{{ config('parque.nome') }}</b>-->
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            

            <ul class="navbar-nav ml-auto">
                @if(!auth()->user())
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('seguranca/usuario') }}">Entrar</a>
                </li>
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ auth()->user()->nome }}</a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown01">
                        <a class="dropdown-item" href="{{ url('seguranca/usuario/home') }}">Página inicial</a>
                        <a class="dropdown-item" href="{{ url('seguranca/usuario/alterar-senha') }}">Alterar senha</a>
                        <a class="dropdown-item" href="{{ url('seguranca/usuario/logout') }}">Sair</a>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    <div id="container-fluid" class="container-fluid col-sm-12 col-md-8 mb-5">
        @yield('conteudo')

        
        @if(request()->session()->exists('pedido'))
            <a href="{{ url('pedido/confirmar-pedido') }}" class="btn btn-primary btn-lg pull-right btn-circulo btn-flutuante" title="Finalizar pedido">
                <i class="material-icons">local_grocery_store</i>
                <div style="margin-top: 0; font-size: 20px; font-weight: bold;">{{ COUNT(request()->session()->get('pedido')) }}</div>
            </a>            
        @endif
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>

    @yield('scripts')
    
</body>

</html>