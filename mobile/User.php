<?php 
require_once(__DIR__ . '/../web/koneksi.php');
class UserMobile{
    private static $sizeImg = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../private/profile';
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
                $stmt->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Registrasi Gagal','kode'=>2]));
            }
            $stmt->close();
            header('Content-Type: application/json');
            echo json_encode(['status'=>'success','pesan'=>'Registrasi Berhasil','kode'=>1]);
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function isExistUser($data){
        try{
            if(!isset($data['email']) || empty($data['email']) || is_null($data['email'])){
                throw new Exception('Email harus di isi !');
            }else{
                $query = "SELECT role FROM users WHERE BINARY email = ?";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param('s', $data['email']);
                $stmt->execute();
                $role = '';
                $stmt->bind_result($role);
                if (!$stmt->fetch()) {
                    $stmt->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Akun belum terdaftar','kode'=>0]));
                }
                $stmt->close();
                if(in_array($role,['super admin','admin tempat','admin event', 'admin seniman'])){
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Invalid role','kode'=>0]));
                }
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','pesan'=>'Lanjut','kode'=>1]);
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
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
    public function updateUser($data){
        try{
            if (!isset($data['id_user']) || empty($data['id_user'])) {
                throw new Exception('ID User harus di isi !');
            }
            if (!isset($data['email']) || empty($data['email'])) {
                throw new Exception('Email harus di isi !');
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email invalid !');
            }
            if (isset($data['password']) && !empty($data['password'])){
                if (strlen($data['password']) < 8) {
                    throw new Exception('Password minimal 8 karakter !');
                }
                if (strlen($data['password']) > 15) {
                    throw new Exception('Password maksimal 15 karakter !');
                }
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                    throw new Exception('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
                }
            }
            if (!isset($data['nama_lengkap']) || empty($data['nama_lengkap'])) {
                throw new Exception('Nama lengkap harus di isi !');
            }
            if (!isset($data['no_telpon']) || empty($data['no_telpon'])) {
                throw new Exception('Nomer telepon harus di isi !');
            }
            if (!is_numeric($data['no_telpon'])) {
                throw new Exception('Nomer telepon harus berisi hanya angka !');
            }
            if (strlen($data['no_telpon']) < 8) {
                throw new Exception('Nomer telepon minimal 8 angka !');
            }
            if (strlen($data['no_telpon']) > 15) {
                throw new Exception('Nomer telepon maksimal 15 angka !');
            }
            if (substr($data['no_telpon'], 0, 2) !== '08') {
                throw new Exception('Nomer telepon harus dimulai dengan 08 !');
            }
            if (!isset($data['jenis_kelamin']) || empty($data['jenis_kelamin'])) {
                throw new Exception('Jenis kelamin harus di isi !');
            }
            if(!in_array($data['jenis_kelamin'], ['laki-laki','perempuan'])){
                throw new Exception('Invalid jenis kelamin !');
            }
            if (!isset($data['tempat_lahir']) || empty($data['tempat_lahir'])) {
                throw new Exception('Tempat lahir harus di isi !');
            }
            if (!isset($data['tanggal_lahir']) || empty($data['tanggal_lahir'])) {
                throw new Exception('Tanggal lahir harus di isi !');
            }
            //check tanggal
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_lahir = strtotime($data['tanggal_lahir']);
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid !');
            }
            // Compare the dates
            if ($tanggal_lahir > $tanggal_sekarang){
                throw new Exception('Tanggal lahir tidak bolek lebih dari sekarang !');
            }
            //check user
            $query = "SELECT role, foto FROM users WHERE id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $idUser = '';
            $stmt[0]->bind_result($role, $idUser);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Akun tidak ditemukan !');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admin seniman'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Role invalid','kode'=>2]));
            }
            //check email input
            $query = "SELECT id_user FROM users WHERE BINARY email = ? AND id_user != ? LIMIT 1";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('si', $data['email'],$data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->fetch()) {
                $stmt[2]->close();
                throw new Exception('Email sudah digunakan !');
            }
            $stmt[2]->close();
            //if upload file then update file
            if (isset($_FILES['foto']) & !empty($_FILES['foto']) && !is_null($_FILES['foto']) && $_FILES['foto']['error'] !== 4) {
                $folderAdmin = '/pengguna';
                //proses file
                $fileFoto = $_FILES['foto'];
                $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
                $size = filesize($fileFoto['name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        throw new Exception("Ukuran File maksimal '".(self::$sizeImg/1000000)."MB' !");
                    }
                } else {
                    throw new Exception('File harus jpg, jpeg, png !');
                }
                //simpan file
                $nameFile = '/'.$idUser.'.'.$extension;  
                $fileFotoPath = self::$folderPath.$folderAdmin.$nameFile;
                if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                    throw new Exception('Gagal menyimpan file !');
                }
            }
            //jika user mengubah password
            if(isset($data['password']) && !empty($data['password'])){
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $query = "UPDATE users SET email = ?, password = ?, nama_lengkap = ?, no_telpon = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ? WHERE id_user = ?";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param("sssssssi", $data['email'], $hashedPassword, $data['nama_lengkap'], $data['no_telpon'], $data['jenis_kelamin'], $data['tempat_lahir'], $data['tanggal_lahir'], $data['id_user']);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','pesan'=>'Akun berhasil diubah', 'kode'=>1]);
                    exit();
                } else {
                    $stmt->close();
                    throw new Exception('Akun gagal diubah !');
                }
            }else{
                $query = "UPDATE users SET email = ?, nama_lengkap = ?, no_telpon = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ? WHERE id_user = ?";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param("ssssssi", $data['email'], $data['nama_lengkap'], $data['no_telpon'], $data['jenis_kelamin'], $data['tempat_lahir'], $data['tanggal_lahir'], $data['id_user']);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','pesan'=>'Akun berhasil diubah', 'kode'=>1]);
                    exit();
                } else {
                    $stmt->close();
                    throw new Exception('Akun gagal diubah !');
                }
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function updatePasswordLupa($data){
        try{
            if (!isset($data['email']) || empty($data['email'])) {
                throw new Exception('Email harus di isi !');
            }
            if (!isset($data['password_baru']) && empty($data['password_baru'])){
                throw new Exception('Password harus di isi !');
            }
            if (strlen($data['password_baru']) < 8) {
                throw new Exception('Password minimal 8 karakter !');
            }
            if (strlen($data['password_baru']) > 15) {
                throw new Exception('Password maksimal 15 karakter !');
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_baru'])) {
                throw new Exception('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['email']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Akun tidak ditemukan !');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admin seniman'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User bukan masyarakat','kode'=>2]));
            }
            $hashedPassword = password_hash($data['password_baru'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ? WHERE BINARY email = ?";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param("si", $hashedPassword, $data['email']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','pesan'=>'Update Berhasil', 'kode'=>1]);
                exit();
            } else {
                $stmt->close();
                throw new Exception('Update Gagal !');
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function updatePasswordProfile($data){
        try{
            if (!isset($data['id_user']) || empty($data['id_user'])) {
                throw new Exception('ID User harus di isi !');
            }
            if (!isset($data['password_lama']) && empty($data['password_lama'])){
                throw new Exception('Password lama harus di isi !');
            }
            if (!isset($data['password_baru']) && empty($data['password_baru'])){
                throw new Exception('Password baru di isi !');
            }
            if (strlen($data['password_baru']) < 8) {
                throw new Exception('Password baru minimal 8 karakter !');
            }
            if (strlen($data['password_baru']) > 15) {
                throw new Exception('Password baru maksimal 15 karakter !');
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password_baru'])) {
                throw new Exception('Password baru harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');
            }
            //check user
            $query = "SELECT role, password FROM users WHERE id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $passDB = '';
            $stmt[0]->bind_result($role, $passDB);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Akun tidak ditemukan !');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admin seniman'])){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Role invalid','kode'=>2]));
            }
            if(!password_verify($data['password_lama'],$passDB)){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Password lama tidak cocok','kode'=>3]));
            }
            $hashedPassword = password_hash($data['password_baru'], PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = ? WHERE id_user = ?";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param("si", $hashedPassword, $data['id_user']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','pesan'=>'Update Berhasil', 'kode'=>1]);
                exit();
            } else {
                $stmt->close();
                throw new Exception('Update Gagal !');
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                    'kode'=>2
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
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../notfound.php');
}
$user = new UserMobile;
$cekEmail = function ($data) use ($user){
    $user->isExistUser($data);
};
$updateProfile = function ($data) use ($user){
    $user->updateUser($data);
};
$updatePasswordLupa = function ($data) use ($user){
    $user->updatePasswordLupa($data);
};
$updatePasswordProfile = function ($data) use ($user){
    $user->updatePasswordProfile($data);
};
?>