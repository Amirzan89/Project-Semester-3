<?php 
if(!defined('APP')){
    http_response_code(404);
    include('view/page/PageNotFound.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php isset($title) ? $title : 'Default Title' ?></title>
    <link rel="stylesheet" href="/public/css/notification.css">
    <!-- <link href="{{ asset($tPath.'css/page/notification.css') }}" rel="stylesheet"> -->
</head>
<body>
    <?php if($div == 'green'){ ?>
        <div id="greenPopup" style="display:block;">
            <div class="bg" onclick="closePopup('green',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <img src="/public/img/icon/check.png" alt="">
            </div>
            <span class="closePopup" onclick="closePopup('green',true)">X</span>
            <label><?php if(isset($message)){
                echo $message;
            }else{
                echo 'Not Found ';
            }
            ?></label>
        </div>
        <?php }else if($div == 'red'){ ?>
        <div id="redPopup" style="display:block;">
            <div class="bg" onclick="closePopup('red',true)"></div>
            <div class="kotak">
                <div class="bunder1"></div>
                <span>!</span>
            </div>
            <span class="closePopup" onclick="closePopup('red',true)">X</span>
            <label><?php if(isset($message)){
                echo $message;
            }else{
                echo 'Not Found ';
            }
            ?></label>

        </div>
    <?php }
        if(isset($div1) && $div1 == 'dashboard'){ ?>
        <script>
            const delay = 3000;
            function dashboardPage(){
                window.location.href = '/dashboard';
            }
            setTimeout(dashboardPage, delay);
        </script>
    <?php } ?>
</body>
</html>