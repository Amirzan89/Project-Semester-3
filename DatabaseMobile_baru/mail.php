<?php
require_once(__DIR__ . '/../mobile/Mail.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = MailMobile::handle();
    $data['desc'] = $data['type'];
    if($data['desc'] == 'email'){
        $createVerifyEmail($data);
    }else if($data['desc'] == 'password'){
        $createForgotPassword($data);
    }else{
        include(__DIR__ . '/../notfound.php');
    }
}
//protection
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include(__DIR__ . '/../notfound.php');
}
?>