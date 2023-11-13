<?php 
function loadEnv(){
    if(!$_SERVER['LOAD_ENV']){
        $path = ".env";
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                    $_SERVER[trim($key)] = trim($value);
                    $_SERVER['LOAD_ENV'] = true;
                }
            }
        }
    }
}
?>