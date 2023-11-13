<?php
require_once(__DIR__ . '/../mobile/seniman/seniman.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = SenimanMobile::handle();
    $getKategori($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>