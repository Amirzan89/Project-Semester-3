<?php 
use Database\Database;
    class DashboardController{
        private static $database;
        private static $con;
        public function __construct(){
            self::$database = Database::getInstance();
            self::$con = self::$database->getConnection();
        }
        public static function index(){
            echo 'mlebuuu';
            include('view/page/dashboard.php');
            // echo 'gabutt';
            exit();
        }
    }
?>