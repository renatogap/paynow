<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="view-qrcode"></div>
    
    <script type="text/javascript" src="{{url('js/QRCode.js')}}"></script>
    <script>
        new QRCode("view-qrcode", {
            text: '{{ $id }}', //document.getElementById('codigo').value,
            width: 325,
            height: 343,
            colorDark: "black",
            colorLight: "white",
            correctLevel : QRCode.CorrectLevel.H
        });
    </script>
</body>
</html>   
   
   
    