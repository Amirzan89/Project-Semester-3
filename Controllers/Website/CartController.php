<?php 
use Database\Database;
Class CartController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function show($email){
        $query = "SELECT id_user, email, password, nama FROM users WHERE BINARY email = ? LIMIT 1";
        $stmt = self::$con->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $columns = ['id_user','email','password','nama'];
        $bindResultArray = [];
        foreach ($columns as $column) {
            $bindResultArray[] = &$$column;
        }
        call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
        $result = [];
        if ($stmt->fetch()) {
            foreach ($columns as $column) {
                $result[$column] = $$column;
            }
        }
        exit();
    }
}
?>