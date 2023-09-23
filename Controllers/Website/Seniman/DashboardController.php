<?php 
namespace Controllers\Website\Seniman;
$rootDir = dirname(dirname(dirname(__DIR__)));
if(!defined('APP')){
    http_response_code(404);
    include($rootDir.'/view/page/PageNotFound.php');
    exit();
}
// require_once ''
use Database\Database;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class SenimanDashboardController{
    private static $database;
    private static $con;
    private static $url;
    public function __construct(){
        if(!isset($_SERVER['SENIMAN_PORT']) ||is_null($_SERVER['SENIMAN_PORT']) || empty($_SERVER['SENIMAN_PORT'])){
            self::$url = $_SERVER['SENIMAN_URL'];
        }else{
            self::$url = $_SERVER['SENIMAN_URL'].':'.$_SERVER['SENIMAN_PORT'];
        }
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function show($data,$uri = null){
        $client = new Client();
        try{
            $response = $client->get(self::$url.'/dashboard');
            $body = $response->getBody();
            return json_decode($body,true);
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