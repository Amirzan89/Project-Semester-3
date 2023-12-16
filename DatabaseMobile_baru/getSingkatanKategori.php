<?php
require_once(__DIR__ . '/../mobile/user.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = UserMobile::handle();
    $getNamaKategori($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>