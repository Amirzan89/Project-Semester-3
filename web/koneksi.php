<?php
class Koneksi{
    private static $instance;
    private static function loadEnv($path = null){
        if($path == null){
            $path = __DIR__."/.env";
        }
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                    $_SERVER[trim($key)] = trim($value);
                }
            }
        }
    }
    private $conn;
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->createConn();
        }
        return self::$instance;
    }
    private function createConn() {
        self::loadEnv();
        $this->conn = new \mysqli('p:'.$_SERVER['DB_HOST'].':'.$_SERVER['DB_PORT'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD'], $_SERVER['DB_DATABASE']);
        if ($this->conn->connect_error) {
            throw new Exception("Tidak bisa membuat koneksi");
        } else {
            // echo "nyambung cuyy<br>";
        }
    }
    public function getConnection() {
        return $this->conn;
    }
    private static $pool = [];
}
?>