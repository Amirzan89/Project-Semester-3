<?php 
// namespace Controllers;
require_once $rootDir . '/Controllers/Auth/JwtController.php';
require_once $rootDir . '/Controllers/Mail/MailController.php';
require_once $rootDir . '/Controllers/Website/ChangePasswordController.php';
require_once $rootDir . '/Controllers/Website/NotificationPageController.php';
use Database\Database;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

// use Exception;
class UserController{
    private static $database;
    private static $con;
    private static $mailController;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
        self::$mailController = new MailController();
    }
    public function createUser($data, $opt){
        try{
            if (!isset($data['email']) || empty($data['email'])) {
                return ['status'=>'error','message'=>'Email wajib di isirwr','code'=>400];
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['status'=>'error','message'=>'Email invalid','code'=>400];
            }
            if (!isset($data['password']) || empty($data['password'])) {
                return ['status'=>'error','message'=>'Password wajib di isi00','code'=>400];
            } elseif (strlen($data['password']) < 8) {
                return ['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400];
            } elseif (strlen($data['password']) > 25) {
                return ['status'=>'error','message'=>'Password maksimal 8 karakter','code'=>400];
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                return ['status' => 'error', 'message' => 'Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400];
            }
            // Validate 'nama' field
            if (!isset($data['nama']) || empty($data['nama'])) {
                return ['status' => 'error', 'message' => 'Nama Wajib di isi', 'code' => 400];
            }
            if($opt == 'register'){
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $query = "INSERT INTO users (email,password, nama, email_verified, level,created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $now = Carbon::now('Asia/Jakarta');
                $verified = false;
                $stmt = self::$con->prepare($query);
                $level = 'ADMIN';
                $stmt->bind_param("sssbsss", $data['email'], $hashedPassword, $data['nama'],$verified, $level, $now, $now);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $email = self::$mailController->createVerifyEmail($data);
                    $stmt->close();
                    if($email['status'] == 'error'){
                        return ['status'=>'error','message'=>$email['message']];
                    }else{
                        return ['status'=>'success','message'=>$email['message'],'data'=>$email['data']];
                    }
                } else {
                    $stmt->close();
                    return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
                }
            }else if('google'){
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $query = "INSERT INTO users (email,password, nama, email_verified, level,created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
                $verified = true;
                $stmt = self::$con->prepare($query);
                $now = Carbon::now('Asia/Jakarta');
                $level = 'ADMIN';
                $stmt->bind_param("sssbsss", $data['email'], $hashedPassword, $data['nama'],$verified, $level, $now, $now);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $email = self::$mailController->createVerifyEmail($data);
                    $stmt->close();
                    if($email['status'] == 'error'){
                        return ['status'=>'error','message'=>$email['message']];
                    }else{
                        return ['status'=>'success','message'=>$email['message'],'data'=>$email['data']];
                    }
                } else {
                    $stmt->close();
                    return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
                }
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $erorr->message,
                );
            }
            return $responseData;
        }
    }
//      public function getUser($email, $data, $where){
    //         try{
//             $database = Database::getInstance();
//             $con = $database->getConnection();
//             $check = "SELECT email FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
//             $Stmt[0] = $con->prepare($check);
//             $email = '%' . $email . '%';
//             $Stmt[0]->bind_param('s', $email);
//             $Stmt[0]->execute();
//             // Bind the result
//             $Stmt[0]->bind_result($email);
//             if ($Stmt[0]->fetch()) {
    //                 $query = "SELECT $data FROM users WHERE BINARY email LIKE CONCAT('%', ?, '%') LIMIT 1";
    //                 $Stmt[1] = $con->prepare($query);
    //                 $email = '%' . $email . '%';
    //                 $Stmt[1]->bind_param('s', $email);
