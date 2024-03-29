<?php
require_once(__DIR__.'/koneksi.php');
require_once(__DIR__.'/Jwt.php');
function loadEnv($path = null) {
    $path = $path ?: __DIR__ . '/../.env';

    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
                $_SERVER[trim($key)] = trim($value);
                $_SERVER['LOAD_ENV'] = true;
            }
        }
    }
}

function createConn() {
    loadEnv();

    $conn = new \mysqli(
        'p:' . $_SERVER['DB_HOST'] . ':' . $_SERVER['DB_PORT'],
        $_SERVER['DB_USERNAME'],
        $_SERVER['DB_PASSWORD'],
        $_SERVER['DB_DATABASE']
    );

    if ($conn->connect_error) {
        throw new Exception("Tidak bisa membuat koneksi");
    }

    return $conn;
}

// Usage example:
// try {
//     $conn = createConn();
//     // Now you can use $conn for database operations
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

$loadEnv = function($path = null){
    if($path == null){
        $path = ".env";
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
// echo json_encode($_POST);
//protection
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
if(isset($_POST['login'])){
    try{
        $email = $_POST["email"];
        $pass = $_POST["password"];
        $pass = "Admin@1234567890";
        if(!isset($email) || empty($email)){
            echo "<script>alert('Email tidak boleh kosong')</script>";
            echo "<script>window.history.back();</script>"; 
            exit();
        } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Email yang anda masukkan ivalid')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }else if(!isset($pass) || empty($pass)){
            echo "<script>alert('Password tidak boleh kosong')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }else{
            // $loadEnv();
            $con = createConn();
            // $db = Koneksi::getInstance();
            // $con = $db->getConnection();
            $query = "SELECT role, password FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = $con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            $stmt[0]->execute();
            $roleDB = '';
            $passDb = '';
            $stmt[0]->bind_result($roleDB, $passDb);
            if ($stmt[0]->fetch()) {
                //check role user
                if($roleDB === 'masyarakat'){
                    $stmt[0]->close();
                    echo "<script>alert('Anda bukan admin')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                //check password
                if(!password_verify($pass,$passDb)){
                    $stmt[0]->close();
                    echo "<script>alert('Password salah')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $stmt[0]->close();
                $jwt = new Jwt();
                $result = $jwt->createToken($_POST,$loadEnv);
                if(is_null($result)){
                    echo "<script>alert('Create token error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }else{
                    if($result['status'] == 'error'){
                        echo json_encode($result);
                        exit();
                    }else{
                        $data1 = ['email'=>$email,'number'=>$result['number'],'expire'=>time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])];
                        $encoded = base64_encode(json_encode($data1));
                        header('Content-Type: application/json');
                        setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                        setcookie('token2', $result['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                        setcookie('token3', $result['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                        header('Location: /dashboard.php');
                    }
                }
            }else{
                $stmt[0]->close();
                echo "<script>alert('Pengguna tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
        }
    }catch(Exception $e){
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
        echo "<script>alert('".json_encode($responseData)."')</script>";
        exit();
    }
}
?>