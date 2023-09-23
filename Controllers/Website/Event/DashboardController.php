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
        $client = new Client(); 
        try{
            $uri = substr($uri, strlen(self::$prefix));
            $response = $client->get(self::$url.$uri);
            // echo "path ".self::$url.$uri;
            $contentType = $response->getHeaderLine('Content-Type');
            $parts = explode(';', $contentType);
            $contentType = trim($parts[0]);
            if (strpos($contentType, 'application/json') !== false) {
                $body = $response->getBody();
                return json_decode($body, true);
            } else {
                $htmlContent = $response->getBody()->getContents();
                return ['status'=>'success','data'=>$htmlContent,'content'=>$contentType];
            }
        }catch(RequestException $e){
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($erorr['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $erorr['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $erorr->message,
                    );
                }
            }
            return $responseData;
        }
    }
}
?>