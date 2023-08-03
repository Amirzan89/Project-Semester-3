<?php
// namespace Controllers\Auth;
use Database\Database;
// use Exception;
class LoginController{ 
    public function Login($data){
        try{
            $database = Database::getInstance();
            $con = $database->getConnection();
            $email = $data["email"];
            $email = "Admin@gmail.com";
            $pass = $data["password"];
            $pass = "Admin@1234567890";
            if(empty($email)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak boleh kosong', 'code'=>400]));
            }else if(empty($pass)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Password tidak boleh kosong', 'code'=>400]));
            }else{
                $rPass = "";
                $query = "SELECT * FROM users WHERE BINARY email LIKE '%" . $con->real_escape_string($email) . "%' LIMIT 1";
                $stmt = $con->prepare($query);
                $email = '%' . $email . '%';
                $stmt->bind_param('s', $email);
                $stmt->execute();
                // $stmt->bind_result($email);
                foreach ($data as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt, 'bind_result'], $bindResultArray);
                $result = [];
                if ($stmt->fetch()) {
                    foreach ($data as $column) {
                        $result[$column] = $$column;
                    }
                    // throw new Exception(json_encode(['status'=>'error','message'=>'Password salah', 'code'=>400]));
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
                $stmt->close();
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
    public function handleProviderCallback(){
        global $client_id, $client_secret, $redirect_uri;
    
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
        
            // Exchange the authorization code for an access token
            $token_url = "https://accounts.google.com/o/oauth2/token";
            $params = [
                'code' => $code,
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_type' => 'authorization_code'
            ];
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $token_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
        
            $access_token_data = json_decode($response, true);
        
            if (isset($access_token_data['access_token'])) {
                // Use the access token to fetch user information
                $access_token = $access_token_data['access_token'];
                $user_info_url = "https://www.googleapis.com/oauth2/v2/userinfo";
                $headers = [
                    "Authorization: Bearer {$access_token}"
                ];
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $user_info_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $user_info_response = curl_exec($ch);
                curl_close($ch);
            
                $user_info = json_decode($user_info_response, true);
            
                // Now you have the user's information in $user_info
                // You can save this information in your application's database
                // or use it to log the user in, etc.
            }
        }
    }
    // public function redirectToProvider(){
    //     return Socialite::driver('google')->redirect();
    // }
    // public function handleProviderCallback(Request $request,ChangePasswordController $changePasswordController, JWTController $jwtController, RefreshToken $refreshToken){
    //     // return response()->json('barnggaavss');
    //     try {
    //         // return response()->json('akgvbabvgvvv');
    //         $user_google = Socialite::driver('google')->stateless()->user();
    //         if(User::select('email')->whereRaw("BINARY email LIKE '%".$user_google->getEmail()."%'")->limit(1)->exists()){
    //             if($request->hasCookie("token1") && $request->hasCookie("token2")){
    //                 $token1 = $request->cookie('token1');
    //                 $token2 = $request->cookie('token2');
    //                 $email = base64_decode($token1);
    //                 $req = [
    //                     'email'=>$email,
    //                     'token'=>$token2
    //                 ];
    //                 $decoded = $jwtController->decode($req);
    //                 if($decoded['status'] == 'error'){
    //                     if($decoded['message'] == 'Expired token'){
    //                         echo "\n update token \n";
    //                         $updated = $jwtController->updateTokenWebsite($email);
    //                         if($updated['status'] == 'error'){
    //                             echo "update token failed";
    //                             return response()->json(['status'=>'error','message'=>$updated['message']],500);
    //                         }else{
    //                             $data1 = ['email'=>$email,'number'=>$updated['number']];
    //                             $encoded = base64_encode(json_encode($data1));
    //                             return redirect("/page/dashboard")->withCookies([
    //                                 cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),
    //                             cookie('token2',$updated['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
    //                         }
    //                     }
    //                 }else{
    //                     return redirect("/dashboard")->with('json', new JsonResponse(['status'=>'success','data'=>$decoded['data'][0][0]]));
    //                 }
    //             //if user exist in database and doesnt login
    //             }else{
    //                 // $data = $jwtController->createJWTWebsite($user_google->getEmail(),$refreshToken);
    //                 $data = $jwtController->createJWTWebsite($request,$refreshToken);
    //                 if(is_null($data)){
    //                     return response()->json(['status'=>'error','message'=>'create token error'],500);
    //                 }else{
    //                     if($data['status'] == 'error'){
    //                         return response()->json(['status'=>'error','message'=>$data['message']],400);
    //                     }else{
    //                         $encoded = base64_encode($user_google->getEmail());
    //                         return redirect("/page/dashboard")->withCookies([cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),cookie('token2',$data['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
    //                     }
    //                 }
    //             }
    //         //if user dont exist in database
    //         }else{
    //             $data = ['email'=>$user_google->getEmail(), 'nama'=>$user_google->getName()];
    //             $costum = new Request();
    //             $costum->replace($data);
    //             return $changePasswordController->showVerify($costum);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json('Error: ' . $e->getMessage() . ', Code: ' . $e->getCode() . ', File: ' . $e->getFile() . ', Line: ' . $e->getLine());
    //     }
    // }
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
    // public function GooglePass(Request $request, User $user, JWTController $jwtController, RefreshToken $refreshToken){
    //     try{
    //         $validator = Validator::make($request->all(), [
    //         'email'=>'required|email',
    //         'password' => [
    //                 'required',
    //                 'string',
    //                 'min:8',
    //                 'max:25',
    //                 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
    //             ],
    //         'username'=>'required',
    //         'nama'=>'required',
    //         ],[
    //             'nama.required'=>'nama wajib di isi',
    //             'email.required'=>'Email wajib di isi',
    //             'email.email'=>'Email yang anda masukkan invalid',
    //             'password.required'=>'Password wajib di isi',
    //             'password.min'=>'Password minimal 8 karakter',
    //             'password.max'=>'Password maksimal 25 karakter',
    //             'password.regex'=>'Password baru wajib terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(['status'=>'error','message'=>$validator->errors()->toArray()],400);
    //         }
    //         $username = $request->input('username');
    //         $nama = $request->input('nama');
    //         $email = $request->input('email');
    //         $password = $request->input('password');
    //         if (User::select("username")->whereRaw("BINARY username LIKE '%$username%'")->limit(1)->exists()){
    //             return response()->json(['status'=>'error','message'=>'Username sudah digunakan'],400);
    //         }else if (User::select("email")->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->exists()){
    //             return response()->json(['status'=>'error','message'=>'Email sudah digunakan'],400);
    //         }else{
    //             $user->username = $username;
    //             $user->email = $email;
    //             $user->nama = $nama;
    //             $user->password = Hash::make($password);
    //             $user->email_verified = true;
    //             if($user->save()){
    //                 $data = $jwtController->createJWT($email,$refreshToken);
    //                 if(is_null($data)){
    //                     return response()->json(['status'=>'error','message'=>'create token error']);
    //                 }else{
    //                     if($data['status'] == 'error'){
    //                         return response()->json(['status'=>'error','message'=>$data['message']],400);
    //                     }else{
    //                         $encoded = base64_encode($email);
    //                         // return redirect('/dashboard');
    //                         return redirect("/page/dashboard")->withCookies([cookie('token1',$encoded,time()+intval(env('JWT_ACCESS_TOKEN_EXPIRED'))),cookie('token2',$data['data'],time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')))]);
    //                     }
    //                 }
    //             }else{
    //                 return response()->json(['status'=>'error','message'=>'Akun Gagal Dibuat'],400);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json($e->getMessage());
    //         // return redirect()->route('login');
    //     }
    // }
}
?>