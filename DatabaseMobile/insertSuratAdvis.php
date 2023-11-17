<?php
require_once(__DIR__ . '/../mobile/pentas/pentas.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = PentasMobile::handle();
    $tambahPentas($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>