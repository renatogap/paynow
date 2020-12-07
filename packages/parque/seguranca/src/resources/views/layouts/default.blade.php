<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!--
        <link rel="icon" sizes="16x16" type="image/png" href="{{ asset('images/android-icon-16x16.png') }}" type="imagem/png">
        <link rel="icon" sizes="36x36" type="image/png" href="{{ asset('images/android-icon-36x36.png') }}" type="imagem/png">
        <link rel="icon" sizes="46x46" type="image/png" href="{{ asset('images/android-icon-46x46.png') }}" type="imagem/png">
        <link rel="icon" sizes="56x56" type="image/png" href="{{ asset('images/android-icon-56x56.png') }}" type="imagem/png">
        <link rel="icon" sizes="66x66" type="image/png" href="{{ asset('images/android-icon-66x66.png') }}" type="imagem/png">
        <link rel="icon" sizes="76x76" type="image/png" href="{{ asset('images/android-icon-76x76.png') }}" type="imagem/png">
        <link rel="icon" sizes="86x86" type="image/png" href="{{ asset('images/android-icon-86x86.png') }}" type="imagem/png">
        <link rel="icon" sizes="160x160" type="image/png" href="{{ asset('images/android-icon-160x160.png') }}" type="imagem/png">
        <link rel="icon" sizes="196x196" type="image/png" href="{{ asset('images/android-icon-196x196.png') }}" type="imagem/png">
    -->
    
    <!--
        <link rel="apple-touch-icon" sizes="16x16" href="{{ asset('images/android-icon-16x16.png') }}" type="imagem/x-icon">
        <link rel="apple-touch-icon" sizes="36x36" href="{{ asset('images/android-icon-36x36.png') }}" type="imagem/png">
        <link rel="apple-touch-icon" sizes="46x46" href="{{ asset('images/android-icon-46x46.png') }}" type="imagem/png">
        <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('images/android-icon-96x96.png') }}" type="imagem/png">
        <link rel="apple-touch-icon" sizes="196x196" href="{{ asset('images/android-icon-196x196.png') }}" type="imagem/x-icon">
        <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/android-icon-128x128.png') }}" type="imagem/x-icon">
    -->
        
    <link rel="icon" sizes="96x96" type="image/png" href="{{ asset('images/android-icon-96x96.png') }}" type="imagem/png">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/android-icon-76x76.png') }}" type="imagem/png">

    <link rel="icon" sizes="16x16" href="{{ asset('images/firefox-icon-16x16.png') }}" type="imagem/png">
    <link rel="icon" sizes="32x32" href="{{ asset('images/firefox-icon-32x32.png') }}" type="imagem/png">
    <link rel="icon" sizes="48x48" href="{{ asset('images/firefox-icon-48x48.png') }}" type="imagem/png">
    <link rel="icon" sizes="90x90" href="{{ asset('images/firefox-icon-90x90.png') }}" type="imagem/png">
    <link rel="icon" sizes="128x128" href="{{ asset('images/firefox-icon-128x128.png') }}" type="imagem/png">
    <link rel="icon" sizes="192x192" href="{{ asset('images/firefox-icon-192x192.png') }}" type="imagem/png">

    <title>{{config('parque.nome')}}</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
{{--    <link href="{{asset('bootstrap/css/bootstrap-theme.min.css')}}" rel="stylesheet">--}}
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    @yield('cabecalho')
</head>

<body>
    <nav class="navbar navbar-inverse">
        <h3 class="hidden">Atalhos</h3>
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{url('')}}">
                    @if(config('app.env') == 'local')
                    <i class="glyphicon glyphicon-wrench" style="color: yellow"></i>
                    @else
                    <img class="pull-left" width="30px" src="{{url('images/logo.png')}}">
                    @endif
                    <span style="{{config('app.env') == 'local' ? 'color:yellow' : null }}">
                        {{config('parque.nome')}}
                    </span>
                </a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                @if(isset($menu))
                <ul class="nav navbar-nav">
                    <li><a><i class="glyphicon glyphicon-menu-right hidden-xs"></i></a></li>
                    @foreach($menu as $raiz) @if(isset($raiz->submenu))
                    @include('layouts.submenu', ['submenu' => $raiz])
                    @else
                    <li><a href="{{url($raiz->acao)}}">{{$raiz->nome}}</a></li>
                    @endif
                    @endforeach
                </ul>
                @endif
                <ul class="nav navbar-nav pull-right">
                    @if(\Illuminate\Support\Facades\Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="glyphicon glyphicon-cog"></i> {{\Illuminate\Support\Facades\Auth::user()->nome}}
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="{{url('seguranca/usuario/home')}}">Página inicial</a></li>
                            <li><a href="{{url('seguranca/usuario/alterar-senha')}}">Alterar senha</a></li>
                            <li><a href="{{url('seguranca/usuario/logout')}}">Sair</a></li>
                        </ul>
                    </li>
                    @else
                    <li><a href="{{url('seguranca/usuario')}}">Entrar</a></li>
                    @endif
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </nav>
    @yield('conteudo')
    <script src="{{asset('bootstrap/js/bootstrap-native-v4.min.js')}}"></script>
    <script>
        BASE_URL = "{{asset('')}}"
    </script>
    @if(config('app.env') === 'production')
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-40659860-1', 'auto');
        ga('send', 'pageview');
    </script>
    @endif @yield('scripts')
</body>

</html>