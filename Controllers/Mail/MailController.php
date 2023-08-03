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
    public function __construct(){
        try {
            self::$database = Database::getInstance();
            self::$con = self::$database->getConnection();
            $this->mail = new PHPMailer(true);
            $this->mail->Host = $_SERVER['MAIL_HOST'];
            $this->mail->isSMTP();
            // $this->mail->Host = 'smtp.gmail.com';
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
            }else if($data['description'] == 'forgotPassword'){
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
    // public function getVerifyEmail(Request $request){
    //     $email = $request->input('email');
    //     if(empty($email) || is_null($email)){
    //         return response()->json(['status'=>'error','message'=>'email empty'],404);
    //     }else{
    //         if(User::select("email")->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->exists()){
    //             if(Verify::select("email")->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->exists()){
    //                 $dataDb = Verify::select()->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->get();
    //                 $data = json_decode(json_encode($dataDb));
    //                 $code = $data['code'];
    //                 $linkPath = $data['link'];
    //                 $verificationLink = URL::to('/verify/' . $linkPath);
    //                 return response()->json(['status'=>'error','data'=>['code'=>$code,'link'=>$verificationLink]]);
    //             }
    //         }else{
    //             return response()->json(['status'=>'error','message'=>'email invalid'],404);
    //         }
    //     }
    // }
    public function createVerifyEmail($data){
        $stmt = array();
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email wajib di isi'];
            }else{
                //checking if email exist in table user
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
                    $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $description = 'verifyEmail';
                    $stmt[1]->bind_param('ss', $email1, $description);
                    $stmt[1]->execute();
                    //checking if email exist in table verify
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $currentDateTime = Carbon::now();
                        $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE ? AND description = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $subminute = $currentDateTime->subMinutes(15);
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
                            $now = Carbon::now();
                            $stmt[3]->bind_param('ssss',$verificationLink, $verificationCode, $email1, $now, $email1);
                            $stmt[3]->execute();
                            //update link
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'verifyEmail'];
                                //resend email
                                $this->send($data);
                                return ['status'=>'success','message'=>'success send verify email','data'=>['waktu'=>Carbon::now()->addMinutes(15)]];
                            }else{
                                return ['status'=>'error','message'=>'fail create verify email'];
                            }
                        }else{
                            return ['status'=>'error','message'=>'we have send verify email'];
                        }
                    }else{
                        $verificationCode = mt_rand(100000, 999999);
                        $linkPath = bin2hex(random_bytes(50 / 2));
                        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'];
                        $baseURL = $protocol . '://' . $host;
                        $verificationLink = $baseURL . '/verify/email/' . $linkPath;
                        $query = "INSERT INTO verify (email, code, link, description, created_at, updated_at) VALUES(?,?,?,?,?,?)";
                        $stmt = self::$con->prepare($query);
                        $description = 'verifyEmail';
                        $currentDateTime = new \DateTime();
                        $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');
                        $stmt->bind_param("ssssss", $data['email'], $verificationCode, $linkPath, $description, $formattedDateTime, $formattedDateTime);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink),'description'=>'verifyEmail'];
                            $this->send($data);
                            return ['status'=>'Success','message'=>'Akun Berhasil Dibuat Silahkan verifikasi email','code'=>200,'data'=>['waktu'=>Carbon::now()->addMinutes(15)]];
                        }else{
                            return ['status'=>'error','message'=>'fail create verify email','code'=>400];
                        }
                    }
                }else{
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
    // public function createForgotPassword(Request $request, Verify $verify){
    //     $email = $request->input('email');
    //     if(empty($email) || is_null($email)){
    //         return response()->json(['status'=>'error','message'=>'email empty'],400);
    //     }else{
    //         //checking if email exist in table user
    //         if(User::select("email")->whereRaw("BINARY email LIKE '%".$email."%'")->limit(1)->exists()){
    //             //checking if email exist in table verify
    //             if(Verify::select("email")->whereRaw("BINARY email LIKE '%".$email."%'AND description = 'changePass'")->limit(1)->exists()){
    //                 //checking time
    //                 $currentDateTime = Carbon::now();
    //                 if (DB::table('verify')->whereRaw("BINARY email LIKE '%".$email."%' AND description = 'changePass'")->where('updated_at', '<=', $currentDateTime->subMinutes(15))->exists()) {
    //                     //if after 15 minute then update code
    //                     $verificationCode = mt_rand(100000, 999999);
    //                     $linkPath = Str::random(50);
    //                     $verificationLink = URL::to('/verify/password/' . $linkPath);
    //                     if(is_null(DB::table('verify')->whereRaw("BINARY email LIKE '%".$email."%' AND description = 'changePass'")->update(['code'=>$verificationCode,'link'=>$linkPath, 'updated_at' => Carbon::now()]))){
    //                         return response()->json(['status'=>'error','message'=>'fail create forgot password'],500);
    //                     }else{
    //                         $inName = User::select('nama')->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->get();
    //                         $Iname = json_decode(json_encode($inName));
    //                         $data = ['name'=>$Iname,'email'=>$email,'code'=>$verificationCode,'link'=>$verificationLink];
    //                         Mail::to($email)->send(new ForgotPassword($data));
    //                         return response()->json(['status'=>'success','message'=>'email benar kami kirim kode ke anda silahkan cek email']);
    //                     }
    //                 }else{
    //                     return response()->json(['status'=>'error','message'=>'Kami sudah mengirimkan otp lupa password silahkan cek mail anda'],400);
    //                 }
    //             //if user haven't create email forgot password
    //             }else{
    //                 $verificationCode = mt_rand(100000, 999999);
    //                 $linkPath = Str::random(50);
    //                 $verificationLink = URL::to('/verify/password/' . $linkPath);
    //                 $verify->email = $email;
    //                 $verify->code = $verificationCode;
    //                 $verify->link = $linkPath;
    //                 $verify->description = 'changePass';
    //                 if($verify->save()){
    //                     $inName = User::select('nama')->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->get();
    //                     $Iname = json_decode(json_encode($inName));
    //                     // return response()->json('link '.$verificationLink);
    //                     $data = ['name'=>$Iname,'email'=>$email,'code'=>$verificationCode,'link'=>$verificationLink];
    //                     Mail::to($email)->send(new ForgotPassword($data));
    //                     return response()->json(['status'=>'success','message'=>'kami akan kirim kode ke anda silahkan cek email','data'=>['waktu'=>Carbon::now()->addMinutes(15)]]);
    //                 }else{
    //                     return response()->json(['status'=>'error','message'=>'fail create forgot password'],500);
    //                 }
    //             }
    //         }else{
    //             return response()->json(['status'=>'error','message'=>'email invalid'],400);
    //         }
    //     }
    // }
    // public function verifyEmail(Request $request){
    //     $email = $request->input('email');
    //     if(empty($email) || is_null($email)){
    //         return response()->json(['status'=>'error','message'=>'email empty'],404);
    //     }else{
    //         $prefix = "/verify/email/";
    //         if(($request->path() === $prefix) && $request->isMethod("post")){
    //             $linkPath = substr($request->path(), strlen($prefix));
    //             if(Verify::select("link")->where('link','=',$linkPath)->limit(1)->exists()){
    //                 if(Verify::select("email")->where('email','=',$email)->limit(1)->exists()){
    //                     if(is_null(DB::table('users')->where('email','=',$email)->update(['email_verified'=>true]))){
    //                         return response()->json(['status'=>'error','message'=>'email invalid'],404);
    //                     }else{
    //                         // return redirect('/login');
    //                         return response()->json(['status'=>'success','message'=>'email verify success']);
    //                     }
    //                 }else{
    //                     return response()->json(['status'=>'error','message'=>'email invalid'],404);
    //                 }
    //             }
    //         }
    //     }
    // }
    // public function send(){
    //     Mail::to('amirzanfikri5@gmail.com')->send(new ForgotPassword(['data'=>'data']));
    //     return view('page.home');
    // }
}
?>