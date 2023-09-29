<?php 
namespace Controllers\Mobile;
use Database\Database;
use Database\Models\User;
class UserController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    // public function tambahPentas($data, $uri = null){
        //
    // }
}
?>