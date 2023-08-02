<?php
// namespace Controllers\Auth;
use Database\DATABASE;
use Controllers\Mail\MailController;
use Controllers\UserController;
use Exception;
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
            $mailController = new MailController();
            // Validate 'email' field
            if (!isset($data['email']) || empty($data['email'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email wajib di isi','code'=>400]));
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email yang anda masukkan invalid','code'=>400]));
            }
            if (!isset($data['password']) || empty($data['password'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password wajib di isi','code'=>400]));
            } elseif (strlen($data['password']) < 8) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400]));
            } elseif (strlen($data['password']) > 25) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400]));
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Password harus mengandung setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400]));
            }
            if (!isset($data['password_confirm']) || empty($data['password_confirm'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password wajib di isi','code'=>400]));
            } elseif (strlen($data['password_confirm']) < 8) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400]));
            } elseif (strlen($data['password_confirm']) > 25) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400]));
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_confirm'])) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Password harus mengandung setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400]));
            }
            // Validate 'nama' field
            if (!isset($requestData['nama']) || empty($requestData['nama'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak ditemukan','code'=>400]));
            }
            $email = $data['email'];
            $pass = $data["password"];
            $pass1 = $data["password_confirm"];
            $query = "SELECT * FROM users WHERE BINARY email LIKE '%" . self::$con->real_escape_string($email) . "%' LIMIT 1";
            $stmt = self::$con->prepare($query);
            $email = '%' . $email . '%';
            $stmt->bind_param('s', $email);
            $stmt->execute();
            foreach ($data as $column) {
                $bindResultArray[] = &$$column;
            }
            call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
            $result = [];
            if (!$stmt->fetch()) {
                if($pass !== $pass1){
                    throw new Exception(json_encode(['status'=>'error','message'=>'Password harus sama','code'=>400]));
                }else{
                    $result = $userController->createUser($data, $mailController);
                    if($result['status'] == 'error'){
                        throw new Exception(json_encode($result));
                        // $error = json_decode($e->getMessage());
                        // $responseData = array(
                        //     'status' => 'error',
                        //     'message' => $result['message'],
                        // );
                        // $jsonResponse = json_encode($responseData);
                        // header('Content-Type: application/json');
                        // http_response_code(!empty($error['code']) ? $error['code'] : 400);
                        // echo $jsonResponse;
                    }else{
                        $responseData = array(
                            'status' => 'success',
                            'message' => 'register success ',
                        );
                        header('Content-Type: application/json');
                        return json_encode($responseData);
                    }
                }
            }else{
                throw new Exception(json_encode(['status'=>'error','message'=>'Email sudah digunakan','code'=>400]));
            }
        }catch(Exception $e){
            $error = json_decode($e->getMessage());
            $responseData = array(
                'status' => 'error',
                'message' => $error['message'],
            );
            $jsonResponse = json_encode($responseData);
            header('Content-Type: application/json');
            http_response_code(!empty($error['code']) ? $error['code'] : 400);
            echo $jsonResponse;
            exit();
        }
    }
}
?>