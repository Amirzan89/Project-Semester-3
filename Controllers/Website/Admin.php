<?php 
use GuzzleHttp\Middleware;
use Database\Models\User as UsersModels;
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
use Database\Database;
    class AdminDashboardController{
        private static $database;
        private static $con;
        public function __construct(){
            self::$database = Database::getInstance();
            self::$con = self::$database->getConnection();
        }
        public static function show($request){
            $role = $request['role'];
            $csrf = $GLOBALS['csrf'];
            $user = $request;
            $number = $request['number'];
            if($role == 'super admin' || $role == 'admin event'){
                //get data user
                $columns = implode(',', UsersModels::$userColumns);
                $query = "SELECT $columns FROM user";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->execute();
                $bindResultArray = [];
                foreach (UsersModels::$userColumns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt[0], 'bind_result'], $bindResultArray);
                $dataEvents = [];
                //harus pakai cache
                while ($stmt[0]->fetch()) {
                    $row = [];
                    foreach(UsersModels::$userColumns as $column) {
                        $row[$column] = $$column;
                    }
                    $dataEvents[] = $row;
                }
                $stmt[0]->close();
                include('view/page/testing/event/dashboard.php');
                exit();
            }else if($role  == 'masyarakat'){
                return ['status'=>'error','Anda bukan super admin','code'=>400];
            }
        }
    }
?>