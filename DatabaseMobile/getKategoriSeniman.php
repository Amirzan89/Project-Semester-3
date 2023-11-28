<?php
require_once(__DIR__ . '/../mobile/seniman/seniman.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = SenimanMobile::handle();
    $getKategori($data);
}
?>