<?php 
// setcookie('token1', '', time() - 3600, '/');
require_once('jwt.php');
require_once('koneksi.php');
$jwt = new Jwt();
$email = $data['email'];
$number = $data['number'];
if(empty($email) || is_null($email)){
    return ['status'=>'error','message'=>'email empty','code'=>400];
}else if(empty($number) || is_null($number)){
    // return ['status'=>'error','message'=>'token empty','code'=>400];
}else{
    $db = koneksi::getInstance();
    $con = $db->getConnection();
    $deleted = $jwt->deleteRefreshWebsite($email,$number,$con);
    if($deleted['status'] == 'error'){
        setcookie('token1', '', time() - 3600, '/');
        setcookie('token2', '', time() - 3600, '/');
        setcookie('token3', '', time() - 3600, '/');
        header('Location: /login');
        exit();
    }else{
        setcookie('token1', '', time() - 3600, '/');
        setcookie('token2', '', time() - 3600, '/');
        setcookie('token3', '', time() - 3600, '/');
        header('Location: /login');
        exit();
    }
}
header('Location:/login.php'); 
exit();
?>