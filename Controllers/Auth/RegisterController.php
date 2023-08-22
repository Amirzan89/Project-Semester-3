<?php
// namespace Controllers\Auth;
require_once 'Controllers/UserController.php';
use Database\DATABASE;
class RegisterController{
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }

    public function Register($data, $uri = null){
        try{
            $userController = new UserController();
            $data = $data['request'];
            if (!isset($data['email']) || empty($data['email'])) {
                return ['status'=>'error','message'=>'Email wajib di isi1414144','code'=>400];
            } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['status'=>'error','message'=>'Email yang anda masukkan invalid','code'=>400];
            }
            if (!isset($data['password']) || empty($data['password'])) {
                return ['status'=>'error','message'=>'Password wajib di isi','code'=>400];
            } else if (strlen($data['password']) < 8) {
                return ['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400];
            } else if (strlen($data['password']) > 25) {
                return ['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400];
            } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                return ['status' => 'error', 'message' => 'Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400];
            }
            if (!isset($data['password_confirm']) || empty($data['password_confirm'])) {
                return ['status'=>'error','message'=>'Password wajib di isi','code'=>400];
            } else if (strlen($data['password_confirm']) < 8) {
                return ['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400];
            } else if (strlen($data['password_confirm']) > 25) {
                return ['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400];
            } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_confirm'])) {
                return ['status' => 'error', 'message' => 'Password confirm harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400];
            }
            // Validate 'nama' field
            if (!isset($data['nama']) || empty($data['nama'])) {
                return ['status'=>'error','message'=>'Nama harus di isi','code'=>400];
            }
            $email = $data['email'];
            $pass = $data["password"];
            $pass1 = $data["password_confirm"];
            $query = "SELECT nama FROM users WHERE BINARY email = ?";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $name = '';
            $stmt->bind_result($name);
            if (!$stmt->fetch()) {
                $stmt->close();
                if($pass !== $pass1){
                    return ['status'=>'error','message'=>'Password harus sama','code'=>400];
                }else{
                    return $userController->createUser($data,'register');
                }
            }else{
                return ['status'=>'error','message'=>'Email sudah digunakan','code'=>400];
            }
        }catch(\Exception $e){
            echo $e->getTraceAsString();
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($erorr['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $erorr['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $erorr->message,
                    );
                }
            }
            return $responseData;
        }
    }
}
?>