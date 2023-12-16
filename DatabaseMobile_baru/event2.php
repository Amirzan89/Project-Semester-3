<?php
require_once(__DIR__ . '/../mobile/event/event.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = EventMobile::handle();
    $updatePasswordProfile($data);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>