<?php
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/Controllers/Website/ChangePasswordController.php';
use Controllers\UserController;
// use Controllers\Auth\JwtController;
require_once 'Controllers/Auth/JWTController.php';
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
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
                throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak boleh kosong', 'code'=>400]));
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email yang anda masukkan invalid', 'code'=>400]));
            }else if(!isset($pass) || empty($pass)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Password tidak boleh kosong', 'code'=>400]));
            }else{
                $query = "SELECT id_user, email, nama FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $columns = ['id_user','email','nama'];
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
                        throw new Exception(json_encode(['status'=>'error','message'=>'Password salah damn','code'=>400]));
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
                                // $cv1 = json_encode(['data' => 'your_data', 'expires' => time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])]);
                                setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                                setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                echo json_encode(['status'=>'success','message'=>'Login sukses silahkan masuk dashboard']);
                                exit();
                            }
                        }
                    }
                }else{
                    $stmt->close();
                    throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak ditemukan','code'=>400]));
                    // exit();
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
            $jsonResponse = json_encode($responseData);
            header('Content-Type: application/json');
            http_response_code(!empty($error['code']) ? $error['code'] : 400);
            echo $jsonResponse;
            exit();
        }
    }
    // $client_id = 'your_google_client_id';
    // $client_secret = 'your_google_client_secret';
    // $redirect_uri = 'your_redirect_uri';
    // Function to redirect user to Google's authorization page
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
    
    // Function to handle the callback after user grants permission
    // public function handleProviderCallback(){
    //     global $client_id, $client_secret, $redirect_uri;
    
    //     if (isset($_GET['code'])) {
    //         $code = $_GET['code'];
        
    //         // Exchange the authorization code for an access token
    //         $token_url = "https://accounts.google.com/o/oauth2/token";
    //         $params = [
    //             'code' => $code,
    //             'client_id' => $client_id,
    //             'client_secret' => $client_secret,
    //             'redirect_uri' => $redirect_uri,
    //             'grant_type' => 'authorization_code'
    //         ];
        
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $token_url);
    //         curl_setopt($ch, CURLOPT_POST, true);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         $response = curl_exec($ch);
    //         curl_close($ch);
        
    //         $access_token_data = json_decode($response, true);
        
    //         if (isset($access_token_data['access_token'])) {
    //             // Use the access token to fetch user information
    //             $access_token = $access_token_data['access_token'];
    //             $user_info_url = "https://www.googleapis.com/oauth2/v2/userinfo";
    //             $headers = [
    //                 "Authorization: Bearer {$access_token}"
    //             ];
            
    //             $ch = curl_init();
    //             curl_setopt($ch, CURLOPT_URL, $user_info_url);
    //             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //             $user_info_response = curl_exec($ch);
    //             curl_close($ch);
            
    //             $user_info = json_decode($user_info_response, true);
    //         }
    //     }
    // }
    // public function redirectToProvider(){
    //     return Socialite::driver('google')->redirect();
    // }
    // $user_google = Socialite::driver('google')->stateless()->user();
    public function handleProviderCallback($data, $uri=null, $param){
        try {
            $data = $data['request'];
            $jwtController = new JwtController();
            $changePasswordController = new ChangePasswordController();
            $client = new Google_Client();
            $client->setClientId($_SERVER['GOOGLE_CLIENT_ID']);
            $client->setClientSecret($_SERVER['GOOGLE_CLIENT_SECRET']);
            $client->setRedirectUri($_SERVER['GOOGLE_REDIRECT']);
            $client->addScope('email');
            $client->addScope('profile');
            $token = $client->fetchAccessTokenWithAuthCode($param['code']);
            // $client->setAccessToken($token);
            // Get user info from the Google API
            $googleService = new Google_Service_Oauth2($token);
            $user_google = $googleService->userinfo->get();
            $query = "SELECT nama FROM users WHERE BINARY email LIKE ?";
            $email = '%' . $user_google->getEmail() . '%';
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
                                    throw new Exception(json_encode(['status'=>'error','message'=>$updated['message'], 'code'=>500]));
                                }else{
                                    header('Location: /dashboard');
                                    setcookie('token2', $data['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
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
                            throw new Exception(json_encode(['status'=>'error','message'=>'update token error','code'=>500]));
                        }else{
                            setcookie('token2', $updated['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']), '/');
                            header('Location: /dashboard');
                            exit();
                        }
                    }
                //if user exist in database and doesnt login
                }else{
                    $data = $jwtController->createJWTWebsite($user_google->getEmail());
                    if(is_null($data)){
                        header('Content-Type: application/json');
                        http_response_code(500);
                        throw new Exception(json_encode(['status'=>'error','message'=>'create token error','code'=>500]));
                    }else{
                        if($data['status'] == 'error'){
                            header('Content-Type: application/json');
                            http_response_code(400);
                            throw new Exception(json_encode(['status'=>'error','message'=>$data['message']]));
                        }else{
                            $encoded = base64_encode($user_google->getEmail());
                            header('Location: /dashboard');
                            setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                            setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                            exit();
                        }
                    }
                }
            //if user dont exist in database
            }else{
                $data = ['email'=>$user_google->getEmail(), 'nama'=>$user_google->getName()];
                return $changePasswordController->showVerify($data);
            }
        } catch (\Exception $e) {
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
            $jsonResponse = json_encode($responseData);
            header('Content-Type: application/json');
            http_response_code(!empty($error['code']) ? $error['code'] : 400);
            echo $jsonResponse;
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
                throw new Exception(json_encode(['status'=>'error','message'=>'Email wajib di isi','code'=>400]));
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email yang anda masukkan invalid','code'=>400]));
            }
            if (!isset($data['password']) || empty($data['password'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password wajib di isi','code'=>400]));
            } elseif (strlen($data['password']) < 8) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password minimal 8 karakter','code'=>400]));
            } elseif (strlen($data['password']) > 25) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Password maksimal 25 karakter','code'=>400]));
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka', 'code' => 400]));
            }
            // Validate 'nama' field
            if (!isset($data['nama']) || empty($data['nama'])) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Nama harus di isi','code'=>400]));
            }
            $nama = $data['nama'];
            $email = $data['email'];
            $query = "SELECT nama FROM users WHERE BINARY email LIKE ?";
            $email = '%' . $email. '%';
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
                        throw new Exception(json_encode(['status'=>'error','message'=>'create token error']));
                    }else{
                        if($data['status'] == 'error'){
                            return json_encode(['status'=>'error','message'=>$data['message']]);
                        }else{
                            $encoded = base64_encode($email);
                            header('Location: /dashboard');
                            setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                            setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                            exit();
                        }
                    }
                }else{
                    throw new Exception(json_encode(['status'=>'error','message'=>'Akun gagal dibuat',!empty($register['code']) ? $register['code'] : 400]));
                }
            }else{
                $stmt->close();
                throw new Exception(json_encode(['status'=>'error','message'=>'Email sudah digunakan']));
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
            $jsonResponse = json_encode($responseData);
            header('Content-Type: application/json');
            http_response_code(!empty($error['code']) ? $error['code'] : 400);
            echo $jsonResponse;
        }
    }
}
?>