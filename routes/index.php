<?php
if(!defined('APP')){
    http_response_code(404);
    include('view/page/PageNotFound.php');
    exit();
}
//absolute path
$rootDir = dirname(__DIR__);
use Controllers\Website\Event\DashboardController as EventDashboard;
require_once $rootDir . '/autoload.php';
// Include the required files using the absolute paths
require_once $rootDir . '/middleware/Authenticate.php';
require_once $rootDir . '/Controllers/UserController.php';
require_once $rootDir . '/Controllers/Website/DashboardController.php';
require_once $rootDir . '/Controllers/Website/Event/DashboardController.php';
require_once $rootDir . '/Controllers/Website/Tempat/DashboardController.php';
require_once $rootDir . '/Controllers/Website/Izin/DashboardController.php';
require_once $rootDir . '/Controllers/Website/Seniman/DashboardController.php';
require_once $rootDir . '/Controllers/Auth/LoginController.php';
require_once $rootDir . '/Controllers/Auth/RegisterController.php';
require_once $rootDir . '/Controllers/Auth/JWTController.php';
require_once $rootDir . '/Controllers/Mail/MailController.php';
Route::add('/','GET',function(){
    include('view/page/utama/home.php');
    exit();
},['Authenticate@handle']);
Route::add('/login','GET',function(){
    include('view/page/utama/login.php');
    exit();
},['Authenticate@handle']);
Route::add('/register','GET',function(){
    include('view/page/utama/register.php');
    exit();
},['Authenticate@handle']);
Route::add('/forgot/password','GET',function(){
    include('view/page/utama/forgotPassword.php');
    exit();
},['Authenticate@handle']);
Route::add('/dashboard', 'GET', 'DashboardController@index',['Authenticate@handle']);
Route::group('/auth',[
    ['/redirect','GET','LoginController@redirectToProvider'],
    ['/google','GET','LoginController@handleProviderCallback'],
]);
Route::add('/token/get','POST','JwtController@createJWTWebsite');
Route::add('/token/decode','POST','JwtController@decode');
Route::group('/verify',[
    ['/create',[
        ['/password','POST','MailController@createForgotPassword'],
        ['/email','POST','MailController@createVerifyEmail'],
    ]],
    ['/password',[
        ['/','GET','UserController@getChangePass'],
        ['/','POST','UserController@changePassEmail'],
    ]],
    ['/otp',[
        ['/otp/password','POST','UserController@getChangePass'],
        ['/otp/email','POST','UserController@verifyEmail'],
    ]],
    ['/email',[
        ['/','GET','UserController@verifyEmail'],
        ['/','POST','UserController@verifyEmail'],
    ]],
]);
Route::group('/users',[
    ['/register','POST','RegisterController@register',['Authenticate@handle']],
    ['/login','POST','LoginController@login',['Authenticate@handle']],
    ['/logout','POST','UserController@logout',['Authenticate@handle']],
]);
// event
Route::group('/event',[
    ['/dashboard','GET','Controllers\Website\Event\EventDashboardController@show',['Authenticate@handle']],
    ['/tambah','POST','Controllers\Website\Event\EventDashboardController@show',['Authenticate@handle']],
]);
// Izin
Route::group('/izin',[
    ['/dashboard','GET','Izin\Dashboard@show'],
]);
// seniman
Route::group('/seniman',[
    ['/dashboard','GET','Seniman\Dashboard@show'],
]);
// tempat
Route::group('/tempat',[
    ['/dashboard','GET','Controllers\Website\Tempat\TempatDashboardController@show',['Authenticate@handle']],
]);
//mobile
Route::group('/mobile',[
    ['/dashboard','GET','Controllers\Website\Event\EventDashboardController@show'],
]);
// Dispatch the request
Route::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
class Route{
    private static $routes = [];
    public static function group($prefix, $routes){
        foreach ($routes as $route) {
            if (is_array($route[1])) {
                self::group($prefix . $route[0], $route[1]);
            } else {
                $route[0] = trim($prefix  . $route[0], '/');
                self::add($route[0], $route[1], $route[2], isset($route[3]) ? $route[3] : [], isset($route[4]) ? $route[4] : []);
            }
        }
    }
    public static function add($uri, $method, $callback, $middlewares = [], $parameter = []){
        $uri = ltrim($uri, '/');
        self::$routes[] = [
            'uri' => $uri,
            'method' => $method,
            'callback' => $callback,
            'middlewares' => $middlewares,
            'parameter'=>$parameter
        ];
    }
    public static function dispatch($uri, $method, $data=null, $uriData=null){
        $query = parse_url($uri, PHP_URL_QUERY);
        parse_str($query, $queryParams);
        $path = parse_url($uri, PHP_URL_PATH);
        $path = ltrim($path, '/');
        $routeFound = false;
        $headers = getallheaders();
        $body = file_get_contents('php://input');
        //check http request method 
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','DELETE'])) {
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                $requestData = json_decode($body, true);
                if ($requestData === null) {
                    $requestData = [];
                }
            } elseif (strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {
                parse_str($body, $requestData);
            } else {
                $requestData = [];
            }
            if (empty($requestData) && !empty($_POST)) {
                $requestData = $_POST;
            }
            if (!empty($_FILES)) {
            }
        //if request is GET
        } else {
            $requestData = [];
        }
        $routeFound = false;
        $lastSlashPos = strrpos($path, '/');
        if($lastSlashPos){
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if(in_array($path1,['/verify/email','/verify/password'])){
                $path = ltrim($path1,'/');
            }
        }
        foreach (self::$routes as $route) {
            if ($route['uri'] === $path && $route['method'] === $method) {
                $routeFound = true;
                $middlewareResults = [];
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareClosure = function ($requestData, $data) use ($middleware) {
                        $parts = explode('@', $middleware);
                        $controllerName = $parts[0];
                        $methodName = $parts[1];
                        $controller = new $controllerName();
                        return call_user_func_array([$controller, $methodName], [$requestData, $data]);
                    };
                    $middlewareResult = $middlewareClosure($requestData, ['uri'=>$_SERVER['REQUEST_URI'],'method'=>$_SERVER['REQUEST_METHOD']]);
                    $middlewareResults[] = $middlewareResult;
                    if($middlewareResult['status'] == 'error'){
                        $middlewareResult['code'] ? http_response_code($middlewareResult['code']) : http_response_code(400);
                        echo $middlewareResult['message'];
                        return;
                    }
                }
                //merge data from middleware
                $middlewareResult = [];
                foreach ($middlewareResults as $results) {
                    if (isset($results['data'])) {
                        $middlewareResult = array_merge($middlewareResult, $results['data']);
                    }
                }
                $requestData = [
                    'middleware'=>$middlewareResult,
                    'request'=>$requestData
                ];
                $callback = $route['callback'];
                if ($callback instanceof Closure) {
                    call_user_func($callback);
                } else {
                    $parts = explode('@', $callback);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];
                    $controller = new $controllerName();
                    $result = [];
                    if($methodName == 'handleProviderCallback'){
                        $result = call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI'], $_GET]);
                    }else if(($methodName == 'getChangePass' || $methodName == 'verifyEmail')){
                        $result = call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI'], $method, $_GET]);
                    }else{
                        $result = call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI']]);
                    }
                    if($result['status'] == 'error'){ 
                        header('Content-Type: application/json');
                        http_response_code(!empty($result['code']) ? $result['code'] : 400);
                        unset($result['code']);
                        echo json_encode($result);
                        exit();
                    }else{
                        if(isset($result['content'])){
                            if($result['content'] == 'application/json'){
                                header('Content-Type: application/json');
                                unset($result['code']);
                                echo json_encode($result);
                                exit();
                            }else if($result['content'] == 'text/html'){
                                echo $result['data'];
                                exit();
                            }else{
                                echo $result['data'];
                                exit();
                            }
                        }else{
                            //handle json
                            header('Content-Type: application/json');
                            unset($result['code']);
                            echo json_encode($result);
                            exit();
                        }
                    }
                }
            }
        }
        if (!$routeFound) {
            http_response_code(404);
            include('view/page/PageNotFound.php');
            exit();
        }
    }
}
?>