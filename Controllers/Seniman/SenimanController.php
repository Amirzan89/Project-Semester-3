<?php 
use Database\Database;
use Database\Models\Seniman;
class SenimanController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function daftarSeniman($data, $uri = null){
        //
    }
    public function editSeniman($data,$uri = null){
        //
    }
    public function perpanjangSeniman($data, $uri = null){
        //
    }
}
?>