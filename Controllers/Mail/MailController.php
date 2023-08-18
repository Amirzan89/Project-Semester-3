<?php
namespace Controllers\Mail;
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Database\Database;
use Carbon\Carbon;
class MailController{ 
    protected $mail;
    private static $database;
    private static $con;
    private static $timeZone;
    public function __construct(){
        try {
            self::$timeZone = 'Asia/Jakarta';
            self::$database = Database::getInstance();
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
            $this->mail->setFrom($_SERVER['MAIL_FROM_ADDRESS'], 'gabutt');
            $this->mail->addAddress($data['email'], $data['name']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $data['description'];
            if($data['description'] == 'verifyEmail'){
                $filePath = __DIR__ . '/../../view/mail/verifyEmail.php';
                $emailBody = file_get_contents($filePath);
                $this->mail->Body = $emailBody;
            }else if($data['description'] == 'changePass'){
                $filePath = __DIR__ . '/../../view/mail/forgotPassword.php';
                $emailBody = file_get_contents($filePath);
                $this->mail->Body = $emailBody;
            }
            $this->mail->send();
            echo 'Email sent successfully!';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
    public function getVerifyEmail($data){
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            return json_encode(['status'=>'success','message'=>'email empty']);
        }else{
            //check email exist in table user
            $query = "SELECT nama FROM users WHERE BINARY email LIKE ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $email1 = '%' . $email . '%';
            $stmt[0]->bind_param('s', $email1);
            $result = '';
            $stmt[0]->bind_result($result);
            $stmt[0]->execute();
            if ($stmt[0]->fetch()) {
                $stmt[0]->close();
                //checking if email exist in table verify
                $query = "SELECT code,link FROM verify WHERE BINARY email LIKE ? AND description = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $description = 'verifyEmail';
                $stmt[1]->bind_param('ss', $email1, $description);
                $code = ''; $link = '';
                $stmt[1]->bind_result($code, $link);
                $stmt[1]->execute();
                if ($stmt[1]->fetch()) {
                    $stmt[1]->close();
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'];
                    $baseURL = $protocol . '://' . $host;
                    $verificationLink = $baseURL . '/verify/email/' . $link;
                    return json_encode(['status'=>'error','data'=>['code'=>$code,'link'=>$verificationLink]]);
                }else{
                    $stmt[1]->close();
                    return json_encode(['status'=>'error','message'=>'email invalid']);
                }
            }else{
                $stmt[0]->close();
                return json_encode(['status'=>'error','message'=>'email invalid']);
            }
        }
    }
    public function createVerifyEmail($data){
        $stmt = array();
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email wajib di isi'];
            }else{
                $currentDateTime = Carbon::now(self::$timeZone);
                $query = "SELECT nama FROM users WHERE BINARY email LIKE ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $email1 = '%' . $email . '%';
                $stmt[0]->bind_param('s', $email1);
                $result = '';
                $stmt[0]->bind_result($result);
                $stmt[0]->execute();
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    //create timeout
                    $subminute = $currentDateTime->subMinutes(15);
                    $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $description = 'verifyEmail';
                    $stmt[1]->bind_param('ss', $email1, $description);
                    $stmt[1]->execute();
                    //checking if email exist in table verify
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('sss', $email1, $description, $subminute);
                        $stmt[2]->execute();
                        //checking if user have create verify email
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            //if after 15 minute then update code
                            $verificationCode = mt_rand(100000, 999999);
                            $linkPath = bin2hex(random_bytes(50 / 2));
                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                            $host = $_SERVER['HTTP_HOST'];
                            $baseURL = $protocol . '://' . $host;
                            $verificationLink = $baseURL . '/verify/email/' . $linkPath;
                            $query = "UPDATE verify SET link = ?, code = ? updated_at = ? FROM verify WHERE BINARY email LIKE ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ssss',$verificationLink, $verificationCode, $email1, $currentDateTime, $email1);
                            $stmt[3]->execute();
                            //update link
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'verifyEmail'];
                                //resend email
                                $this->send($data);
                                return ['status'=>'success','message'=>'success send verify email','data'=>['waktu'=>$subminute]];
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'fail create verify email'];
                            }
                        }else{
                            $stmt[2]->close();
                            return ['status'=>'error','message'=>'we have send verify email'];
                        }
                    }else{
                        $stmt[1]->close();
                        $verificationCode = mt_rand(100000, 999999);
                        $linkPath = bin2hex(random_bytes(50 / 2));
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'];
                        $baseURL = $protocol . '://' . $host;
                        $verificationLink = $baseURL . '/verify/email/' . $linkPath;
                        $query = "INSERT INTO verify (email, code, link, description, created_at, updated_at) VALUES(?,?,?,?,?,?)";
                        $stmt[2] = self::$con->prepare($query);
                        $description = 'verifyEmail';
                        $stmt[2]->bind_param("ssssss", $data['email'], $verificationCode, $linkPath, $description, $currentDateTime, $currentDateTime);
                        $stmt[2]->execute();
                        if ($stmt[2]->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'verifyEmail'];
                            $this->send($data);
                            return ['status'=>'Success','message'=>'Akun Berhasil Dibuat Silahkan verifikasi email','code'=>200,'data'=>['waktu'=>$subminute]];
                        }else{
                            $stmt[2]->close();
                            return ['status'=>'error','message'=>'fail create verify email','code'=>500];
                        }
                    }
                }else{
                    $stmt[0]->close();
                    if($_SERVER['REQUEST_URI']->path() === 'verify/create/email' && $_SERVER['REQUEST_METHOD'] === 'get'){
                        return ['status'=>'error','message'=>'email invalid'];
                    }else{
                        return ['status'=>'error','message'=>'email invalid','code'=>400];
                    }
                }    
            }
        }catch(Exception $e){
            return $e;
        }
    }
    //send email forgot password
    public function createForgotPassword($data){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email empty'];
            }else{
                //checking if email exist in table user
                $currentDateTime = Carbon::now(self::$timeZone);
                $query = "SELECT nama FROM users WHERE BINARY email LIKE ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $email1 = '%' . $email . '%';
                $stmt[0]->bind_param('s', $email1);
                $result = '';
                $stmt[0]->bind_result($result);
                $stmt[0]->execute();
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    //checking if email exist in table verify
                    $stmt[0]->close();
                    //create timeout
                    $subminute = $currentDateTime->subMinutes(15);
                    $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $description = 'changePass';
                    $stmt[1]->bind_param('ss', $email1, $description);
                    $stmt[1]->execute();
                    //checking if email exist in table verify
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('sss', $email1, $description, $subminute);
                        $stmt[2]->execute();
                        //checking if user have create verify email
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            //if after 15 minute then update code
                            $verificationCode = mt_rand(100000, 999999);
                            $linkPath = bin2hex(random_bytes(50 / 2));
                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                            $host = $_SERVER['HTTP_HOST'];
                            $baseURL = $protocol . '://' . $host;
                            $verificationLink = $baseURL . '/verify/email/' . $linkPath;
                            $query = "UPDATE verify SET link = ?, code = ? updated_at = ? FROM verify WHERE BINARY email LIKE ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ssss',$verificationLink, $verificationCode, $email1, $currentDateTime, $email1);
                            $stmt[3]->execute();
                            //update link
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'changePass'];
                                //resend email
                                $this->send($data);
                                return ['status'=>'success','message'=>'success send reset Password','data'=>['waktu'=>$subminute]];
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'fail create verify email','code'=>500];
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
                        $verificationLink = $baseURL . '/verify/email/' . $linkPath;
                        $query = "INSERT INTO verify (email, code, link, description, created_at, updated_at) VALUES(?,?,?,?,?,?)";
                        $stmt[2] = self::$con->prepare($query);
                        $description = 'changePass';
                        $stmt[2]->bind_param("ssssss", $data['email'], $verificationCode, $linkPath, $description, $currentDateTime, $currentDateTime);
                        $stmt[2]->execute();
                        if ($stmt[2]->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'changePass'];
                            $this->send($data);
                            return ['status'=>'Success','message'=>'Akun Berhasil Dibuat Silahkan verifikasi email','code'=>200,'data'=>['waktu'=>$subminute]];
                        }else{
                            $stmt[2]->close();
                            return ['status'=>'error','message'=>'fail create verify email','code'=>500];
                        }
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        }catch(\Exception $e){
            return $e;
        }
    }
    public function verifyEmail($data, $uri,$method){
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            throw new Exception(json_encode(['status'=>'error','message'=>'Email empty','code'=>400]));
        }else{
            $prefix = "/verify/email/";
            if(($uri === $prefix) && $method === "post"){
                $linkPath = substr($uri, strlen($prefix));
                $query = "SELECT email FROM verify WHERE BINARY link LIKE ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $linkPath);
                $email1 = '';
                $stmt[0]->bind_result($email);
                $stmt[0]->execute();
                //checking if email exist in table verify
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    if($email === $email1){
                        $query = "UPDATE users SET email_verified = ? FROM users WHERE BINARY email LIKE ? LIMIT 1";
                        $stmt[1] = self::$con->prepare($query);
                        $verified = true;
                        $stmt[1]->bind_param('bs',$verified, $email);
                        $stmt[1]->execute();
                        //update link
                        if ($stmt[1]->fetch()) {
                            $stmt[1]->close();
                            return json_encode(['status'=>'success','message'=>'email verify success']);
                        }else{
                            $stmt[1]->close();
                            // return redirect('/login');
                            return (json_encode(['status'=>'error','message'=>'Email invalid','code'=>500]));
                        }
                    }else{
                        return json_encode(['status'=>'error','message'=>'email invalid','code'=>400]);
                    }
                }else{
                    $stmt[0]->close();
                    return json_encode(['status'=>'error','message'=>'link invalid','code'=>400]);
                }
            }else{
                return json_encode(['status'=>'error','message'=>'not found']);
            }
        }
    }
}
?>