<?php
    //absolute path
    $rootDir = dirname(__DIR__);
    // Include the required files using the absolute paths
    require_once $rootDir . '/middleware/Authenticate.php';
    require_once $rootDir . '/Controllers/UserController.php';
    require_once $rootDir . '/Controllers/Website/DashboardController.php';
    require_once $rootDir . '/Controllers/Auth/LoginController.php';
    require_once $rootDir . '/Controllers/Auth/JWTController.php';
    Route::add('/','GET',function(){
        echo '<br> terserajjj la';
        exit();
    });
    Route::add('/dashboard', 'GET', 'DashboardController@index');
    Route::add('/contact', 'POST', 'ContactController@postContact');
    // Dispatch the request
    Route::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    class Route{
        private static $routes = [];

        public static function add($uri, $method, $callback, $middlewares = []){
            $uri = ltrim($uri, '/');
            self::$routes[] = [
                'uri' => $uri,
                'method' => $method,
                'callback' => $callback,
                'middlewares' => $middlewares,
            ];
        }

        public static function dispatch($uri, $method){
            $uri = ltrim($uri, '/');
            $routeFound = false;
            echo 'ganbutttt';
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
                        call_user_func([$controller, $methodName]);
                    }
                }
            }
            if (!$routeFound) {
                http_response_code(404);
                echo 'Page not found.';
            }
        }
    }
?>