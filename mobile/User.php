<?php 
require_once('koneksi.php');
class UserMobile{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/public/img/event';
    }
    //khusus masyarakat
    public function createUser($data, $opt){
        try{
            if (!isset($data['email']) || empty($data['email'])) {
                throw new Exception('Email harus di isi !');
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email invalid !');
            }
            if (!isset($data['password']) || empty($data['password'])) {
                throw new Exception('Password harus di isi !');
            } elseif (strlen($data['password']) < 8) {
                throw new Exception('Password minimal 8 karakter !');
            } elseif (strlen($data['password']) > 25) {
                throw new Exception('Password maksimal 25 karakter !');
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                throw new Exception('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
            }
            if (!isset($data['nama']) || empty($data['nama'])) {
                throw new Exception('Nama lengkap harus di isi !');
            }
            if (!isset($data['phone']) || empty($data['phone'])) {
                throw new Exception('Nomer telepon harus di isi !');
            }
            if (!isset($data['jenisK']) || empty($data['jenisK'])) {
                throw new Exception('Jenis kelamin harus di isi !');
            }
            if(!in_array($data['jenisK'], ['laki-laki','perempuan'])){
                throw new Exception('Jenis kelamin invalid !');
            }
            if (!isset($data['tempatL']) || empty($data['tempatL'])) {
                throw new Exception('Tempat lahir harus di isi !');
            }
            if (!isset($data['tanggalL']) || empty($data['tanggalL'])) {
                throw new Exception('Tanggal lahir harus di isi !');
            }
            if (!isset($data['role']) || empty($data['role'])) {
                throw new Exception('Role harus di isi !');
            }
            if(!in_array($data['role'], ['super admin','admin event','admin pentas', 'admin tempat', 'admin seniman'])){
                throw new Exception('Role invalid !');
            }
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $query = "INSERT INTO users (email,password, nama_lengkap, no_telpon, jenis_kelamin, tempat_lahir, tanggal_lahir, role, verifikasi) VALUES (?, ?, ?, ?, ? , ?, ?, ?, ?)";
            if($opt == 'register'){
                $verifikasi = 0;
            }else{
                $verifikasi = 1;
            }
            $stmt = self::$con->prepare($query);
            $stmt->bind_param("ssssssssi", $data['email'], $hashedPassword, $data['nama'], $data['phone'], $data['jenisK'],$data['tempatL'], $data['tanggalL'], $data['role'],$verifikasi);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $email = self::$mailController->createVerifyEmail($data);
                $stmt->close();
                if($email['status'] == 'error'){
                    throw new Exception(json_encode(['status'=>'error','message'=>$email['message']]));
                }else{
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>$email['message'],'data'=>$email['data']]);
                    exit();
                }
            } else {
                $stmt->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Akun gagal dibuat','code'=>500]));
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
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function isExistUser($email){
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email empty'];
        }else{
            $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
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
            $changePassPage = new ChangePasswordController();
            $notificationPage = new NotificationPageController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'code' =>'nullable'
            // ],[
            //     'email.required'=>'Email harus di isi',
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
            if($path1 == '/verifikasi/password' && $method == 'GET'){
                $email = $param['email'];
                //get link 
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY LINK = ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email,$link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check link & email is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            $now->sub(new DateInterval('PT15M'));
                            $time = $now->format('Y-m-d H:i:s');
                            // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ?  AND updated_at >= ? LIMIT 1";
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
                                    'description' => 'password',
                                    'code' => '',
                                    'link' => $link
                                ];
                                extract($data);
                                include('view/page/forgotPassword.php');
                                exit();
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY link = ?";
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
                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND binary kode_otp = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $now->sub(new DateInterval('PT15M'));
                        $time = $now->format('Y-m-d H:i:s');
                        // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            return ['status'=>'success','message'=>'otp anda benar silahkan ganti password'];
                            // return response()->json(['status'=>'success','data'=>['div'=>'verifikasi','description'=>'password']]);
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
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
            //     'email.required'=>'Email wreajib di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            //     'password.required'=>'Password harus di isi',
            //     'password.min'=>'Password minimal 8 karakter',
            //     'password.max'=>'Password maksimal 25 karakter',
            //     'password.regex'=>'Password baru harus terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'password_confirm.required'=>'Password konfirmasi konfirmasi harus di isi',
            //     'password_confirm.min'=>'Password konfirmasi minimal 8 karakter',
            //     'password_confirm.max'=>'Password konfirmasi maksimal 25 karakter',
            //     'password_confirm.regex'=>'Password konfirmasi terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'description.required'=>'Deskripsi harus di isi',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0];
            //     }
            //     return ['status' => 'error', 'message' => $errors];
            // }
            // var_dump($data);
            $email = $data['email'];
            $password = $data["password"];
            $pass1 = $data["password_confirm"];
            $link = $data['link'];
            $desc = $data['description'];
            if($password !== $pass1){
                return ['status'=>'error','message'=>'Password Harus Sama'];
            }else{
                if(is_null($link) || empty($link)){
                    if($desc == 'createUser'){
                        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                        $query = "INSERT INTO users (email,password, nama_lengkap, verifikasi, role) VALUES (?, ?, ?, ?, ?)";
                        $verifikasi = 1;
                        $stmt = self::$con->prepare($query);
                        // $now = Carbon::now('Asia/Jakarta');
                        $role = 'MASYARAKAT';
                        $stmt->bind_param("sssis", $data['email'], $hashedPassword, $data['nama'],$verifikasi, $role);
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
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY kode_otp = ? LIMIT 1";
                        $stmt[0] = self::$con->prepare($query);
                        $stmt[0]->bind_param('s', $code);
                        $stmt[0]->execute();
                        $name = '';
                        $stmt[0]->bind_result($name);
                        //check email is valid on table verifikasi
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
                                //check email and code is valid on table verifikasi
                                if ($stmt[2]->fetch()) {
                                    $stmt[2]->close();
                                    $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                                    $now->sub(new DateInterval('PT15M'));
                                    $time = $now->format('Y-m-d H:i:s');
                                    // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                                    $stmt[3] = self::$con->prepare($query);
                                    $stmt[3]->bind_param('ss', $email, $time);
                                    $stmt[3]->execute();
                                    $name = '';
                                    $stmt[3]->bind_result($name);
                                    //check time is valid on table verifikasi
                                    if ($stmt[3]->fetch()) {
                                        $stmt[3]->close();
                                        $newPass = password_hash($password, PASSWORD_DEFAULT,['cost'=>10]);
                                        $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                        $stmt[4] = self::$con->prepare($query);
                                        $stmt[4]->bind_param('ss', $newPass, $email);
                                        $stmt[4]->execute();
                                        $affectedRows = $stmt[4]->affected_rows;
                                        //check time is valid on table verifikasi
                                        if ($affectedRows > 0) {
                                            $stmt[4]->close();
                                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
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
                                        $query = "DELETE FROM verifikasi WHERE BINARY kode_otp = ? AND deskripsi = 'password'";
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
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? AND deskripsi = $desc LIMIT 1";
                    $stmt[0] = self::$con->prepare($query);
                    $stmt[0]->bind_param('s', $link);
                    $stmt[0]->execute();
                    $name = '';
                    $stmt[0]->bind_result($name);
                    //check link is valid on table verifikasi
                    if ($stmt[0]->fetch()) {
                        $stmt[0]->close();
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND deskripsi = $desc LIMIT 1";
                        $stmt[1] = self::$con->prepare($query);
                        $stmt[1]->bind_param('s', $email);
                        $stmt[1]->execute();
                        $name = '';
                        $stmt[1]->bind_result($name);
                        //check email is valid on table verifikasi
                        if ($stmt[1]->fetch()) {
                            $stmt[1]->close();
                            $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY link = ? AND deskripsi = $desc LIMIT 1";
                            $stmt[2] = self::$con->prepare($query);
                            $stmt[2]->bind_param('ss', $email, $link);
                            $stmt[2]->execute();
                            $name = '';
                            $stmt[2]->bind_result($name);
                            //check email and link is valid on table verifikasi
                            if ($stmt[2]->fetch()) {
                                $stmt[2]->close();
                                $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                                $now->sub(new DateInterval('PT15M'));
                                $time = $now->format('Y-m-d H:i:s');
                                // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = $desc LIMIT 1";
                                $stmt[3] = self::$con->prepare($query);
                                $stmt[3]->bind_param('ss', $email, $time);
                                $stmt[3]->execute();
                                $name = '';
                                $stmt[3]->bind_result($name);
                                //check time is valid on table verifikasi
                                if ($stmt[3]->fetch()) {
                                    $stmt[3]->close();
                                    $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                    $stmt[4] = self::$con->prepare($query);
                                    $newPass = password_hash($password, PASSWORD_DEFAULT);
                                    $stmt[4]->bind_param('ss', $newPass, $email);
                                    $stmt[4]->execute();
                                    $affectedRows = $stmt[4]->affected_rows;
                                    //check time is valid on table verifikasi
                                    if ($affectedRows > 0) {
                                        $stmt[4]->close();
                                        $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = $desc";
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
                                    $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
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
    public function getVerifyEmail($data, $uri,$method){
        try{
            $validator = Validator::make($data, [
                'email'=>'required|email',
                'link' => 'nullable',
            ],[
                'email.required'=>'Email harus di isi',
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
            $query =  "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
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
                if($path1 == '/verifikasi/email' && $method == 'GET'){
                    $link = ltrim(substr($path, strrpos($path, '/')),'/');
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
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
                                'description' => 'password',
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
            $notificationPage = new NotificationPageController();
            $validator = Validator::make($data, [
                'email'=>'required|email',
                'code' =>'nullable'
            ],[
                'email.required'=>'Email harus di isi',
                'email.email'=>'Email yang anda masukkan invalid',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                    $errors = $errorMessages[0]; 
                }
                throw new Exception(json_encode(['status' => 'error', 'message' => $errors]));
            }
            //get path
            $path = parse_url($uri, PHP_URL_PATH);
            $path = ltrim($path, '/');
            //get relative path 
            $lastSlashPos = strrpos($path, '/');
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if($path1 == '/verifikasi/email' && $method == 'GET'){
                $email = $param['email'];
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                // echo 'link '.$link;
                $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid on table verifikasi
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid on table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY link = ? AND deskripsi = 'email' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check email and link is valid on table verifikasi
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            $now->sub(new DateInterval('PT15M'));
                            $time = $now->format('Y-m-d H:i:s');
                            // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = 'email' LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ss', $email, $time);
                            $stmt[3]->execute();
                            $name = '';
                            $stmt[3]->bind_result($name);
                            //check time is valid on table verifikasi
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $query =  "UPDATE users SET verifikasi = true WHERE BINARY email = ?";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $stmt[4]->execute();
                                $affectedRows = $stmt[4]->affected_rows;
                                //update users
                                if ($affectedRows > 0) {
                                    $stmt[4]->close();
                                    $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
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
                                    return ['status'=>'error','message'=>'error verifikasi email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY link = ?";
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
                $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid on table verifikasi
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY kode_otp = ? AND deskripsi = 'email' LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid on table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $now->sub(new DateInterval('PT15M'));
                        $time = $now->format('Y-m-d H:i:s');
                        // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = 'email' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid on table verifikasi
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $query =  "UPDATE users SET verifikasi = true WHERE BINARY email = ?";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //check time is valid on table verifikasi
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $result = $stmt[4]->execute();
                                if($result){
                                    $stmt[4]->close();
                                    return ['status'=>'success','message'=>'verifikasi email berhasil silahkan login'];
                                }else{
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'error verifikasi email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'error update password','code'=>500];
                            }
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
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
    }
    // public function logout($data,$uri = null){
    //         try{
    //             $jwtController = new JwtController();
    //             $email = $data['email'];
    //             $number = $data['number'];
    //             if(empty($email) || is_null($email)){
    //                 return ['status'=>'error','message'=>'email empty','code'=>400];
    //             }else if(empty($number) || is_null($number)){
    //                 return ['status'=>'error','message'=>'token empty','code'=>400];
    //             }else{
    //                 $deleted = $jwtController->deleteRefreshWebsite($email,$number);
    //                 if($deleted['status'] == 'error'){
    //                     setcookie('token1', '', time() - 3600, '/');
    //                     setcookie('token2', '', time() - 3600, '/');
    //                     setcookie('token3', '', time() - 3600, '/');
    //                     header('Location: /login');
    //                     exit();
    //                 }else{
    //                     setcookie('token1', '', time() - 3600, '/');
    //                     setcookie('token2', '', time() - 3600, '/');
    //                     setcookie('token3', '', time() - 3600, '/');
    //                     header('Location: /login');
    //                     exit();
    //                 }
    //             }
    //         } catch (Exception $e) {
    //             // echo $e->getTraceAsString();
    //             $error = $e->getMessage();
    //             $erorr = json_decode($error, true);
    //             if ($erorr === null) {
    //                 $responseData = array(
    //                     'status' => 'error',
    //                     'message' => $error,
    //                 );
    //             }else{
    //                 if($erorr['message']){
    //                     $responseData = array(
    //                         'status' => 'error',
    //                         'message' => $erorr['message'],
    //                     );
    //                 }else{
    //                     $responseData = array(
    //                         'status' => 'error',
    //                         'message' => $erorr->message,
    //                     );
    //                 }
    //             }
    //             return $responseData;
    //         }
    //     }
    public function logout($data){
        try{
            $jwtController = new JwtController();
            $email = $data['email'];
            $number = $data['number'];
            if(empty($email) || is_null($email)){
                throw new Exception('Email harus di isi !');
            }else if(empty($number) || is_null($number)){
                throw new Exception('token harus di isi !');
            }else{
                $deleted = $jwtController->deleteRefreshWebsite($email,$number);
                if($deleted['status'] == 'error'){
                    // setcookie('token1', '', time() - 3600, '/');
                    // setcookie('token2', '', time() - 3600, '/');
                    // setcookie('token3', '', time() - 3600, '/');
                    // header('Location: /login');
                    exit();
                }else{
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'Anda berhasil keluar silahkan login kembali']);
                    exit();
                }
            }
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
        }
    }
    public static function handle(){
        $contentType = $_SERVER["CONTENT_TYPE"];
        if ($contentType === "application/json") {
            $rawData = file_get_contents("php://input");
            $requestData = json_decode($rawData, true);
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } elseif ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
$user = new User;
if(isset($_POST['tambahAdmin'])){
    $user->tambahAdmin($_POST);
}
if(isset($_POST['editAdmin'])){
    $user->editAdmin($_POST);
}
if(isset($_POST['hapusAdmin'])){
    $user->hapusAdmin($_POST);
}
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo 'data ilang';
}
?>