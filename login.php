<?php
require_once('web/koneksi.php');
require_once('web/authenticate.php');
$database = koneksi::getInstance();
$conn = $database->getConnection();
$userAuth = authenticate($_POST,[
    'uri'=>$_SERVER['REQUEST_URI'],
    'method'=>$_SERVER['REQUEST_METHOD']
],$conn);
if($userAuth['status'] == 'error'){
	header('Location: /login.php');
}else{
	$userAuth = $userAuth['data'];
  // if($userAuth['role'] != 'super admin'){
  //   header('Location: /dashboard.php');
  // }
}
$csrf = $GLOBALS['csrf'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style></style>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Login</title> -->
    <title>Disporabudpar - Nganjuk</title>
    <link rel="stylesheet" href="/public/css/utama/login.css?">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="/public/img/icon/utama/logo.png" rel="icon">
</head>
<body>
    <div class="container">
            <form action="web/login.php" method="post" class="form-login" id="loginForm">
                <h2><b> Selamat datang kembali!</b></h2>
                <input type="email" name="email" id="inpEmail" class="box" placeholder="Masukkan emailmu">
                <input type="password" name="password" id="inpPassword" class="box" placeholder="Masukkan kata sandimu">
                <input type="submit" name="login" value="Masuk" id="submit">
                
            </form>
            <div class="side-login">
                <img src="/public/img/icon/utama/login.png" alt="">
            </div>
        </div>
        <div id="preloader" style="display: none;"></div>
        <div id="greenPopup" style="display:none"></div>
        <div id="redPopup" style="display:none"></div>
</body>
</html>