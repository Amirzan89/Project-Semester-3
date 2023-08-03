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
use Controllers\Auth\RegisterController;
Route::add('/','GET',function(){
    include('view/page/dashboard.php');
    exit();
});
Route::add('/login','GET',function(){
    include('view/page/login.php');
    exit();
});
Route::add('/register','GET',function(){
    include('view/page/register.php');
    exit();
});
Route::add('/forgot/password','GET',function(){
    include('view/page/forgotPassword.php');
    exit();
});
Route::add('/email', 'GET', 'MailController@send',[],[$_SERVER['REQUEST_URI']]);
Route::add('/dashboard', 'GET', 'DashboardController@index');
Route::add('/users/register','POST','RegisterController@register');
Route::add('/users/login','POST','LoginController@login()');
Route::add('/auth/redirect','GET','LoginController@redirectToProvider');
Route::add('/auth/google','GET','LoginController@handleProviderCallback');
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
        $uri = ltrim($uri, '/');
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
                // echo 'data ';
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
        $cookies = $_COOKIE;
        // echo 'ganbutttt';
        foreach (self::$routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                $routeFound = true;
                // Apply middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareResult = call_user_func($middleware);
                    if ($middlewareResult !== true) {
                        // Middleware returned something other than true (e.g., error message)
                        echo $middlewareResult;
                        return;
                    }
                }
                // Call the controller or closure
                $callback = $route['callback'];
                // echo $callback instanceof Closure;
                // return;
                if ($callback instanceof Closure) {
                    call_user_func($callback);
                } else {
                    $parts = explode('@', $callback);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $methodName], [$requestData,  $_SERVER['REQUEST_URI']]);
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