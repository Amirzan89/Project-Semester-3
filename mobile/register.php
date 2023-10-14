<?php
require(__DIR__.'/../web/koneksi.php');
require(__DIR__.'/../web/User.php');
function Register($data,$con){
    try{
        if (!isset($data['email']) || empty($data['email'])) {
            throw new Exception('Email harus di isi !');
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email invalid !');
        }
        if (!isset($data['password']) || empty($data['password'])) {
            throw new Exception('Password harus di isi !');
        } else if (strlen($data['password']) < 8) {
            throw new Exception('Password minimal 8 karakter !');
        } else if (strlen($data['password']) > 25) {
            throw new Exception('Password maksimal 25 karakter !');
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
            throw new Exception('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
        }
        if (!isset($data['password_confirm']) || empty($data['password_confirm'])) {
            throw new Exception('Password konfirmasi harus di isi');
        } else if (strlen($data['password_confirm']) < 8) {
            throw new Exception('Password konfirmasi minimal 8 karakter !');
        } else if (strlen($data['password_confirm']) > 25) {
            throw new Exception('Password konfirmasi maksimal 25 karakter !');
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_confirm'])) {
            throw new Exception('Password konfirmasi harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
        }
        if (!isset($data['nama']) || empty($data['nama'])) {
            throw new Exception('Nama harus di isi');
        }
        $email = $data['email'];
        $pass = $data["password"];
        $pass1 = $data["password_confirm"];
        $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ?";
        $db = koneksi::getInstance();
        $con = $db->getConnection();
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $name = '';
        $stmt->bind_result($name);
        if (!$stmt->fetch()) {
            $stmt->close();
            if($pass !== $pass1){
                throw new Exception('Password harus sama');
            }else{
                $user = new UserMobile();
                // echo 'tambah database';
                return $user->createUser($data,'register');
            }
        }else{
            throw new Exception('Email sudah digunakan');
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
    Register($data,$con);
}
if(isset($_POST['register'])){
    Register($_POST,$con);
}
?>