<?php
require_once(__DIR__ . '/../mobile/login.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $data['desc'] = 'google';
    Login($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>