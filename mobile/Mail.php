<?php
require(__DIR__.'/../web/koneksi.php');
require(__DIR__.'/../vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;
class MailMobile{ 
    protected $mail;
    private static $database;
    private static $con;
    private static $timeZone = 'Asia/Jakarta';
    private static $sendTime = [5, 15, 30, 60, 6*60, 12*60, 24*60];
    private static function loadEnv($path = null){
        if($path == null){
            $path = __DIR__."/../web/.env";
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
    }
    public function testing(){
        $mail = new PHPMailer(true);
        $mail->Host = 'smtp.gmail.com';
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = 'amirzanfikri5@gmail.com';
        $mail->Password = 'vamvwrdfyewbhkca';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        try {
            $mail->setFrom($_SERVER['MAIL_FROM_ADDRESS'], 'gabutt');
            $mail->Body = 'This is the email body content.';
            $mail->addAddress('amirzanfikri5@gmail.com','Amirzan');
            $mail->send();
            echo 'Email sent successfully';
        } catch (Exception $e) {
            echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }
    public function __construct(){
        try {
            self::loadEnv();
            self::$database = koneksi::getInstance();
            self::$con = self::$database->getConnection();
            $this->mail = new PHPMailer(true);
            $this->mail->Host = $_SERVER['MAIL_HOST'];
            $this->mail->isSMTP();
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_SERVER['MAIL_USERNAME'];
            $this->mail->Password = $_SERVER['MAIL_PASSWORD'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $_SERVER['MAIL_PORT'];
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
            // echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
    public function send($data){
        try {
            $this->mail->setFrom($_SERVER['MAIL_FROM_ADDRESS'], 'gabutt');
            $this->mail->addAddress($data['email'], $data['name']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $data['deskripsi'];
            if($data['deskripsi'] == 'email'){
                $filePath = __DIR__ . '/../mobile/Mail/verifikasiEmail.php';
                $emailBody = file_get_contents($filePath);
                $emailData = [
                    'EMAIL' => $data['email'],
                    'CODE' => $data['kode_otp'],
                    'LINK' => $data['link'],
                ];
                $emailTemplate = $emailBody; 
                foreach ($emailData as $key => $value) {
                    $placeholder = '%' . strtoupper($key) . '%';
                    $emailTemplate = str_replace($placeholder, $value, $emailTemplate);
                }
                $this->mail->Body = $emailTemplate;
            }else if($data['deskripsi'] == 'password'){
                $filePath = __DIR__ . '/../mobile/mail/lupaPassword.php';
                $emailBody = file_get_contents($filePath);
                $emailData = [
                    'EMAIL' => $data['email'],
                    'CODE' => $data['kode_otp'],
                    'LINK' => $data['link'],
                ];
                $emailTemplate = $emailBody; 
                foreach ($emailData as $key => $value) {
                    $placeholder = '%' . strtoupper($key) . '%';
                    $emailTemplate = str_replace($placeholder, $value, $emailTemplate);
                }
                $this->mail->Body = $emailTemplate;
            }
            $this->mail->send();
            return ['status'=>'success', 'message'=>'Email sent successfully!'];
        } catch (Exception $e) {
            return ['status'=>'error','message'=>"Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}"];
        }
    }
    public function getVerifyEmail($data){
        try{
            $email = $data['email'];
            if(empty($email) || empty($email)){
                throw new Exception('Email harus di isi !');
            }else{
                //check email exist in table user
                $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $result = '';
                $stmt[0]->bind_result($result);
                $stmt[0]->execute();
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    //checking if email exist in table verifikasi
                    $query = "SELECT kode_otp,link FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $deskripsi = 'email';
                    $stmt[1]->bind_param('ss', $email, $deskripsi);
                    $kode_otp = ''; $link = '';
                    $stmt[1]->bind_result($kode_otp, $link);
                    $stmt[1]->execute();
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'];
                        $baseURL = $protocol . '://' . $host;
                        $verificationLink = $baseURL . '/verifikasi/email/' . $link;
                        header('Content-Type: application/json');
                        echo json_encode(['status'=>'success','data'=>['kode_otp'=>$kode_otp,'link'=>$verificationLink]]);
                    }else{
                        $stmt[1]->close();
                        throw new Exception('Email tidak ditemukan');
                    }
                }else{
                    $stmt[0]->close();
                    throw new Exception('Email tidak ditemukan');
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
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson['message'],
                    );
                }
                header('Content-Type: application/json');
                isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
                echo json_encode($responseData);
                exit();
        }
    }
    public function createVerifyEmail($data,$uri = null){
        try{
            $email = $data['email'];
            if(!isset($email) || empty($email) || is_null($email)){
                throw new Exception('Email harus di isi !');
            }else{
                //check email exist in table user
                $currentDateTime = Carbon::now(self::$timeZone);
                $now = $currentDateTime->format('Y-m-d H:i:s');
                $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $namaLengkap = '';
                $stmt[0]->bind_result($namaLengkap);
                $stmt[0]->execute();
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    //checking if email exist in table verifikasi
                    $query = "SELECT updated_at, send FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $deskripsi = 'email';
                    $stmt[1]->bind_param('ss', $email, $deskripsi);
                    $timeUpdate = '';
                    $sendd = '';
                    $stmt[1]->bind_result($timeUpdate,$sendd);
                    $stmt[1]->execute();
                    $subminute = $currentDateTime->subMinutes(15);
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                         // If $now is after 15 minutes from $timeUpdate, then resend is expired.
                        $databaseTime = Carbon::parse($timeUpdate);
                        $remaining = $currentDateTime->diffInMinutes($databaseTime);
                        if ($remaining < 15) {
                            throw new Exception(json_encode(['status'=>'error','message'=>'Kami sudah kirim kode otp','data'=>$remaining]));
                        }
                        $subminute = $currentDateTime->subMinutes(self::$sendTime[$sendd]);
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('sss', $email, $deskripsi, $subminute);
                        $stmt[2]->execute();
                        //checking if user have create verifikasi email
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            //if after 15 minute then update kode_otp
                            $verificationCode = mt_rand(100000, 999999);
                            $linkPath = bin2hex(random_bytes(50 / 2));
                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                            $host = $_SERVER['HTTP_HOST'];
                            $baseURL = $protocol . '://' . $host;
                            $verificationLink = $baseURL . '/verifikasi/email/' . $linkPath;
                            $query = "UPDATE verifikasi SET link = ?, kode_otp = ?, send = ?, updated_at = ? FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            if($sendd < (count(self::$sendTime) - 1)){
                                $sendd++;
                            }
                            $stmt[3]->bind_param('sssss',$verificationLink, $verificationCode, $sendd, $now, $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //update link
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $data = ['name'=>$namaLengkap,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'email'];
                                //resend email
                                $result = $this->send($data);
                                if($result['status'] == 'error'){
                                    throw new Exception($result['message']);
                                }else{
                                    header('Content-Type: application/json');
                                    echo json_encode(['status'=>'success','message'=>'success send verifikasi email','data'=>['waktu'=>$subminute]]);
                                }
                            }else{
                                $stmt[3]->close();
                                throw new Exception(json_encode(['status' => 'error', 'message' => 'verifikasi gagal dibuat','code'=>500]));
                            }
                        }else{
                            $stmt[2]->close();
                            throw new Exception('Kami sudah kirim verifikasi email !');
                        }
                    //if user not create verifikasi email
                    }else{
                        $stmt[1]->close();
                        $verificationCode = mt_rand(100000, 999999);
                        $linkPath = bin2hex(random_bytes(50 / 2));
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'];
                        $baseURL = $protocol . '://' . $host;
                        $verificationLink = $baseURL . '/verifikasi/email/' . $linkPath;
                        //get id_user
                        $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('s', $data['email']);
                        $stmt[2]->execute();
                        $idUser = '';
                        $stmt[2]->bind_result($idUser);
                        $stmt[2]->fetch();
                        $stmt[2]->close();
                        //insert data
                        $query = "INSERT INTO verifikasi (email, kode_otp, link, deskripsi, send, created_at, updated_at,id_user) VALUES(?,?,?,?,?,?,?,?)";
                        $stmt[3] = self::$con->prepare($query);
                        $deskripsi = 'email';
                        $send = 0;
                        $stmt[3]->bind_param("ssssssss", $data['email'], $verificationCode, $linkPath, $deskripsi, $send, $now, $now, $idUser);
                        $stmt[3]->execute();
                        if ($stmt[3]->affected_rows > 0) {
                            $data = ['name'=>$namaLengkap,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'email'];
                            $result = $this->send($data);
                            if($result['status'] == 'error'){
                                throw new Exception(json_encode(['status'=>'error','message'=>$result['message'],'kode_otp'=> isset($result['kode_otp']) ? $result['kode_otp'] : 400 ,'data'=>['waktu'=>$subminute]]));
                            }else{
                                header('Content-Type: application/json');
                                echo json_encode(['status'=>'success','message'=>'Akun Berhasil Dibuat Silahkan verifikasi email','kode_otp'=>200,'data'=>['waktu'=>$subminute]]);
                            }
                        }else{
                            $stmt[3]->close();
                        }
                    }
                }else{
                    $stmt[0]->close();
                    if($_SERVER['REQUEST_URI']->path() === 'verifikasi/create/email' && $_SERVER['REQUEST_METHOD'] === 'get'){
                    }else{
                    }
                }    
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    //send email forgot password
    public function createForgotPassword($data, $uri = null){
        try{
            $email = $data['email'];
            if(!isset($email) || empty($email) || is_null($email)){
                throw new Exception('Email harus di isi !');
            }else{
                //checking if email exist in table user
                $currentDateTime = Carbon::now(self::$timeZone);
                $now = $currentDateTime->format('Y-m-d H:i:s');
                $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $namaLengkap = '';
                $stmt[0]->bind_result($namaLengkap);
                $stmt[0]->execute();
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    //checking if email exist in table verifikasi
                    $query = "SELECT updated_at, send FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $deskripsi = 'password';  
                    $stmt[1]->bind_param('ss', $email, $deskripsi);
                    $timeUpdate = '';
                    $sendd = '';
                    $stmt[1]->bind_result($timeUpdate,$sendd);
                    $stmt[1]->execute();
                    $subminute = $currentDateTime->subMinutes(15);
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        // If $now is after 15 minutes from $timeUpdate, then resend is expired.
                        $databaseTime = Carbon::parse($timeUpdate);
                        $remaining = $currentDateTime->diffInMinutes($databaseTime);
                        if ($remaining > 15) {
                            throw new Exception(json_encode(['status'=>'error','message'=>'Kami sudah kirim kode otp','data'=>$remaining]));
                        }
                        $subminute = $currentDateTime->subMinutes(self::$sendTime[$sendd]);
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('sss', $email, $deskripsi, $subminute);
                        $stmt[2]->execute();
                        //checking if user have create verifikasi email
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            //if after 15 minute then update kode_otp
                            $verificationCode = mt_rand(100000, 999999);
                            $linkPath = bin2hex(random_bytes(50 / 2));
                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                            $host = $_SERVER['HTTP_HOST'];
                            $baseURL = $protocol . '://' . $host;
                            $verificationLink = $baseURL . '/verifikasi/password/' . $linkPath;
                            $query = "UPDATE verifikasi SET link = ?, kode_otp = ?, send = ?, updated_at = ? WHERE BINARY email = ? AND deskripsi = 'password' LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            if($sendd < (count(self::$sendTime) - 1)){
                                $sendd++;
                            }
                            $stmt[3]->bind_param('sssss',$linkPath, $verificationCode, $sendd, $now, $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //update link
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $data = ['name'=>$namaLengkap,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'password'];
                                //resend email
                                $result = $this->send($data);
                                if($result['status'] == 'error'){
                                    throw new Exception(json_encode(['status'=>'error','message'=>$result['message'],'kode_otp'=>isset($result['kode_otp']) ? $result['kode_otp'] : 400,'data'=>['waktu'=>$subminute]]));
                                }else{
                                    header('Content-Type: application/json');
                                    echo json_encode(['status'=>'success','message'=>'success send reset Password','data'=>['waktu'=>$subminute]]);
                                }
                            }else{
                                $stmt[3]->close();
                                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal buat lupa password','code'=>500]));
                            }
                        }else{
                            $stmt[2]->close();
                            throw new Exception('Kami sudah mengirimkan otp lupa password silahkan cek mail anda');
                        }
                    //if user haven't create email forgot password
                    }else{
                        $stmt[1]->close();
                        $verificationCode = mt_rand(100000, 999999);
                        $linkPath = bin2hex(random_bytes(50 / 2));
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'];
                        $baseURL = $protocol . '://' . $host;
                        $verificationLink = $baseURL . '/verifikasi/password/' . $linkPath;
                        //get id_user
                        $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('s', $data['email']);
                        $stmt[2]->execute();
                        $idUser = '';
                        $stmt[2]->bind_result($idUser);
                        $stmt[2]->fetch();
                        $stmt[2]->close();
                        //insert data
                        $query = "INSERT INTO verifikasi (email, kode_otp, link, deskripsi, send, created_at, updated_at,id_user) VALUES(?,?,?,?,?,?,?,?)";
                        $stmt[3] = self::$con->prepare($query);
                        $deskripsi = 'password';
                        $send = 0;
                        $stmt[3]->bind_param("ssssssss", $data['email'], $verificationCode, $linkPath, $deskripsi, $send, $now, $now, $idUser);
                        $stmt[3]->execute();
                        if ($stmt[3]->affected_rows > 0) {
                            $data = ['name'=>$namaLengkap,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'password'];
                            $result = $this->send($data);
                            if($result['status'] == 'error'){
                                throw new Exception(json_encode(['status'=>'error','message'=>$result['message'],'kode_otp'=>isset($result['kode_otp']) ? $result['kode_otp'] : 400,'data'=>['waktu'=>$subminute]]));
                            }else{
                                header('Content-Type: application/json');
                                echo json_encode(['status'=>'success','message'=>'Reset password sudah dikirim','data'=>['waktu'=>$subminute]]);
                            }
                        }else{
                            $stmt[3]->close();
                            throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal buat lupa password','code'=>500]));
                        }
                    }
                }else{
                    $stmt[0]->close();
                    throw new Exception('Email tidak ditemukan');
                }
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function email($data, $uri,$method){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                throw new Exception('Email harus di isi !');
            }else{
                $prefix = "/verifikasi/email/";
                if(($uri === $prefix) && $method === "post"){
                    $linkPath = substr($uri, strlen($prefix));
                    $query = "SELECT email FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                    $stmt[0] = self::$con->prepare($query);
                    $stmt[0]->bind_param('s', $linkPath);
                    $email1 = '';
                    $stmt[0]->bind_result($email1);
                    $stmt[0]->execute();
                    //checking if email exist in table verifikasi
                    if ($stmt[0]->fetch()) {
                        $stmt[0]->close();
                        //check email is same
                        if($email === $email1){
                            $query = "UPDATE users SET email_verified = ? FROM users WHERE BINARY email = ? LIMIT 1";
                            $stmt[1] = self::$con->prepare($query);
                            $verified = true;
                            $stmt[1]->bind_param('bs',$verified, $email);
                            $stmt[1]->execute();
                            $affectedRows = $stmt[1]->affected_rows;
                            //update link
                            if ($affectedRows > 0) {
                                $stmt[1]->close();
                                header('Content-Type: application/json');
                                echo json_encode(['status'=>'success','message'=>'email verifikasi success']);
                            }else{
                                $stmt[1]->close();
                                throw new Exception(json_encode(['status' => 'error', 'message' => 'kode otp gagal diupdate','code'=>500]));
                            }
                        }else{
                            throw new Exception('Email invalid');
                        }
                    }else{
                        $stmt[0]->close();
                        throw new Exception('Link invalid');
                    }
                }else{
                    include(__DIR__.'/../notfound.php');
                    exit();
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
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public static function handle(){
        $contentType = $_SERVER["CONTENT_TYPE"];
        if ($contentType === "application/json") {
            $rawData = file_get_contents("php://input");
            $requestData = json_decode($rawData, true);
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } else if ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } else if (strpos($contentType, 'multipart/form-data') !== false) {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mailMobile = new MailMobile();
    $data = MailMobile::handle();
    if(isset($data['desc']) && !is_null($data['desc']) && !empty($data['desc'])){
        if($data['desc'] == 'email'){
            $mailMobile->createVerifyEmail($data);
        }else if($data['desc'] == 'password'){
            $mailMobile->createForgotPassword($data);
        }else if($data['desc'] == 'random'){
            $mailMobile->testing();
        }
    }
    // if(isset($data['_method'])){
    //     if($data['_method'] == 'PUT'){
    //         $mailMobile->editSewaTempat($data);
    //     }
    //     if($data['_method'] == 'DELETE'){
    //         $mailMobile->hapusSewaTempat($data);
    //     }
    // }else{
    //     $mailMobile->sewaTempat($data);
    // }
}
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    // $mailMobile = new MailMobile();
    // $mailMobile->editSewaTempat(MailMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    // $mailMobile = new MailMobile();
    // $mailMobile->hapusSewaTempat(MailMobile::handle());
}
?>