<?php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;
define('APP',true);
$dotenv = Dotenv::createImmutable(__DIR__, '.env');
$dotenv->load();
require_once 'Database/Database.php';
require_once 'routes/index.php';

// require_once 'env.php';
?>