<?php 
require_once('jwt.php');
require_once('koneksi.php');
$jwt = new Jwt();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);
    $email = $data['email'];
    $number = $data['number'];
    if(empty($email) || is_null($email)){
        return ['status'=>'error','message'=>'email empty','code'=>400];
    }else if(empty($number) || is_null($number)){
    }else{
        $db = Koneksi::getInstance();
        $con = $db->getConnection();
        $deleted = $jwt->deleteRefreshWebsite($email,$number);
        if($deleted['status'] == 'error'){
            setcookie('token1', '', time() - 3600, '/');
            setcookie('token2', '', time() - 3600, '/');
            setcookie('token3', '', time() - 3600, '/');
            header('Location: /login.php');
            exit();
        }else{
            setcookie('token1', '', time() - 3600, '/');
            setcookie('token2', '', time() - 3600, '/');
            setcookie('token3', '', time() - 3600, '/');
            header('Location: /login.php');
            exit();
        }
    }
    header('Location: /login.php'); 
    exit();
}
?>