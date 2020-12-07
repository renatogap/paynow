<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('parque.slogan') }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="imagem/x-icon">

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


    <!-- 16, 32, 48, 64, 90, 128, 256 -->

    <link href="{{ asset('materialize-css/materialize.min.css') }}" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%;
            background: #8bb315;
            width: 100%;
        }

        .box {
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 10px;
            padding: 30px 0;
            float: none;
            max-width: 400px;
            width: 96%;
            margin: 3em auto;

        }

        .corpo {
            padding: 15px 20px 0px;
        }

        .cabeca {
            text-align: center;
        }

        .logo {
            width: 7em !important;
        }

        .cabeca img {
            width: 10em;
        }

        .cabeca h1 {
            color: #555;
            font-weight: 300;
            font-size: 22px;
            font-weight: normal;
            margin: 0 0 15px;
        }

        .cabeca h2 {
            color: #3c3c3c;
            font-weight: 300;
            font-size: 18px;
            font-weight: normal;
            margin: 0 0 15px;
        }

        .corpo input {
            padding: 15px;
            font-size: 16px;
            height: 20px;
            color: #777;
            border: 1px solid #ccc;
        }

        .corpo a {
            font-size: 14px;
        }

        .pe {
            padding: 0px 40px;
            text-align: center;
        }

        .pe div {
            padding: 10px 0;
            clear: both;

            color: #555;
            font-size: 12px;
        }

        .pe .letreiro {
            text-align: justify !important;
        }

        .pe .letreiro i, .pdtp {
            padding-top: 10px;
        }

        .indicator {
            display: none;
            height: 30px;
            padding: 8px;
            position: absolute;
            right: 5px;
            text-align: center;
            top: 5px;
            width: 30px;
        }

        .indicator.on {
            display: block
        }

    </style>
</head>
<body>

<div class="box">
    <div class="cabeca">
        <a href="#"><img class="logo" src="{{ config('parque.logo') }}" /></a>
        <!--<h1>{{config('parque.nome')}}</h1>-->
        <h1 style="font-weight: bold;">Sistema {{config('parque.slogan')}}</h1>
        <?php if (config('app.env') == 'local'): ?>
        <!--<h1 style="color: red; text-decoration: blink;">DESENVOLVIMENTO</h1>-->
        <?php endif;?>
    </div>
    <div class="corpo">
        <form id="form" class="form-signin" action="{{url('seguranca/usuario/login')}}" method="post">
            <div class="content-wrap">
                <div class="input-field">
                    <label for="email">E-mail</label>
                    <input type="text" class="form-control" name="email"
                           value="{{old('email')}}" id="email" required>
                </div>
                <div class="input-field">
                    <label for="password">Senha</label>
                    <input type="password" class="form-control"
                           id="password" name="ab" required>
                    <div id="pnlIndicator" class="indicator">
                        <i class="material-icons right">warning</i>
                    </div>
                </div>
                @if (count($errors) > 0)
                    <div class="card-panel red lighten-4" style="color: #b71c1c">
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
                <div class="row">
                    <!--
                    <span class="left pdtp">
                        <a href="{{url('cadastro')}}">Esqueci a senha</a>
                    </span>
                    -->
                    <button style="background: #004735 !important;" class="btn waves-effect blue darken-3 right" type="submit">Entrar</button>
                </div>
            </div>
            {{ csrf_field() }}
        </form>
    </div>
    <div class="pe">
        @php($letreiro = config('parque.letreiro'))
        @if (strlen($letreiro) > 0)
            <div class="letreiro">
                <i class="material-icons left">info</i>{{$letreiro}}
            </div>
        @endif
        <div class="creditos">
            &copy;  <?php echo date('Y') ?> - Desenvolvido por PayNow.
        </div>
    </div>
</div>
</body>
<script src="{{asset('materialize-css/materialize.min.js')}}"></script>

</html>