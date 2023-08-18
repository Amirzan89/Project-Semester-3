<?
use Database\Database;
require_once '';
Class DetailController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function show(){
        //
    }
}
?>