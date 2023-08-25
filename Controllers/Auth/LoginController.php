<?php
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/Controllers/Website/ChangePasswordController.php';
require_once 'Controllers/UserController.php';
require_once 'Controllers/Auth/JWTController.php';
use Google\Service\Oauth2;
use Database\Database;
class LoginController{ 
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function Login($data, $uri =  null){
        try{
            $jwtController = new JwtController();
            $data = $data['request'];
            $email = $data["email"];
            $email = "Admin@gmail.com";
            $pass = $data["password"];
            $pass = "Admin@1234567890";
            if(!isset($email) || empty($email)){
                return ['status'=>'error','message'=>'Email tidak boleh kosong', 'code'=>400];
            } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['status'=>'error','message'=>'Email yang anda masukkan invalid', 'code'=>400];
            }else if(!isset($pass) || empty($pass)){
                return ['status'=>'error','message'=>'Password tidak boleh kosong', 'code'=>400];
            }else{
                $query = "SELECT id_user, email, nama, password FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $columns = ['id_user','email','nama', 'password'];
                $bindResultArray = [];
                foreach ($columns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
                $result = [];
                if ($stmt->fetch()) {
                    foreach ($columns as $column) {
                        $result[$column] = $$column;
                    }
                    $stmt->close();
                    if(!password_verify($pass,$result['password'])){
                        return ['status'=>'error','message'=>'Password salah','code'=>400];
                    }else{
                        $data = $jwtController->createJWTWebsite($data);
                        if(is_null($data)){
                            return ['status'=>'error','message'=>'create token error'];
                        }else{
                            if($data['status'] == 'error'){
                                return ['status'=>'error','message'=>$data['message']];
                            }else{
                                $data1 = ['email'=>$email,'number'=>$data['number'],'expire'=>time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])];
                                $encoded = base64_encode(json_encode($data1));
                                header('Content-Type: application/json');
                                setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                                setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                return (['status'=>'success','message'=>'Login sukses silahkan masuk dashboard']);
                            }
                        }
                    }
                }else{
                    $stmt->close();
                    return ['status'=>'error','message'=>'Email tidak ditemukan','code'=>400];
                    // exit();
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
            return $responseData;
        }
    }
    function redirectToProvider(){
        // global $client_id, $redirect_uri;
        $client_id = $_SERVER['GOOGLE_CLIENT_ID'];
        $redirect_uri = $_SERVER['GOOGLE_REDIRECT'];
        $auth_url = "https://accounts.google.com/o/oauth2/auth?" .
            "client_id={$client_id}&" .
            "redirect_uri={$redirect_uri}&" .
            "response_type=code&" .
            "scope=email profile";
        header("Location: {$auth_url}");
        exit();
    }
    
