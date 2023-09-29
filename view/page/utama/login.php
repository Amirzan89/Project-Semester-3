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
    <link rel="stylesheet" href="/public/css/utama/login.css?">
    <meta charset="UTF-8">
    <style></style>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Login</title> -->
    <title>Disporabudpar - Nganjuk</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="/public/img/icon/utama/logo.png" rel="icon">
    <!-- <link href="/public/css/utama/login.css" rel="stylesheet"> -->
</head>
<body>
    <!-- <div class="container">
            <form action="" class="form-login" id="loginForm">
                <h2><b> Selamat datang kembali!</b></h2>
                <input type="email" name="email" id="inpEmail" class="box" placeholder="Masukkan emailmu">
                <input type="password" name="password" id="inpPassword" class="box" placeholder="Masukkan kata sandimu">
                <input type="submit" value="Masuk" id="submit">
                
            </form>
            <div class="side-login">
                <img src="/public/img/icon/utama/login.png" alt="">
            </div>
        </div> -->
    <div class='login'>
        <div class="bg"></div>
        <div class="content">
            <form id="loginForm">
                <div class="header">
                    <h1>Login</h1>
                </div>
                <div class="row">
                    <label>Email</label> 
                    <input type="email" name='inpEmail' id="inpEmail" required><br>
                </div>
                <div class="row">
                    <label>Password</label>
                    <input type="password" name='inpPassword' id="inpPassword" required>
                </div>
                <div class="row">
                    <input type="checkbox">
                    <label>Remember me</label>
                    <a href="/forgot/password">Forgot Password ?</a>
                </div>
                <input type="submit" name='submit' value='Login'>
                <img src="" alt="">
                <a href="/auth/redirect" id="google"><img src="public/img/icon/utama/google.png" alt=""> Sig in with Google</a>
                <span id="register">Don't have account ? <a href="/register">Signup</a></span>
            </form>
            <div class="wm"></div>
        </div>
    </div>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script src="/public/js/utama/login.js?"></script>
</body>
</html>