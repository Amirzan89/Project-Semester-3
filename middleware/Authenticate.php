<?php
require_once 'Controllers/Auth/JWTController.php';
require_once 'Controllers/UserController.php';
class Authenticate
{
    public function handle($request,$data = null){
        $userController = new UserController();
        $jwtController = new JwtController();
        // $pathh = $request->path();
        $previousUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $path = parse_url($previousUrl, PHP_URL_PATH);
        if(isset($_COOKIE['token1'] ) && isset($_COOKIE['token3'])){
            $token1 = $_COOKIE['token1'];
            // $token2 = $_COOKIE['token2'];
            $token3 = $_COOKIE['token3'];
            $tokenDecode1 = json_decode(base64_decode($token1),true);
            $email = $tokenDecode1['email'];
            $number = $tokenDecode1['number'];
            $authPage = ['login','register','password/reset','verify/password','verify/email','auth/redirect','auth/google','/'];
            if(in_array(ltrim($data['uri'],'/'),$authPage) && $data['method'] == "GET"){
                $auth = ['/login','/register','/password/reset','/verify/password','/verify/email','/auth/redirect','/auth/google','/'];
                if (in_array('/'.ltrim($path,'/'), $authPage)) {
                    header('Location: /dashboard');
                } else {
                    header("Location: $path");
                }
                exit();
            }else{
                $decodeRefresh = [
                    'email'=>$email,
                    'token'=>$token3,
                    'opt'=>'refresh'
                ];
                $decode1 = [
                    'email'=>$email,
                    'token'=>$token3,
                    'opt'=>'token'
                ];
                //check user is exist in database
                $exist = $userController->isExistUser($email);
                if($exist['status'] == 'error'){
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login');
                    exit();
                }else{
                    if(!$exist['data']){
                        setcookie('token1', '', time() - 3600, '/');
                        setcookie('token2', '', time() - 3600, '/');
                        setcookie('token3', '', time() - 3600, '/');
                        header('Location: /login');
                        exit();
                    }else{
                        //check token if exist in database
                        if($jwtController->checkExistRefreshWebsiteNew(['token'=>$token3])){
                            $decodedRefresh = $jwtController->decode($decodeRefresh);
                            if($decodedRefresh['status'] == 'error'){
                                if($decodedRefresh['message'] == 'Expired token'){
                                    setcookie('token1', '', time() - 3600, '/');
                                    setcookie('token2', '', time() - 3600, '/');
                                    setcookie('token3', '', time() - 3600, '/');
                                    header('Location: /login');
                                    exit();
                                }else if($decodedRefresh['message'] == 'invalid email'){
                                    setcookie('token1', '', time() - 3600, '/');
                                    setcookie('token2', '', time() - 3600, '/');
                                    setcookie('token3', '', time() - 3600, '/');
                                    header('Location: /login');
                                    exit();
                                }
                            //if token refresh success decoded and not expired
                            }else{
                                //check if token2 exist
                                if(isset($_COOKIE['token2'])){
                                    $token2 = $_COOKIE['token2'];
                                    $decode = [
                                        'email'=>$email,
                                        'token'=>$token2,
                                        'opt'=>'token'
                                    ];
                                    $decoded = $jwtController->decode($decode);
                                    if($decoded['status'] == 'error'){
                                        if($decoded['message'] == 'Expired token'){
                                            $updated = $jwtController->updateTokenWebsite($decodedRefresh['data']['data']);
                                            if($updated['status'] == 'error'){
                                                return ['status'=>'error','message'=>'update token error','code'=>500];
                                            }else{
                                                setcookie('token2', $updated['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']), '/');
                                                foreach($updated['data'] as $key => $value){
                                                    $request[$key] = $value;
                                                }
                                                return ['status'=>'success','data'=>$request];
                                            }
                                        }else{
                                            return ['status'=>'error','message'=>$decoded['message'],'code'=>500];
                                        }
                                    //if success decode
                                    }else{
                                        $decoded['data'][0][0]['number'] = $number;
                                        if($data['uri'] === 'users/google' && $data['method'] == "GET"){
                                            $data = [$decoded['data'][0][0]];
                                            return ['status'=>'success','data'=>$request];
                                        }
                                        return ['status'=>'success','data'=>$request];
                                    }
                                //if token 2 disappear
                                }else{
                                    $updated = $jwtController->updateTokenWebsite($decodedRefresh['data']['data']);
                                    if($updated['status'] == 'error'){
                                        return ['status'=>'error','message'=>'update token error','code'=>500];
                                    }else{
                                        setcookie('token2', $updated['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']), '/');
                                        foreach($updated['data'] as $key => $value){
                                            $request[$key] = $value;
                                        }
                                        return ['status'=>'success','data'=>$request];
                                    }
                                }
                            }
                        //if token is not exist in database
                        }else{
                            $delete = $jwtController->deleteRefreshWebsite($email,$number);
                            if($delete['status'] == 'error'){
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
                    }
                }
                return ['status'=>'success','data'=>$request];
                // return $request;
            }
        //if cookie gone
        }else{
            $page = ['/dashboard','/device','/pengaturan','/laporan','/edukasi'];
            if(in_array($data['uri'],$page)){
                if(isset($_COOKIE["token1"])){
                    $token1 = $_COOKIE['token1'];
                    $token1 = json_decode(base64_decode($token1),true);
                    $email = $token1['email'];
                    $number = $token1['number'];
                    $delete = $jwtController->deleteRefreshWebsite($email,$number);
                    if($delete['status'] == 'error'){
                        return json_encode(['status'=>'error','message'=>'delete token error'],500);
                    }else{
                        setcookie('token1', '', time() - 3600, '/');
                        setcookie('token2', '', time() - 3600, '/');
                        setcookie('token3', '', time() - 3600, '/');
                        header('Location: /login');
                        exit();
                    }
                }else{
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login');
                    exit();
                }
            }
            return ['status'=>'success','data'=>$request];
        }
    }
}
?>