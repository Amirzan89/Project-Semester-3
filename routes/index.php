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
Route::add('/users/register','POST','RegisterController@register()');
Route::add('/users/login','POST','LoginController@login()');
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
    public static function dispatch($uri, $method){
        $uri = ltrim($uri, '/');
        $routeFound = false;
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
                    // $params = [];
                    // foreach ($route['parameter'] as $paramName) {
                    //     $params[] = $_REQUEST[$paramName];
                    // }
                    call_user_func_array([$controller, $methodName], [$_REQUEST, $_SERVER['REQUEST_URI']]);
                    // call_user_func([$controller, $methodName]);
                }
            }
        }
        if (!$routeFound) {
            http_response_code(404);
            // echo 'Page not found.';
            include('view/page/PageNotFound.php');
        }
    }
}
?>