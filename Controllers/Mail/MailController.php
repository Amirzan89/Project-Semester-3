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
            $this->mail->setFrom('terserah@gmail.com', 'gabutt');
            // $this->mail->addAddress('amirzanfikri5@gmail.com', 'gabut tersrah');
            $this->mail->addAddress($data['email'], $data['nama']);
            // Email content
            $this->mail->isHTML(true);
            // $this->mail->Subject = 'Test Email from PHPMailer';
            $this->mail->Subject = $data['description'];
            if($data['description'] == 'verifyEmail'){
                $this->mail->Body = include 'view/mail/verifyEmail.php';
            }else if($data['description'] == 'forgotPassword'){
                $this->mail->Body = include 'view/mail/forgotPassword.php';
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
                return ['status'=>'error','message'=>'email empty'];
                // throw new Exception(json_encode(['status'=>'error','message'=>'Email wajib di isi','code'=>400]));
            }else{
                //checking if email exist in table user
                $query = "SELECT nama FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $email = '%' . $email . '%';
                $stmt[0]->bind_param('s', $email);
                $result = '';
                $stmt[0]->bind_result($result);
                //check email exist in table user
                if ($stmt[0]->fetch()) {
                    $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE CONCAT('%', ?, '%') AND description = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $email = '%' . $email . '%';
                    $description = 'verifyEmail';
                    $stmt[1]->bind_param('ss', $email, $description);
                    //checking if email exist in table verify
                    if ($stmt[1]->fetch()) {
                        $currentDateTime = Carbon::now();
                        $stmt[0]->close();
                        $query = "SELECT updated_at FROM verify WHERE BINARY email LIKE CONCAT('%', ?, '%') AND description = ? AND updated_at >= ".$currentDateTime->subMinutes(15). " LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $email = '%' . $email . '%';
                        $stmt[2]->bind_param('ss', $email, $description);
                        //checking if user have create verify email
                        if ($stmt[2]->fetch()) {
                            $stmt[1]->close();
                        // if (DB::table('verify')->whereRaw("BINARY email LIKE '%".$email."%' AND description = 'verifyEmail'")->where('updated_at', '>=', $currentDateTime->subMinutes(15))->exists()) {
                            //if after 15 minute then update code
                            $verificationCode = mt_rand(100000, 999999);
                            $linkPath = bin2hex(random_bytes(50 / 2));
                            $baseURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
                            $verificationLink = $baseURL.'/verify/email/'.$linkPath;
                            $query = "UPDATE link = ?, code = ? updated_at = NOW() FROM verify WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $email = '%' . $email . '%';
                            $stmt[3]->bind_param('sss',$verificationLink, $verificationCode, $email);
                            //update link
                            if ($stmt[3]->fetch()) {
                                $stmt[2]->close();
                                $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink)];
                                // return response()->json('link '.$verificationLink);
                                //resend email
                                $this->send($data);
                                // Mail::to($email)->send(new VerifyEmail($data));
                                $stmt[3]->close();
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
                        $baseURL = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
                        $verificationLink = $baseURL.'/verify/email/'.$linkPath;
                        // $verify->email = $email;
                        // $verify->code = $verificationCode;
                        // $verify->link = $linkPath;
                        // $verify->description = 'verifyEmail';
                        $query = "INSERT INTO users VALUES(?,?,?,?)";
                        $verified = false;
                        $stmt = self::$con->prepare($query);
                        $description = 'verifyEmail';
                        $currentDateTime = new \DateTime();
                        $formattedDateTime = $currentDateTime->format('Y-m-d H:i:s');
                        $stmt->bind_param("ssssss", $data['email'], $verificationCode, $linkPath, $description, $formattedDateTime, $formattedDateTime);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            $data = ['name'=>$result,'email'=>$email,'code'=>$verificationCode,'link'=>urldecode($verificationLink)];
                            $this->send($data);
                            // Mail::to($email)->send(new VerifyEmail($data));
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
            // $error = json_decode($e->getMessage());
            // $responseData = array(
            //     'status' => 'error',
            //     'message' => $error['message'],
            // );
            // $jsonResponse = json_encode($responseData);
            // header('Content-Type: application/json');
            // http_response_code(!empty($error['code']) ? $error['code'] : 400);
            // echo $jsonResponse;
            // exit();
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