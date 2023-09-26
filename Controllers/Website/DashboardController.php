<?php 
use GuzzleHttp\Middleware;
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
use Database\Database;
    class DashboardController{
        private static $database;
        private static $con;
        public function __construct(){
            self::$database = Database::getInstance();
            self::$con = self::$database->getConnection();
        }
        public static function index($request){
            $csrf = $GLOBALS['csrf'];
            $data = $request['middleware']['data'];
            $number = $request['middleware']['number'];
            include('view/page/utama/dashboard.php');
            exit();
        }
    }
?>