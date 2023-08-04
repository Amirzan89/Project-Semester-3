<?php
$rootDir = dirname(__DIR__);
require_once $rootDir.'Controller/Website/ChangePasswordController.php';
use Controllers\UserController;
use Controllers\Auth\JwtController;
// use controllers\Auth\ChangePasswordController;
use Laravel\Socialite\Facades\Socialite;
use Database\Database;
class LoginController{ 
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
    }
    public function Login($data){
        try{
            $con = self::$con;
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
                $rPass = "";
                $query = "SELECT * FROM users WHERE BINARY email LIKE '%" . $con->real_escape_string($email) . "%' LIMIT 1";
                $stmt = $con->prepare($query);
                $email = '%' . $email . '%';
                $stmt->bind_param('s', $email);
                $stmt->execute();
                foreach ($data as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
                $result = [];
                if ($stmt->fetch()) {
                    foreach ($data as $column) {
                        $result[$column] = $$column;
                    }
                    $stmt->close();
                    if(!password_verify($pass,$result['password'])){
                        throw new Exception(json_encode(['status'=>'error','message'=>'Password salah','code'=>400]));
                    }else{
                        // $data = $jwtController->createJWTWebsite($email,$refreshToken);
                        if(is_null($data)){
                            return ['status'=>'error','message'=>'create token error'];
                        }else{
                            if($data['status'] == 'error'){
                                return ['status'=>'error','message'=>$data['message']];
                            }else{
                                $data1 = ['email'=>$email,'number'=>$data['number']];
                                $encoded = base64_encode(json_encode($data1));
                                header('Content-Type: application/json');
                                setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                                setcookie('token2', $data['data']['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                                setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                                return json_encode(['status'=>'success','message'=>'Login sukses silahkan masuk dashboard']);
                                // return ['status'=>'success','message'=>'login sukses silahkan masuk dashboard']
                                    // ->cookie('token1',$encoded,time()+intval(env('JWT_REFRESH_TOKEN_EXPIRED')))
                                    // ->cookie('token2',$data['data']['token'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))
                                    // ->cookie('token3',$data['data']['refresh'],time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED')));
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
        }
    }
    // $client_id = 'your_google_client_id';
    // $client_secret = 'your_google_client_sec/ret';
    // $redirect_uri = 'your_redirect_uri';
    // Function to redirect user to Google's authorization page
    function redirectToProvider(){
        global $client_id, $redirect_uri;
    
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
    public function handleProviderCallback($data, $cookie){
        // return response()->json('barnggaavss');
        try {
            $jwtController = new JwtController();
            $changePasswordController = new ChangePasswordController();
            $user_google = Socialite::driver('google')->stateless()->user();
            // return response()->json('akgvbabvgvvv');
            $query = "SELECT nama FROM users WHERE BINARY email LIKE ?";
            $email = '%' . $user_google->getEmail() . '%';
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $name = '';
            $stmt->bind_result($name);
            if (!$stmt->fetch()) {
                $stmt->close();
                if((isset($cookie['token1']) || !empty($cookie['token1']) && (isset($cookie['token2']) || !empty($cookie['token2']) && (isset($cookie['token3']) || !empty($cookie['token3']))))){
                    $token1 = $cookie['token1'];
                    $token2 = $cookie['token2'];
                    $token3 = $cookie['token3'];
                    $email = base64_decode($token1);
                    $req = [
                        'email'=>$email,
                        'token'=>$token2
                    ];
                    $decoded = $jwtController->decode($req);
                    if($decoded['status'] == 'error'){
                        if($decoded['message'] == 'Expired token'){
                            // echo "\n update token \n";
                            $updated = $jwtController->updateTokenWebsite($email);
                            if($updated['status'] == 'error'){
                                // echo "update token failed";
                                header('Content-Type: application/json');
                                http_response_code(500);
                                return json_encode(['status'=>'error','message'=>$updated['message']],500);
                            }else{
                                $data1 = ['email'=>$email,'number'=>$updated['number']];
                                $encoded = base64_encode(json_encode($data1));
                                header('Location: /page/dashboard');
                                setcookie('token1', $encoded, time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                                setcookie('token2', $data['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                                exit();
                                // return redirect("/page/dashboard")->withCookies([cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),cookie('token2',$updated['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
                            }
                        }
                    }else{
                        $json = json_encode(['status' => 'success', 'data' => $decoded['data'][0][0]]);
                        header('Location: /dashboard?json=' . urlencode($json));
                        exit();
                        // return redirect("/dashboard")->with('json', new JsonResponse(['status'=>'success','data'=>$decoded['data'][0][0]]));
                    }
                //if user exist in database and doesnt login
                }else{
                    $data = $jwtController->createJWTWebsite($user_google->getEmail());
                    if(is_null($data)){
                        header('Content-Type: application/json');
                        http_response_code(500);
                        return json_encode(['status'=>'error','message'=>'create token error'],500);
                    }else{
                        if($data['status'] == 'error'){
                            header('Content-Type: application/json');
                            http_response_code(400);
                            return json_encode(['status'=>'error','message'=>$data['message']],400);
                        }else{
                            $encoded = base64_encode($user_google->getEmail());
                            header('Location: /page/dashboard');
                            setcookie('token1', $encoded, time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            setcookie('token2', $data['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            exit();
                            // return redirect("/page/dashboard")->withCookies([cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),cookie('token2',$data['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
                        }
                    }
                }
            //if user dont exist in database
            }else{
                $data = ['email'=>$user_google->getEmail(), 'nama'=>$user_google->getName()];
                return $changePasswordController->showVerify($data);
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
                    $data = $jwtController->createJWT($email);
                    if(is_null($data)){
                        throw new Exception(json_encode(['status'=>'error','message'=>'create token error']));
                    }else{
                        if($data['status'] == 'error'){
                            return json_encode(['status'=>'error','message'=>$data['message']]);
                        }else{
                            $encoded = base64_encode($email);
                            header('Location: /page/dashboard');
                            setcookie('token1', $encoded, time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            setcookie('token2', $data['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                            exit();
                            // return redirect('/dashboard');
                            // return redirect("/page/dashboard")->withCookies([cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),cookie('token2',$data['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
                        }
                    }
                }else{
                    throw new Exception(json_encode(['status'=>'error','message'=>'Akun gagal dibuat',!empty($register['code']) ? $register['code'] : 400]));
                    // throwing (['status'=>'error','message'=>'Akun Gagal Dibuat'],500);
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