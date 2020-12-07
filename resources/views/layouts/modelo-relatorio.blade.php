<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('titulo')</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url('<?= asset("iconfont/calibri.ttf") ?>') format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'CalibriB';
            src: url('<?= asset("iconfont/calibrib.ttf") ?>') format("truetype");
            font-weight: bold;
        }

        @page {
            margin: 130px 100px 60px 100px;
            font-size: 16px;
            /*line-height: 1.5em;*/
            font-family: 'Calibri', 'CalibriB', 'sans-serif';
        }

        body {
            font-family: 'Calibri', 'CalibriB', 'sans-serif';
        }

        #header {
            position: fixed;
            left: 0;
            top: -100px;
            text-align: center;
            font-weight: bold;
            font-size: 1em;
            margin-bottom: 0;
        }

        .header_title {
            line-height: 1.3em;
            font-family: 'CalibriB', 'sans-serif';
        }

        #footer {
            position: fixed;
            left: 0;
            bottom: -63px;
            right: 0;
            height: 50px;
            text-align: center;
            text-indent: 0;
            font-size: 11px
        }
    </style>
    @yield('head')
</head>
<body>
<div id="header">
    <img src="{{ asset('images/policia.png') }}" width="85" style="float: left">
    <img src="{{ asset('images/estado.png') }}" width="68" style="float: right">
    <div>
        <div class="header_title">GOVERNO DO ESTADO DO PARÁ</div>
        <div class="header_title">POLÍCIA CIVIL DO ESTADO DO PARÁ</div>
        <div class="header_title" style="margin-top: 1em;">@yield('subtitulo')</div>
    </div>
</div>
<!-- O footer deve ficar depois do header e antes do content -->
<div id="footer">
    <script type='text/php'>
            if (isset($pdf))
            {
                $pdf->page_text(70, $pdf->get_height() - 25, "{{ date('d/m/Y H:i') }}", null, 9, array(0,0,0));
                $pdf->page_text(510, $pdf->get_height() - 25, "{PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0,0,0));
            }
    </script>
</div>
@yield('conteudo')
</body>
</html>