    public function handleProviderCallback($data, $uri=null, $param){
        try {
            $data = $data['request'];
            $jwtController = new JwtController();
            $changePasswordController = new ChangePasswordController();
            $client = new Google_Client();
            $client->setClientId($_SERVER['GOOGLE_CLIENT_ID']);
            $client->setClientSecret($_SERVER['GOOGLE_APP_SECRET']);
            $client->addScope('https://www.googleapis.com/auth/drive');
            $client->setRedirectUri($_SERVER['GOOGLE_REDIRECT']);
            $token = $client->fetchAccessTokenWithAuthCode($param['code']);
            $client->setAccessToken($token['access_token']);
            $google_oauth = new Google_Service_Oauth2($client);
            $user_google = $google_oauth->userinfo->get();
            $query = "SELECT nama FROM users WHERE BINARY email = ?";
            $email = $user_google->getEmail();
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $name = '';
            $stmt->bind_result($name);
            //check if user is exist on database
            if ($stmt->fetch()) {
                $stmt->close();
                //check if user have login 
                if(isset($_COOKIE['token1']) && (isset($cookie['token3']))){
                    $token3 = $_COOKIE['token3'];
                    $token1 = $_COOKIE['token1'];
                    $email = base64_decode($token1);
                    $reqRefresh = [
                        'email'=>$email,
                        'token'=>$token3,
                        'opt'=>'token'
                    ];
                    $decodedRefresh = $jwtController->decode($reqRefresh);
                    if(isset($_COOKIE['token2'])){
                        $token2 = $_COOKIE['token2'];
                        $req = [
                            'email'=>$email,
                            'token'=>$token2,
                            'opt'=>'token'
                        ];
                        $decoded = $jwtController->decode($req);
                        if($decoded['status'] == 'error'){
                            if($decoded['message'] == 'Expired token'){
                                $updated = $jwtController->updateTokenWebsite($decodedRefresh['data']['data']);
                                if($updated['status'] == 'error'){
                                    header('Content-Type: application/json');
                                    http_response_code(500);
                                    return ['status'=>'error','message'=>$updated['message'], 'code'=>500];
                                }else{
                                    header('Location: /dashboard');
                                    setcookie('token2', $data['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                                    exit();
                                }
                            }
                        }else{
                            header('Location: /dashboard');
                            exit();
                        }
                    }else{
                        $updated = $jwtController->updateTokenWebsite($decodedRefresh['data']['data']);
                        if($updated['status'] == 'error'){
                            return ['status'=>'error','message'=>'update token error','code'=>500];
                        }else{
                            setcookie('token2', $updated['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']), '/');
                            header('Location: /dashboard');
                            exit();
                        }
                    }
                //if user exist in database and doesnt login
                }else{
                    $data = $jwtController->createJWTWebsite(['email'=>$user_google->getEmail()]);
                    if($data['status'] == 'error'){
                        return ['status'=>'error','message'=>$data['message'],'code'=>isset($data['code']) ? $data['code'] : 400];
                    }else{
                        // $encoded = base64_encode([$user_google->getEmail()]);
                        $data1 = ['email'=>$email,'number'=>$data['number'],'expire'=>time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])];
                        $encoded = base64_encode(json_encode($data1));
                        setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                        setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                        setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                        header('Location: /dashboard');
                        exit();
                    }
                }
            //if user dont exist in database
            }else{
                $stmt->close();
                $data = ['email'=>$user_google->getEmail(), 'nama'=>$user_google->getName()];
                return $changePasswordController->showRegisterGoogle($data);
            }
        } catch (\Exception $e) {
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
    // public function getPassPage(Request $request, User $user){
    //     $validator = Validator::make($request->all(), [
    //         'email'=>'required|email',
    //         'nama'=>'required',
    //     ],[
    //         'nama.required'=>'nama wajib di isi',
    //         'email.required'=>'Email wajib di isi',
    //         'email.email'=>'Email yang anda masukkan invalid',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['status'=>'error','message'=>$validator->failed()],400);
    //     }
    //     $email = $request->input('email');
    //     $nama = $request->input('nama');
    //     return view('page.changePassword',['email'=>$email,'nama'=>$nama,'div'=>'register','description'=>'changePass','code'=>'','link'=>'']);
    // }
    public function GooglePass($data, $user){
        try{
            $jwtController = new JwtController();
            $userController = new UserController();
            if (!isset($data['email']) || empty($data['email'])) {
                return ['status'=>'error','message'=>'Email wajib di isi','code'=>400];
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['status'=>'error','message'=>'Email yang anda masukkan invalid','code'=>400];
            }
            if (!isset($data['password']) || empty($data['password'])) {
                return ['status'=>'error','message'=>'Password wajib di isi','code'=>400];
            } elseif (strlen($data['password']) < 8) {
                return ['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400];
            } elseif (strlen($data['password']) > 25) {
                return ['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400];
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                return ['status' => 'error', 'message' => 'Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400];
            }
            // Validate 'nama' field
            if (!isset($data['nama']) || empty($data['nama'])) {
                return ['status'=>'error','message'=>'Nama harus di isi','code'=>400];
            }
            $nama = $data['nama'];
            $email = $data['email'];
            $query = "SELECT nama FROM users WHERE BINARY email = ?";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $name = '';
            $stmt->bind_result($name);
            if ($stmt->fetch()) {
                $stmt->close();
                $register = $userController->createUser($data, 'google');
                if($register['status'] == 'success'){
                    $data = $jwtController->createJWTWebsite($email);
                    if(is_null($data)){
                        return ['status'=>'error','message'=>'create token error'];
                    }else{
                        if($data['status'] == 'error'){
                            return ['status'=>'error','message'=>$data['message']];
                        }else{
                            $encoded = base64_encode($email);
                            header('Location: /dashboard');
                            setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                            setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                            setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                            exit();
                        }
                    }
                }else{
                    return ['status'=>'error','message'=>'Akun gagal dibuat',!empty($register['code']) ? $register['code'] : 400];
                }
            }else{
                $stmt->close();
                return ['status'=>'error','message'=>'Email sudah digunakan'];
            }
        } catch (\Exception $e) {
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