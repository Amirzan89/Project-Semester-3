<?php
require_once(__DIR__ . '/../../mobile/pentas/pentas.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = PentasMobile::handle();
    $data['desc'] = 'diajukan';
    $getPentas($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../../notfound.php');
}
?>