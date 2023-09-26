<?php 
namespace Controllers\Website\Event;
$rootDir = dirname(dirname((dirname(__DIR__))));
if(!defined('APP')){
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
use Database\Database;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class EventDashboardController{
    private static $database;
    private static $con;
    private static $url;
    private static $prefix;
    public function __construct(){
        self::$prefix = '/event';
        if(!isset($_SERVER['EVENT_PORT']) ||is_null($_SERVER['EVENT_PORT']) || empty($_SERVER['EVENT_PORT'])){
            self::$url = $_SERVER['EVENT_URL'];
        }else{
            self::$url = $_SERVER['EVENT_URL'].':'.$_SERVER['EVENT_PORT'];
        }
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function show($data,$uri = null){
    }
}
?>