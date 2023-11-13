<?php
require_once(__DIR__ . '/../mobile/login.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = file_get_contents("php://input");
    $data = json_decode($input_data, true);
    Register($data,$con);
}
if(isset($_POST['register'])){
    Register($_POST,$con);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>