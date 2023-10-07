<?php
require(__DIR__.'/../web/koneksi.php');
require(__DIR__.'/../web/User.php');
function Register($data,$con){
    echo 'mlebu register';
    echo "<br>";
    try{
        if (!isset($data['email']) || empty($data['email'])) {
            echo "<script>alert('Email harus di isi')</script>";
            exit();
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Email yang anda masukkan invalid')</script>";
            exit();
        }
        if (!isset($data['password']) || empty($data['password'])) {
            echo "<script>alert('Password harus di isi')</script>";
            exit();
        } else if (strlen($data['password']) < 8) {
            echo "<script>alert('Password minimal 8 karakter')</script>";
            exit();
        } else if (strlen($data['password']) > 25) {
            echo "<script>alert('Password maksimal 25 karakter')</script>";
            exit();
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
            echo "<script>alert('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka')</script>";
            exit();
        }
        if (!isset($data['password_confirm']) || empty($data['password_confirm'])) {
            echo "<script>alert('Password konfirmasi harus di isi')</script>";
            exit();
        } else if (strlen($data['password_confirm']) < 8) {
            echo "<script>alert('Password minimal 8 karakter')</script>";
            exit();
        } else if (strlen($data['password_confirm']) > 25) {
            echo "<script>alert('Password maksimal 25 karakter')</script>";
            exit();
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_confirm'])) {
            echo "<script>alert('Password konfirmasi harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka')</script>";
            exit();
        }
        if (!isset($data['nama']) || empty($data['nama'])) {
            echo "<script>alert('Nama harus di isi')</script>";
            header('Location:/register.php');
            exit();
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
                header('Location:/register.php');
                echo "<script>alert('Password harus sama')</script>";
                exit();
            }else{
                echo 'tambah database';
                return createUser($data,'register',$con);
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo 'kenek sakjane';
    $input_data = file_get_contents("php://input");
    $data = json_decode($input_data, true);
    Register($data,$con);
}
if(isset($_POST['register'])){
    Register($_POST,$con);
}
?>