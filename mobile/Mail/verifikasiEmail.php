<?php 
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../../notfound.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
</head>
<body>
    <div>
        <div class="bg"></div>
        <div class="content">
            <h1>Verify Email</h1>
            <span>Berikut ini adalah kode untuk verifikasi email anda</span>
            <div class="otp">
                <p>codee otp   %CODE%</p>
            </div>
            <p>atau link untuk verifikasi email anda <a href="%LINK%?email=%EMAIL%">Verifikasi Email</a></p>
        </div>
    </div>
</body>
</html>