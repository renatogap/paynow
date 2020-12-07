<!doctype html>
<html lang="{{ config('app.locale') }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
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

    <title>{{config('parque.nome')}} - Polícia Civil do Pará</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    @yield('cabecalho')


    <style>
        /*.container {*/
        /*    width: auto;*/
        /*    max-width: 680px;*/
        /*    padding: 0 15px;*/
        /*}*/

        .footer {
            background-color: #f5f5f5;
        }

        /*.bd-placeholder-img {*/
        /*    font-size: 1.125rem;*/
        /*    text-anchor: middle;*/
        /*    -webkit-user-select: none;*/
        /*    -moz-user-select: none;*/
        /*    -ms-user-select: none;*/
        /*    user-select: none;*/
        /*}*/

        /*@media (min-width: 768px) {*/
        /*    .bd-placeholder-img-lg {*/
        /*        font-size: 3.5rem;*/
        /*    }*/
        /*}*/
    </style>
</head>

<body class="d-flex flex-column h-100">
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="{{ url('') }}">{{ config('parque.nome') }}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
            aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav ml-auto">
            <!-- <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li> -->
            @if(!auth()->user())
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('seguranca/usuario') }}">Entrar</a>
                </li>
            @else
            <!-- <li class="nav-item">
                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                </li> -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">{{ auth()->user()->nome }}</a>
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
<!-- Begin page content -->
{{--<main role="main" class="flex-shrink-0">--}}
{{--    <div class="container">--}}
{{--        <h1 class="mt-5">Sticky footer</h1>--}}
{{--        <p class="lead">Pin a footer to the bottom of the viewport in desktop browsers with this custom HTML and--}}
{{--            CSS.</p>--}}
{{--        <p>Use <a href="/docs/4.3/examples/sticky-footer-navbar/">the sticky footer with a fixed navbar</a> if need--}}
{{--            be, too.</p>--}}
{{--    </div>--}}
{{--</main>--}}

@yield('conteudo')

<footer class="footer mt-auto py-3">
    <div class="container">
            <span class="text-muted">&copy; {{date('Y')}} - DIME - Diretoria de Inform&aacute;tica,
                Manuten&ccedil;&atilde;o e Estat&iacute;stica.</span>
    </div>
</footer>
<script src="{{ asset('bootstrap/js/bootstrap-native-v4.min.js') }}"></script>
@yield('scripts')
</body>
</html>