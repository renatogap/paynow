<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <title>{{ config('parque.nome') }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('iconfont/material-icons.css') }}">
    <style>
        html {
            height: 100%;
        }
        body {
            background: #262626;
            background: -webkit-radial-gradient(#D3D3D3, #808080, #000000);
            background: -o-radial-gradient(#D3D3D3, #808080, #000000);
            background: -moz-radial-gradient(#D3D3D3, #808080, #000000);
            background: radial-gradient(#D3D3D3, #808080, #000000);
        }

        h2, h5 {
            color: white;
        }

        form label {font-weight: bold;}
    </style>
    <script> var BASE_URL = "{{url('')}}/"; </script>

    @yield('cabecalho')
</head>

<body>

    <main role="main" class="container">
        <div class="starter-template">
            @yield('conteudo')
        </div>
    </main>

    <script src="{{ asset('bootstrap/js/bootstrap-native-v4.min.js') }}"></script>

    @yield('scripts')
</body>

</html>