//                 $Stmt[1]->execute();
//                 // Bind the result
//                 $result = [];
//                 $Stmt[1]->bind_result($result);
//                 while ($Stmt[1]->fetch()) {
//                 }
//                 return $email;
//             } else {
    
    //                 // No row found with the email
    //                 echo "Email not found.";
//             }
//             foreach($Stmt as $stmt){
    //                 $stmt->close();
    //             }
    //         }catch(Exception $e){
        //             return $e->getMessage();
        //         }
        //     }
    public function getUser($email, $data){
        // $database = Database::getInstance();
        // $con = $database->getConnection();
        $columns = implode(', ', $data); // Convert the array of columns to a comma-separated string
        $query = "SELECT $columns FROM users WHERE BINARY email = ? LIMIT 1";
        $stmt = self::$con->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $bindResultArray = [];
        foreach ($data as $column) {
            $bindResultArray[] = &$$column;
        }
        call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
        $result = [];
        if ($stmt->fetch()) {
            // Fetch the data and store it in the $result array
            foreach ($data as $column) {
                $result[$column] = $$column;
            }
        }
        $stmt->close();
        return $result;
    }
    public function isExistUser($email){
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email empty'];
        }else{
            $query = "SELECT nama FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($email);
            if ($stmt->fetch()) {
                return ['status'=>'success','data'=>true];
            }else{
                return ['status'=>'success','data'=>false];
            }
        }
    }
    public function getChangePass($data, $uri, $method, $param){
        try{
            $data = $data['request'];
            $changePassPage = new ChangePasswordController();
            $notificationPage = new NotificationPageController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'code' =>'nullable'
            // ],[
            //     'email.required'=>'Email wajib di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0];
            //     }
            //     throw new Exception(json_encode(['status' => 'error', 'message' => $errors]));
            // }
            $code = isset($data['code']) ? $data['code'] : null;
            //get path
            $path = parse_url($uri, PHP_URL_PATH);
            $path = ltrim($path, '/');
            //get relative path 
            $lastSlashPos = strrpos($path, '/');
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if($path1 == '/verify/password' && $method == 'GET'){
                $email = $param['email'];
                //get link 
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                $query = "SELECT id FROM verify WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id FROM verify WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT id FROM verify WHERE BINARY email = ? AND BINARY LINK = ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email,$link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check link & email is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query = "SELECT id FROM verify WHERE BINARY email = ?  AND updated_at >= ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ss', $email,$time);
                            $stmt[3]->execute();
                            $name = '';
                            $stmt[3]->bind_result($name);
                            //check email is valid
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $data = [
                                    'email' => $email,
                                    'div' => 'verifyDiv',
                                    'title' => 'Reset Password',
                                    'description' => 'changePass',
                                    'code' => '',
                                    'link' => $link
                                ];
                                extract($data);
                                include('view/page/forgotPassword.php');
                                exit();
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verify WHERE BINARY link = ?";
                                $stmt = self::$con->prepare($query);
                                $stmt->bind_param('s', $link);
                                $result = $stmt->execute();
                                return $notificationPage->showFailResetPass('Link Expired');
                            }
                        }else{
                            $stmt[2]->close();
                            return $notificationPage->showFailResetPass('Link invalid');
                        }
                    }else{
                        $stmt[1]->close();
                        return $notificationPage->showFailResetPass('Email invalid');
                    }
                }else{
                    $stmt[0]->close();
                    return $notificationPage->showFailResetPass('Link invalid');
                }
            }else{
                $email = $data['email'];
                $query = "SELECT id FROM verify WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id FROM verify WHERE BINARY email = ? AND binary code = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query = "SELECT id FROM verify WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            return ['status'=>'success','message'=>'otp anda benar silahkan ganti password'];
                            // return response()->json(['status'=>'success','data'=>['div'=>'verify','description'=>'changePass']]);
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'changePass'";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $result = $stmt[3]->execute();
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'code otp expired'];
                        }
                    }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'code otp invalid'];
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
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
    public function changePassEmail($data, $uri){
        try{
            $jwtController = new JwtController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'nama'=>'nullable',
            //     'password' => [
            //         'required',
            //         'string',
            //         'min:8',
            //         'max:25',
            //         'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            //     ],
            //     'password_confirm' => [
            //         'required',
            //         'string',
            //         'min:8',
            //         'max:25',
            //         'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            //     ],
            //     'code' => 'nullable',
            //     'link' => 'nullable',
            //     'description'=>'required'
            // ],[
            //     'email.required'=>'Email wajib di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            //     'password.required'=>'Password wajib di isi',
            //     'password.min'=>'Password minimal 8 karakter',
            //     'password.max'=>'Password maksimal 25 karakter',
            //     'password.regex'=>'Password baru wajib terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'password_confirm.required'=>'Password konfirmasi konfirmasi harus di isi',
            //     'password_confirm.min'=>'Password konfirmasi minimal 8 karakter',
            //     'password_confirm.max'=>'Password konfirmasi maksimal 25 karakter',
            //     'password_confirm.regex'=>'Password konfirmasi terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'description.required'=>'Deskripsi wajib di isi',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0];
            //     }
            //     return ['status' => 'error', 'message' => $errors];
            // }
            // var_dump($data);
            $data = $data['request'];
            $email = $data['email'];
            $pass = $data["password"];
            $pass1 = $data["password_confirm"];
            $link = $data['link'];
            $desc = $data['description'];
            if($pass !== $pass1){
                return ['status'=>'error','message'=>'Password Harus Sama'];
            }else{
                if(is_null($link) || empty($link)){
                    if($desc == 'createUser'){
                        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                        $query = "INSERT INTO users (email,password, nama, email_verified, level,created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $verified = true;
                        $stmt = self::$con->prepare($query);
                        $now = Carbon::now('Asia/Jakarta');
                        $level = 'ADMIN';
                        $stmt->bind_param("sssbsss", $data['email'], $hashedPassword, $data['nama'],$verified, $level, $now, $now);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            $stmt->close();
                            $data = $jwtController->createJWTWebsite(['email'=>$email]);
                            if(is_null($data)){
                                return ['status'=>'error','message'=>'create token error','code'=>500];
                            }else{
                                if($data['status'] == 'error'){
                                    return ['status'=>'error','message'=>$data['message']];
                                }else{
                                    $data1 = ['email'=>$email,'number'=>$data['number']];
                                    $encoded = base64_encode(json_encode($data1));
                                    setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                    setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                                    setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                    return ['status'=>'success','message'=>'Login sukses silahkan masuk dashboard'];
                                }
                            }
                        }else{
                            $stmt->close();
                            return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
                        }
                    }else{
                        $code = $data['code'];
                        $query = "SELECT id FROM verify WHERE BINARY code = ? LIMIT 1";
                        $stmt[0] = self::$con->prepare($query);
                        $stmt[0]->bind_param('s', $code);
                        $stmt[0]->execute();
                        $name = '';
                        $stmt[0]->bind_result($name);
                        //check email is valid on table verify
                        if ($stmt[0]->fetch()) {
                            $stmt[0]->close();
                            $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                            $stmt[1] = self::$con->prepare($query);
                            $stmt[1]->bind_param('s', $email);
                            $stmt[1]->execute();
                            $name = '';
                            $stmt[1]->bind_result($name);
                            //check email is valid on table users
                            if ($stmt[1]->fetch()) {
                                $stmt[1]->close();
                                $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                                $stmt[2] = self::$con->prepare($query);
                                $stmt[2]->bind_param('s', $email);
                                $stmt[2]->execute();
                                $name = '';
                                $stmt[2]->bind_result($name);
                                //check email and code is valid on table verify
                                if ($stmt[2]->fetch()) {
                                    $stmt[2]->close();
                                    $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                    $query = "SELECT id FROM verify WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                                    $stmt[3] = self::$con->prepare($query);
                                    $stmt[3]->bind_param('ss', $email, $time);
                                    $stmt[3]->execute();
                                    $name = '';
                                    $stmt[3]->bind_result($name);
                                    //check time is valid on table verify
                                    if ($stmt[3]->fetch()) {
                                        $stmt[3]->close();
                                        $newPass = password_hash($pass, PASSWORD_DEFAULT,['cost'=>10]);
                                        $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                        $stmt[4] = self::$con->prepare($query);
                                        $stmt[4]->bind_param('ss', $newPass, $email);
                                        $stmt[4]->execute();
                                        $affectedRows = $stmt[4]->affected_rows;
                                        //check time is valid on table verify
                                        if ($affectedRows > 0) {
                                            $stmt[4]->close();
                                            $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'changePass'";
                                            $stmt[5] = self::$con->prepare($query);
                                            $stmt[5]->bind_param('s', $email);
                                            $result = $stmt[5]->execute();
                                            if($result){
                                                $stmt[5]->close();
                                                return ['status'=>'success','message'=>'ganti password berhasil silahkan login'];
                                            }else{
                                                $stmt[5]->close();
                                                return ['status'=>'error','message'=>'error update password','code'=>500];
                                            }
                                        }else{
                                            $stmt[4]->close();
                                            return ['status'=>'error','message'=>'error update password','code'=>500];
                                        }
                                    }else{
                                        $stmt[3]->close();
                                        $query = "DELETE FROM verify WHERE BINARY code = ? AND description = 'changePass'";
                                        $stmt[4] = self::$con->prepare($query);
                                        $stmt[4]->bind_param('s', $code);
                                        $result = $stmt[4]->execute();
                                        $stmt[4]->close();
                                        return ['status'=>'error','message'=>'token expired'];
                                    }
                                }else{
                                    $stmt[2]->close();
                                    return ['status'=>'error','message'=>'Invalid Email'];
                                }
                            }else{
                                $stmt[1]->close();
                                return ['status'=>'error','message'=>'Invalid Email'];
                            }
                        }else{
                            $stmt[0]->close();
                            return ['status'=>'error','message'=>'token invalid'];
                        }
                    }
                //
                }else{
                    $query = "SELECT id FROM verify WHERE BINARY link = ? AND description = $desc LIMIT 1";
                    $stmt[0] = self::$con->prepare($query);
                    $stmt[0]->bind_param('s', $link);
                    $stmt[0]->execute();
                    $name = '';
                    $stmt[0]->bind_result($name);
                    //check link is valid on table verify
                    if ($stmt[0]->fetch()) {
                        $stmt[0]->close();
                        $query = "SELECT id FROM verify WHERE BINARY email = ? AND description = $desc LIMIT 1";
                        $stmt[1] = self::$con->prepare($query);
                        $stmt[1]->bind_param('s', $email);
                        $stmt[1]->execute();
                        $name = '';
                        $stmt[1]->bind_result($name);
                        //check email is valid on table verify
                        if ($stmt[1]->fetch()) {
                            $stmt[1]->close();
                            $query = "SELECT id FROM verify WHERE BINARY email = ? AND BINARY link = ? AND description = $desc LIMIT 1";
                            $stmt[2] = self::$con->prepare($query);
                            $stmt[2]->bind_param('ss', $email, $link);
                            $stmt[2]->execute();
                            $name = '';
                            $stmt[2]->bind_result($name);
                            //check email and link is valid on table verify
                            if ($stmt[2]->fetch()) {
                                $stmt[2]->close();
                                $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                $query = "SELECT id FROM verify WHERE BINARY email = ? AND updated_at >= ? AND description = $desc LIMIT 1";
                                $stmt[3] = self::$con->prepare($query);
                                $stmt[3]->bind_param('ss', $email, $time);
                                $stmt[3]->execute();
                                $name = '';
                                $stmt[3]->bind_result($name);
                                //check time is valid on table verify
                                if ($stmt[3]->fetch()) {
                                    $stmt[3]->close();
                                    $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                    $stmt[4] = self::$con->prepare($query);
                                    $newPass = password_hash($pass, PASSWORD_DEFAULT);
                                    $stmt[4]->bind_param('ss', $newPass, $email);
                                    $stmt[4]->execute();
                                    $affectedRows = $stmt[4]->affected_rows;
                                    //check time is valid on table verify
                                    if ($affectedRows > 0) {
                                        $stmt[4]->close();
                                        $query = "DELETE FROM verify WHERE BINARY email = ? AND description = $desc";
                                        $stmt[5] = self::$con->prepare($query);
                                        $stmt[5]->bind_param('s', $email);
                                        $result = $stmt[5]->execute();
                                        if($result){
                                            $stmt[5]->close();
                                            return ['status'=>'success','message'=>'ganti password berhasil silahkan login'];
                                        }else{
                                            $stmt[5]->close();
                                            return ['status'=>'error','message'=>'error update password','code'=>500];
                                        }
                                    }else{
                                        $stmt[4]->close();
                                        return ['status'=>'error','message'=>'error update password','code'=>500];
                                    }
                                }else{
                                    $stmt[3]->close();
                                    $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'changePass'";
                                    $stmt[4] = self::$con->prepare($query);
                                    $stmt[4]->bind_param('s', $email);
                                    $result = $stmt[4]->execute();
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'link expired'];
                                }
                            }else{
                                $stmt[2]->close();
                                return ['status'=>'error','message'=>'Email invalid'];
                            }
                        }else{
                            $stmt[1]->close();
                            return ['status'=>'error','message'=>'Invalid Email1'];
                        }
                    }else{
                        $stmt[0]->close();
                        return ['status'=>'error','message'=>'link expired'];
                    }
                }
            }
        } catch (Exception $e) {
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
    public function getVerifyEmail($data, $uri,$method){
        try{
            $validator = Validator::make($data, [
                'email'=>'required|email',
                'link' => 'nullable',
            ],[
                'email.required'=>'Email wajib di isi',
                'email.email'=>'Email yang anda masukkan invalid',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                    $errors = $errorMessages[0];
                }
                return ['status' => 'error', 'message' => $errors];
            }
            $email = $data['email'];
            $query =  "SELECT nama FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            $stmt[0]->execute();
            $name = '';
            $stmt[0]->bind_result($name);
            //check email is valid on table users
            if ($stmt[0]->fetch()) {
                $stmt[0]->close();
                //get path
                $path = parse_url($uri, PHP_URL_PATH);
                $path = ltrim($path, '/');
                //get relative path 
                $lastSlashPos = strrpos($path, '/');
                $path1 = substr($uri, 1, $lastSlashPos);
                // $email = $param['email'];
                if($path1 == '/verify/email' && $method == 'GET'){
                    $link = ltrim(substr($path, strrpos($path, '/')),'/');
                    $query =  "SELECT id FROM verify WHERE BINARY link = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $link);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid on table users
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                            $data = [
                                'email' => $email,
                                'div' => 'verifyDiv',
                                'title' => 'Reset Password',
                                'description' => 'changePass',
                                'code' => '',
                                'link' => $link
                            ];
                            extract($data);
                            include('view/page/verifyEmail.php');
                            exit();
                        }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'invalid token'];
                    }
                }
            }else{
                $stmt[0]->close();
                return ['status'=>'error','message'=>'Email invalid'];
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
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
    public function verifyEmail($data,$uri, $method, $param){
        try{
            if(isset($data['request'])){
                $data = $data['request'];
            }
            $notificationPage = new NotificationPageController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'code' =>'nullable'
            // ],[
            //     'email.required'=>'Email wajib di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0]; 
            //     }
            //     throw new Exception(json_encode(['status' => 'error', 'message' => $errors]));
            // }
            //get path
            $path = parse_url($uri, PHP_URL_PATH);
            $path = ltrim($path, '/');
            //get relative path 
            $lastSlashPos = strrpos($path, '/');
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if($path1 == '/verify/email' && $method == 'GET'){
                $email = $param['email'];
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                // echo 'link '.$link;
                $query =  "SELECT id FROM verify WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid on table verify
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id FROM verify WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid on table verify
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query =  "SELECT id FROM verify WHERE BINARY email = ? AND BINARY link = ? AND description = 'verifyEmail' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check email and link is valid on table verify
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query =  "SELECT id FROM verify WHERE BINARY email = ? AND updated_at >= ? AND description = 'verifyEmail' LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ss', $email, $time);
                            $stmt[3]->execute();
                            $name = '';
                            $stmt[3]->bind_result($name);
                            //check time is valid on table verify
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $query =  "UPDATE users SET email_verified = true WHERE BINARY email = ?";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $stmt[4]->execute();
                                $affectedRows = $stmt[4]->affected_rows;
                                //update users
                                if ($affectedRows > 0) {
                                    $stmt[4]->close();
                                    $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'verifyEmail'";
                                    $stmt[5] = self::$con->prepare($query);
                                    $stmt[5]->bind_param('s', $email);
                                    $result = $stmt[5]->execute();
                                    if($result){
                                        $stmt[5]->close();
                                        return $notificationPage->showSuccessVerifyEmail('Verifikasi email berhasil silahkan login', ['email'=>$email]);
                                    }else{
                                        $stmt[5]->close();
                                        return $notificationPage->showFailVerifyEmail('Error verifikasi Email',500);
                                    }
                                }else{
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'error verify email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verify WHERE BINARY link = ?";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $link);
                                $result = $stmt[4]->execute();
                                $stmt[4]->close();
                                return $notificationPage->showFailVerifyEmail('Link Expired');
                            }
                        }else{
                            $stmt[2]->close();
                            return $notificationPage->showFailVerifyEmail('Link invalid');
                        }
                    }else{
                        $stmt[1]->close();
                        return $notificationPage->showFailVerifyEmail('email invalid');
                    }
                }else{
                    $stmt[0]->close();
                    return $notificationPage->showFailVerifyEmail('Link invalid');
                }
            }else{
                $email = $data['email'];
                $code = $data['code'];
                $query =  "SELECT id FROM verify WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid on table verify
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id FROM verify WHERE BINARY email = ? AND BINARY code = ? AND description = 'verifyEmail' LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid on table verify
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query =  "SELECT id FROM verify WHERE BINARY email = ? AND updated_at >= ? AND description = 'verifyEmail' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid on table verify
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $query =  "UPDATE users SET email_verified = true WHERE BINARY email = ?";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //check time is valid on table verify
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'verifyEmail'";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $result = $stmt[4]->execute();
                                if($result){
                                    $stmt[4]->close();
                                    return ['status'=>'success','message'=>'verifikasi email berhasil silahkan login'];
                                }else{
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'error verify email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'error update password','code'=>500];
                            }
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verify WHERE BINARY email = ? AND description = 'verifyEmail'";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $result = $stmt[3]->execute();
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'token expired'];
                        }
                    }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'token invalid'];
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
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
    public function updateUser(){
        //
    }
    public function logout($data,$uri = null){
        try{
            $jwtController = new JwtController();
            $data = $data['request'];
            $email = $data['email'];
            $number = $data['number'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty','code'=>400];
            }else if(empty($number) || is_null($number)){
                return ['status'=>'error','message'=>'token empty','code'=>400];
            }else{
                $deleted = $jwtController->deleteRefreshWebsite($email,$number);
                if($deleted['status'] == 'error'){
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login');
                    exit();
                }else{
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login');
                    exit();
                }
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
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
}
?>