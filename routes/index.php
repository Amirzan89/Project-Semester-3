<?php
if(!defined('APP')){
    http_response_code(404);
    include('view/page/PageNotFound.php');
    exit();
}
//absolute path
$rootDir = dirname(__DIR__);
require_once $rootDir . '/autoload.php';
// Include the required files using the absolute paths
require_once $rootDir . '/middleware/Authenticate.php';
require_once $rootDir . '/Controllers/UserController.php';
require_once $rootDir . '/Controllers/Website/DashboardController.php';
require_once $rootDir . '/Controllers/Auth/LoginController.php';
require_once $rootDir . '/Controllers/Auth/RegisterController.php';
require_once $rootDir . '/Controllers/Auth/JWTController.php';
require_once $rootDir . '/Controllers/Mail/MailController.php';
// use Controllers\Auth\RegisterController;
Route::add('/','GET',function(){
    // echo '<br>home page<br>';
    include('view/page/dashboard.php');
    exit();
},['Authenticate@handle']);
Route::add('/login','GET',function(){
    // echo 'loggiiinn';
    include('view/page/login.php');
    exit();
},['Authenticate@handle']);
Route::add('/register','GET',function(){
    // echo 'registerr';
    include('view/page/register.php');
    exit();
},['Authenticate@handle']);
Route::add('/forgot/password','GET',function(){
    // echo 'forgott';
    include('view/page/forgotPassword.php');
    exit();
},['Authenticate@handle']);
Route::add('/verify/password','GET','UserController@getChangePass');
Route::add('/verify/password','POST','UserController@changePassEmail');
Route::add('/verify/email','GET','UserController@verifyEmail');
Route::add('/verify/email','POST','UserController@verifyEmail');
Route::add('/dashboard', 'GET', 'DashboardController@index',['Authenticate@handle']);
Route::add('/users/register','POST','RegisterController@register',['Authenticate@handle']);
Route::add('/users/login','POST','LoginController@login',['Authenticate@handle']);
Route::add('/auth/redirect','GET','LoginController@redirectToProvider');
Route::add('/auth/google','GET','LoginController@handleProviderCallback');
Route::add('/token/get','POST','JwtController@createJWTWebsite');
Route::add('/token/decode','POST','JwtController@decode');
// Dispatch the request
Route::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
class Route{
    private static $routes = [];
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
        // Get request body
        $body = file_get_contents('php://input');
        // Check if the request method is POST
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST','PUT','DELETE'])) {
            // Check if the request data is provided as JSON
            if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
                // Decode the JSON data from the request body
                $requestData = json_decode($body, true);
                // If the JSON is not valid or not provided, default to an empty array
                if ($requestData === null) {
                    $requestData = [];
                }
            } elseif (strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false) {
                // Check if the content type is application/x-www-form-urlencoded
                // Parse form data from the request body
                parse_str($body, $requestData);
            } else {
                // For other types of data, treat the request data as an empty array
                $requestData = [];
            }
            // Check if there is any regular form data (not file uploads)
            if (empty($requestData) && !empty($_POST)) {
                // Regular form data (not file uploads) will be available in $_POST
                $requestData = $_POST;
            }
            // Check if there are uploaded files
            if (!empty($_FILES)) {
                // Process uploaded files here, if needed
            }
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
                $requestData = [
                    'middleware'=>$middlewareResults,
                    'request'=>$requestData
                ];
                // Call the controller or closure
                $callback = $route['callback'];
                if ($callback instanceof Closure) {
                    call_user_func($callback);
                } else {
                    $parts = explode('@', $callback);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];
                    $controller = new $controllerName();
                    if($methodName == 'handleProviderCallback'){
                        call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI'], $_GET]);
                    }else if(($methodName == 'getChangePass' || $methodName == 'verifyEmail')&& $route['method'] == 'GET'){
                        call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI'], $method, $_GET]);
                    }else{
                        call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI']]);
                    }
                }
            }
        }
        if (!$routeFound) {
            http_response_code(404);
            // $query = parse_url($uri, PHP_URL_QUERY);
            // parse_str($query, $queryParams);
            // $path = parse_url($uri, PHP_URL_PATH);
            // // $path = ltrim($path, '/');
            // // echo '<br>path terserah '.$path;
            // $lastSlashPos = strrpos($path, '/');
            // $path1 = substr($uri, 1, $lastSlashPos);
            // echo '<br>pathh relativeee '.$path1;
            // $randomString = ltrim(substr($path, strrpos($path, '/')),'/');
            // echo '<br>pathh aneh '.$randomString;
            // echo "<br>path random <br>".$path;
            include('view/page/PageNotFound.php');
            exit();
        }
    }
}
?>