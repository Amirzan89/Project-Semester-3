<?php 
use GuzzleHttp\Middleware;
use Database\Models\Event as EventsModels;
if(!defined('APP')){
    $rootDir = dirname(dirname(__DIR__));
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
use Database\Database;
    class EventDashboardController{
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
            // echo 'data json';
            // echo json_encode($request);
            // echo "<br>";
            if($role == 'super admin' || $role == 'admin event'){
                //get data event
                $columns = implode(',', EventsModels::$eventColumns);
                $query = "SELECT $columns FROM event";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->execute();
                $bindResultArray = [];
                foreach (EventsModels::$eventColumns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt[0], 'bind_result'], $bindResultArray);
                $dataEvents = [];
                //harus pakai cache
                while ($stmt[0]->fetch()) {
                    $row = [];
                    foreach(EventsModels::$eventColumns as $column) {
                        $row[$column] = $$column;
                    }
                    $dataEvents[] = $row;
                }
                $stmt[0]->close();
            }else if($role  == 'masyarakat'){
                // echo "<br>";
                // echo 'masuk masyarakat';
                // echo "<br>";
                //get data event
                $columns = implode(',', EventsModels::$eventColumns);
                $query = "SELECT $columns FROM event WHERE id_user = ?";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s',$request['id_user']);
                $stmt[0]->execute();
                $bindResultArray = [];
                foreach (EventsModels::$eventColumns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt[0], 'bind_result'], $bindResultArray);
                $dataEvents = [];
                //harus pakai cache
                while ($stmt[0]->fetch()) {
                    $row = [];
                    foreach(EventsModels::$eventColumns as $column) {
                        $row[$column] = $$column;
                    }
                    $dataEvents[] = $row;
                }
                echo "data masyarakat";
                echo json_encode($dataEvents);
                echo "<br>";
                // exitt();
                $stmt[0]->close();
            }
            include('view/page/testing/event/dashboard.php');
            exit();
        }
    }
?>