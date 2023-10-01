<?php 
use GuzzleHttp\Middleware;
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
use Database\Database;
    class SenimanDashboardController{
        private static $database;
        private static $con;
        public function __construct(){
            self::$database = Database::getInstance();
            self::$con = self::$database->getConnection();
        }
        public static function show($request){
            // echo json_encode($request);
            // exit();
            $role = $request['role'];
            $csrf = $GLOBALS['csrf'];
            $user = $request;
            $number = $request['number'];
            include('view/page/testing/seniman/dashboard.php');
            exit();
        }
    }
?>