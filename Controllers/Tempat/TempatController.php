<?php 
use Database\Database;
use Database\Models\Tempat;
use Database\Models\ListTempat;
class TempatController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    //hanya aadmin
    public function tambahTempat($data, $uri = null){
        //
    }
    public function sewaTempat($data, $uri = null){
        //
    }
}
?>