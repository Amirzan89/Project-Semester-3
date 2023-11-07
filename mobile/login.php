<?php
require(__DIR__.'/../web/koneksi.php');
require(__DIR__.'/../web/Jwt.php');
$loadEnv = function($path = null){
    if($path == null){
        $path = __DIR__."/../.env";
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
};
function Login($data,$loadEnv){
    try{
        $email = $data["email"];
        // $email = "Admin@gmail.com";
        $pass = $data["password"];
        $pass = "Admin@1234567890";
        if(!isset($email) || empty($email)){
            throw new Exception('Email harus di isi !');
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalid !');
        }else if(!isset($pass) || empty($pass)){
            throw new Exception('Password harus di isi !');
        }else{
            $db = koneksi::getInstance();
            $con = $db->getConnection();
            $query = "SELECT password FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = $con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            $stmt[0]->execute();
            $passDb = '';
            $stmt[0]->bind_result($passDb);
            if ($stmt[0]->fetch()) {
                if(!password_verify($pass,$passDb)){
                    $stmt[0]->close();
                    throw new Exception('Password salah !');
                }else{
                    $stmt[0]->close();
                    $result = Jwt::createToken($data,$con,$loadEnv);
                    if(is_null($result)){
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'create token error','code'=>500]));
                    }else{
                        if($result['status'] == 'error'){
                            throw new Exception(json_encode($result));
                        }else{
                            $loadEnv();
                            // $data1 = ['email'=>$email,'number'=>$result['number'],'expire'=>time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])];
                            // $encoded = base64_encode(json_encode($data1));
                            // setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                            // setcookie('token2', $result['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                            // setcookie('token3', $result['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                            // header('Location: /dashboard.php');
                            header('Content-Type: application/json');
                            echo json_encode(['status'=>'success','message'=>'login berhasil']);
                        }
                    }
                }
            }else{
                $stmt[0]->close();
                throw new Exception('Email tidak ditemukan !');
            }
        }
    }catch(Exception $e){
        echo $e->getTraceAsString();
        $error = $e->getMessage();
        $errorJson = json_decode($error, true);
        if ($errorJson === null) {
            $responseData = array(
                'status' => 'error',
                'message' => $error,
            );
        }else{
            if($errorJson['message']){
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson->message,
                );
            }
        }
        header('Content-Type: application/json');
        isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
        echo json_encode($responseData);
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = file_get_contents("php://input");
    $data = json_decode($input_data, true);
    // echo json_encode($data);
    // exit();
    Login($data,$loadEnv);
}
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    header('Location: /');
}
?>