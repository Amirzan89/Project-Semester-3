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
            include('view/page/dashboard.php');
            exit();
        }
    }
?>