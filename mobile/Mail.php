<?php
require(__DIR__.'/../web/koneksi.php');
// namespace Controllers\Mail;
// require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Database\Database;
use Carbon\Carbon;
class MailMobile{ 
    protected $mail;
    private static $database;
    private static $con;
    private static $timeZone;
    public function __construct(){
        try {
            self::$timeZone = 'Asia/Jakarta';
            self::$database = Koneksi::getInstance();
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
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }

    public function send($data){
        try {
            // exit();
            $this->mail->setFrom($_SERVER['MAIL_FROM_ADDRESS'], 'gabutt');
            $this->mail->addAddress($data['email'], $data['name']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $data['deskripsi'];
            if($data['deskripsi'] == 'email'){
                $filePath = __DIR__ . '/../../view/mail/verifyEmail.php';
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
                $filePath = __DIR__ . '/../../view/mail/forgotPassword.php';
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
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email kosong'];
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
                    return ['status'=>'success','data'=>['kode_otp'=>$kode_otp,'link'=>$verificationLink]];
                }else{
                    $stmt[1]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }else{
                $stmt[0]->close();
                return ['status'=>'error','message'=>'email invalid'];
            }
        }
    }
    public function createVerifyEmail($data,$uri = null){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email wajib di isi'];
            }else{
                $currentDateTime = Carbon::now(self::$timeZone);
                $now = $currentDateTime->format('Y-m-d H:i:s');
                $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $result = '';
                $stmt[0]->bind_result($result);
                $stmt[0]->execute();
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    //create timeout
                    $subminute = $currentDateTime->subMinutes(15);
                    $query = "SELECT updated_at FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $deskripsi = 'email';
                    $stmt[1]->bind_param('ss', $email, $deskripsi);
                    $stmt[1]->execute();
                    //checking if email exist in table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT updated_at FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? AND updated_at >= ? LIMIT 1";
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
                            $query = "UPDATE verifikasi SET link = ?, kode_otp = ? updated_at = ? FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ssss',$verificationLink, $verificationCode, $email, $now, $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //update link
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $data = ['name'=>$result,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'email'];
                                //resend email
                                $result = $this->send($data);
                                if($result['status'] == 'error'){
                                    return ['status'=>'error','message'=>$result['message']];
                                }else{
                                    return ['status'=>'success','message'=>'success send verifikasi email','data'=>['waktu'=>$subminute]];
                                }
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'fail create verifikasi email'];
                            }
                        }else{
                            $stmt[2]->close();
                            return ['status'=>'error','message'=>'we have send verifikasi email'];
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
                        $query = "INSERT INTO verifikasi (email, kode_otp, link, deskripsi, created_at, updated_at,id_user) VALUES(?,?,?,?,?,?,?)";
                        $stmt[3] = self::$con->prepare($query);
                        $deskripsi = 'email';
                        $stmt[3]->bind_param("sssssss", $data['email'], $verificationCode, $linkPath, $deskripsi, $now, $now, $idUser);
                        $stmt[3]->execute();
                        if ($stmt[3]->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'email'];
                            $result = $this->send($data);
                            if($result['status'] == 'error'){
                                return ['status'=>'error','message'=>$result['message'],'kode_otp'=> isset($result['kode_otp']) ? $result['kode_otp'] : 400 ,'data'=>['waktu'=>$subminute]];
                            }else{
                                return ['status'=>'success','message'=>'Akun Berhasil Dibuat Silahkan verifikasi email','kode_otp'=>200,'data'=>['waktu'=>$subminute]];
                            }
                        }else{
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'fail create verifikasi email','kode_otp'=>500];
                        }
                    }
                }else{
                    $stmt[0]->close();
                    if($_SERVER['REQUEST_URI']->path() === 'verifikasi/create/email' && $_SERVER['REQUEST_METHOD'] === 'get'){
                        return ['status'=>'error','message'=>'email invalid'];
                    }else{
                        return ['status'=>'error','message'=>'email invalid','kode_otp'=>400];
                    }
                }    
            }
        }catch(Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    //send email forgot password
    public function createForgotPassword($data, $uri = null){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email empty'];
            }else{
                //checking if email exist in table user
                $currentDateTime = Carbon::now(self::$timeZone);
                $now = $currentDateTime->format('Y-m-d H:i:s');
                $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $result = '';
                $stmt[0]->bind_result($result);
                $stmt[0]->execute();
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    //checking if email exist in table verifikasi
                    $stmt[0]->close();
                    //create timeout
                    $subminute = $currentDateTime->subMinutes(15);
                    $query = "SELECT updated_at FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $deskripsi = 'password';  
                    $stmt[1]->bind_param('ss', $email, $deskripsi);
                    $stmt[1]->execute();
                    //checking if email exist in table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT updated_at FROM verifikasi WHERE BINARY email = ? AND deskripsi = ? AND updated_at >= ? LIMIT 1";
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
                            $query = "UPDATE verifikasi SET link = ?, kode_otp = ?, updated_at = ? WHERE BINARY email = ? AND deskripsi = 'password' LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ssss',$linkPath, $verificationCode, $now, $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //update link
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $data = ['name'=>$result,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'password'];
                                //resend email
                                $result = $this->send($data);
                                if($result['status' == 'error']){
                                    return ['status'=>'error','message'=>$result['message'],'kode_otp'=>isset($result['kode_otp']) ? $result['kode_otp'] : 400,'data'=>['waktu'=>$subminute]];
                                }else{
                                    return ['status'=>'success','message'=>'success send reset Password','data'=>['waktu'=>$subminute]];
                                }
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'fail create verifikasi email','kode_otp'=>500];
                            }
                        }else{
                            $stmt[2]->close();
                            return ['status'=>'error','message'=>'Kami sudah mengirimkan otp lupa password silahkan cek mail anda'];
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
                        $query = "INSERT INTO verifikasi (email, kode_otp, link, deskripsi, created_at, updated_at,id_user) VALUES(?,?,?,?,?,?,?)";
                        $stmt[3] = self::$con->prepare($query);
                        $deskripsi = 'password';
                        $stmt[3]->bind_param("sssssss", $data['email'], $verificationCode, $linkPath, $deskripsi, $now, $now, $idUser);
                        $stmt[3]->execute();
                        if ($stmt[3]->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'kode_otp'=>$verificationCode,'link'=>urldecode($verificationLink),'deskripsi'=>'password'];
                            $result = $this->send($data);
                            if($result['status'] == 'error'){
                                return ['status'=>'error','message'=>$result['message'],'kode_otp'=>isset($result['kode_otp']) ? $result['kode_otp'] : 400,'data'=>['waktu'=>$subminute]];
                            }else{
                                return ['status'=>'success','message'=>'Reset password sudah dikirim ','kode_otp'=>200,'data'=>['waktu'=>$subminute]];
                            }
                        }else{
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'fail create verifikasi email','kode_otp'=>500];
                        }
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        }catch(\Exception $e){
            echo $e->getTraceAsString();
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public function email($data, $uri,$method){
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'Email empty','kode_otp'=>400];
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
                            return ['status'=>'success','message'=>'email verifikasi success'];
                        }else{
                            $stmt[1]->close();
                            // return redirect('/login');
                            return ['status'=>'error','message'=>'Email invalid','kode_otp'=>500];
                        }
                    }else{
                        return ['status'=>'error','message'=>'email invalid','kode_otp'=>400];
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'link invalid','kode_otp'=>400];
                }
            }else{
                return ['status'=>'error','message'=>'not found'];
            }
        }
    }
}
?>