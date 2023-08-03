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
    <link rel="stylesheet" href="public/css/login.css">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div class='login'>
        <div class="bg"></div>
        <div class="content">
            <form id="loginForm">
                <div class="header">
                    <h1>Login</h1>
                </div>
                <div class="row">
                    <label>Email</label> 
                    <input type="email" name='email' required><br>
                </div>
                <div class="row">
                    <label>Password</label>
                    <input type="password" name='password' required>
                </div>
                <div class="row">
                    <input type="checkbox">
                    <label>Remember me</label>
                    <a href="/forgot/password">Forgot Password ?</a>
                </div>
                <input type="submit" name='submit' value='Login'>
                <!-- <img src="" alt=""> -->
                <a href="/gabutt" id="google"><img src="public/img/icon/search.png" alt=""> Sig in with Google</a>
                <span id="register">Don't have account ? <a href="/register">Signup</a></span>
            </form>
            <div class="wm"></div>
        </div>
    </div>
    <div class="popup"></div>
</body>
</html>