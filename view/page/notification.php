<?php 
$tPath = app()->environment('local') ? '' : '/public/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($title) ? $title : 'Default Title' }}</title>
    <link href="{{ asset($tPath.'css/page/notification.css') }}" rel="stylesheet">
</head>
<body>
    @if(app()->environment('local'))
        <script>
            var tPath = '';
        </script>
    @else
        <script>
            var tPath = '/public/';
        </script>
    @endif
    @if($div == 'green')
        <div id="greenPopup" style="display:block;">
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <img src="{{ asset($tPath.'assets/img/check.png') }}" alt="">
            </div>
            <span class="closePopup" onclick="closePopup('green',true)">X</span>
            <label>{{ isset($message) ? $message : 'random ' }}</label>
        </div>
        @elseif($div == 'red')
        <div id="redPopup" style="display:block;">
            <div class="bg" onclick="closePopup('red',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <span>!</span>
            </div>
            <span class="closePopup" onclick="closePopup('red',true)">X</span>
            <label>{{ isset($message) ? $message : 'Not Found ' }}</label>
        </div>
    @endif
    @if(isset($div1) && $div1 == 'dashboard')
        <script>
            const delay = 3000;
            function dashboardPage(){
                window.location.href = '/page/dashboard';
            }
            setTimeout(dashboardPage, delay);
        </script>
    @endif
</body>
</html>