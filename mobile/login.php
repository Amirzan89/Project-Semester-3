<?php
require(__DIR__.'/../web/koneksi.php');
require(__DIR__.'/../web/Jwt.php');
function loadEnv($path = null){
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
function Login($data){
    try{
        $email = $data["email"];
        // $email = "Admin@gmail.com";
        // $pass = $data["password"];
        // $data['password'] = "Admin@1234567890";
        if(!isset($email) || empty($email)){
            throw new Exception('Email harus di isi !');
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalid !');
        }
        $db = koneksi::getInstance();
        $con = $db->getConnection();
        if($data['desc'] == 'google'){
            $query = "SELECT * FROM users WHERE BINARY email = ?";
            $stmt[0] = $con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            if(!$stmt[0]->execute()){
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan !','kode'=>2]));
            }
            $result = $stmt[0]->get_result();
            $usersData = $result->fetch_assoc();
            $stmt[0]->close();
            if ($usersData === null) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Pengguna tidak ditemukan','kode'=>2]));
            }
            if(in_array($usersData['role'],['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Role Invalid','kode'=>2]));
            }
            unset($usersData['password']);
            unset($usersData['foto']);
            header('Content-Type: application/json');
            echo json_encode(['status'=>'success','message'=>'Data Tersedia', 'kode'=>1, 'data'=>$usersData]);
            exit();
        }else if($data['desc'] == 'login'){
            if(!isset($data['password']) || empty($data['password'])){
                throw new Exception('Password harus di isi !');
            }
            $query = "SELECT * FROM users WHERE BINARY email = ?";
            $stmt[0] = $con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            if(!$stmt[0]->execute()){
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan !','kode'=>2]));
            }
            $result = $stmt[0]->get_result();
            $usersData = $result->fetch_assoc();
            $stmt[0]->close();
            if ($usersData === null) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Pengguna tidak ditemukan','kode'=>2]));
            }
            if(!password_verify($data['password'],$usersData['password'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Password Salah','kode'=>2]));
            }
            if(in_array($usersData['role'],['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Role invalid','kode'=>3]));
            }
            unset($usersData['password']);
            unset($usersData['foto']);
            header('Content-Type: application/json');
            echo json_encode(['status'=>'success','message'=>'Data Tersedia', 'kode'=>1, 'data'=>$usersData]);
            exit();
        }
    }catch(Exception $e){
        $error = $e->getMessage();
        $errorJson = json_decode($error, true);
        if ($errorJson === null) {
            $responseData = array(
                'status' => 'error',
                'pesan' => $error,
                'kode'=>2
            );
        }else{
            if($errorJson['message']){
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }
        }
        header('Content-Type: application/json');
        isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
        echo json_encode($responseData);
        exit();
    }
}
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
?>