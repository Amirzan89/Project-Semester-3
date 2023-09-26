<?php 
if(!defined('APP')){
    http_response_code(404);    
    // echo 'random';
    include('view/page/PageNotFound.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="/public/css/utama/register.css">
</head>
<body class="bg-blue-500">
    <div class='register' id='registerDiv'>
        <div class="bg"></div>
        <div class="content">
            <form id="registerForm">
                <div class="header">
                    <h1>Login</h1>
                </div>
                <div class="row">
                    <label>Nama </label> 
                    <input type="text" name='email' id="inpNama" required><br>
                </div>
                <div class="row">
                    <label>Email</label> 
                    <input type="email" name='email' id="inpEmail" required><br>
                </div>
                <div class="row">
                    <label>Password</label>
                    <input type="password" name='password' id="inpPassword" required>
                </div>
                <div class="row">
                    <label>Password</label>
                    <input type="password" name='password1' id="inpPassword1" required>
                </div>
                <div class="row">
                    <input type="checkbox">
                    <label>Remember me</label>
                    <a href="/forgot/password">Forgot Password ?</a>
                </div>
                <input type="submit" name='submit' value='Login'>
                <!-- <img src="" alt=""> -->
                <a href="/gabutt" id="google"><img src="view/img/icon/search.png" alt=""> Sig in with Google</a>
                <span id="register">Don't have account ? <a href="/login">Signup</a></span>
            </form>
            <div class="wm"></div>
        </div>
    </div>
    <div id="otp" style="display:none;">
        <div class="bg"></div>
        <form action="#" id="VerifyOTP">
            <h3>Verifikasi Email</h3>
            <p>Pakai fitur ini untuk Verifikasi Email</p>
            <p>Verifikasi OTP</p>
            <div class="input otp">
                <input type="text" id="otp1">
                <input type="text" id="otp2">
                <input type="text" id="otp3">   
                <input type="text" id="otp4">
                <input type="text" id="otp5">
                <input type="text" id="otp6">
            </div>
            <input type="submit" value="Konfirmasi OTP">
            <span>Tidak Menerima Kode OTP ? <a href="#" onclick="sendOtp()">kirim ulang</a></span>
        </form>
    </div>
    <div id="preloader" style="display: none;"></div>
    <div id="greenPopup" style="display:none"></div>
    <div id="redPopup" style="display:none"></div>
    <script src="/public/js/utama/register.js?"></script>
</body>
</html>