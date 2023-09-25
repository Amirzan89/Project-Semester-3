<?php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;
define('APP',true);
$dotenv = Dotenv::createImmutable(__DIR__, '.env');
$dotenv->load();
//create csrf token 
if (empty($_SESSION['key'])) {
    $_SESSION['key'] = bin2hex(random_bytes(32));
}
global $csrf;
$csrf = hash_hmac('sha256', 'this is some string: index.php', $_SESSION['key']);
require_once 'Database/Database.php';
require_once 'routes/index.php';
// require_once 'env.php';
?>