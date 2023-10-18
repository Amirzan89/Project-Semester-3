<?php
require('User.php');
require('Jwt.php');
require_once('koneksi.php');
function authenticate($request,$data,$con = null){
    try{
    $user = new User();
    $jwt = new Jwt();
    if(isset($_SERVER['HTTP_REFERER'])){
        $previousUrl = $_SERVER['HTTP_REFERER'];
        $path = parse_url($previousUrl, PHP_URL_PATH);
    }else{
        $path = isset($data['uri']) ? $data['uri'] : null;
    }
    if(isset($_COOKIE['token1'] ) && isset($_COOKIE['token3'])){
        $token1 = $_COOKIE['token1'];
        $token3 = $_COOKIE['token3'];
        $tokenDecode1 = json_decode(base64_decode($token1),true);
        $email = $tokenDecode1['email'];
        $number = $tokenDecode1['number'];
        $authPage = ['login.php','register.php','password/reset','verify/password','verify/email','auth/redirect','auth/google','/'];
        if(in_array(ltrim($data['uri'],'/'),$authPage) && $data['method'] == "GET"){
            $auth = ['/login.php','/register.php','/password/reset','/verify/password','/verify/email','/auth/redirect','/auth/google','/'];
            if (in_array(ltrim($path,'/'), $authPage)) {
                header('Location: /dashboard.php');
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
            if($con == null){
                $db = koneksi::getInstance();
                $con = $db->getConnection();
            }
            //check user is exist in database
            $exist = $user->isExistUser($email);
            if($exist['status'] == 'error'){
                setcookie('token1', '', time() - 3600, '/');
                setcookie('token2', '', time() - 3600, '/');
                setcookie('token3', '', time() - 3600, '/');
                header('Location: /login.php');
                exit();
            }else{
                if(!$exist['data']){
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login.php');
                    exit();
                }else{
                    //check token if exist in database
                    if($jwt->checkExistRefreshWebsiteNew(['token'=>$token3],$con)){
                        $decodedRefresh = jwt::decode_and_validate_jwt($decodeRefresh);
                        if($decodedRefresh['status'] == 'error'){
                            if($decodedRefresh['message'] == 'Expired token'){
                                setcookie('token1', '', time() - 3600, '/');
                                setcookie('token2', '', time() - 3600, '/');
                                setcookie('token3', '', time() - 3600, '/');
                                header('Location: /login.php');
                                exit();
                            }else if($decodedRefresh['message'] == 'invalid email'){
                                setcookie('token1', '', time() - 3600, '/');
                                setcookie('token2', '', time() - 3600, '/');
                                setcookie('token3', '', time() - 3600, '/');
                                header('Location: /login.php');
                                exit();
                            }
                        //if token refresh success decoded and not expired
                        }else{
                            //check if token 2 exist
                            if(isset($_COOKIE['token2'])){
                                $token2 = $_COOKIE['token2'];
                                $decode = [
                                    'email'=>$email,
                                    'token'=>$token2,
                                    'opt'=>'token'
                                ];
                                $decoded = Jwt::decode_and_validate_jwt($decode);
                                if($decoded['status'] == 'error'){
                                    if($decoded['message'] == 'Expired token'){
                                        $updated = $jwt->updateTokenWebsite($decodedRefresh['data']['data']);
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
                                    if($data['uri'] === 'users/google' && $data['method'] == "GET"){
                                        // $data = [$decoded['data'][0][0]];
                                        return ['status'=>'success','data'=>$request];
                                    }
                                    //create csrf token 
                                    if (empty($_SESSION['key'])) {
                                        $_SESSION['key'] = bin2hex(random_bytes(32));
                                    }
                                    global $csrf;
                                    $csrf = hash_hmac('sha256', 'this is some string: index.php', $_SESSION['key']);
                                    return ['status'=>'success','data'=>$decoded['data']];
                                }
                            //if token 2 disappear
                            }else{
                                $updated = $jwt->updateTokenWebsite($decodedRefresh['data']);
                                if($updated['status'] == 'error'){
                                    return ['status'=>'error','message'=>'update token error','code'=>500];
                                }else{
                                    setcookie('token2', $updated['data'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']), '/');
                                    // foreach($updated['data'] as $key => $value){
                                    //     $request[$key] = $value;
                                    // }
                                    // return ['status'=>'success','data'=>$updated['data']];
                                }
                            }
                        }
                    //if token is not exist in database
                    }else{
                        $delete = $jwt->deleteRefreshWebsite($email,$number);
                        if($delete['status'] == 'error'){
                            setcookie('token1', '', time() - 3600, '/');
                            setcookie('token2', '', time() - 3600, '/');
                            setcookie('token3', '', time() - 3600, '/');
                            header('Location: /login.php');
                            exit();
                        }else{
                            setcookie('token1', '', time() - 3600, '/');
                            setcookie('token2', '', time() - 3600, '/');
                            setcookie('token3', '', time() - 3600, '/');
                            header('Location: /login.php');
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
        $page = ['/dashboard.php','/pengaturan','/admin.php','/event/dashboard','/tempat/dashboard','/testing/seniman/dashboard','/testing/event/dashboard','/testing/tempat/dashboard'];
        if(in_array($data['uri'],$page)){
            if(isset($_COOKIE["token1"])){
                $token1 = $_COOKIE['token1'];
                $token1 = json_decode(base64_decode($token1),true);
                $email = $token1['email'];
                $number = $token1['number'];
                $delete = $jwt->deleteRefreshWebsite($email,$number);
                if($delete['status'] == 'error'){
                    return json_encode(['status'=>'error','message'=>'delete token error'],500);
                }else{
                    setcookie('token1', '', time() - 3600, '/');
                    setcookie('token2', '', time() - 3600, '/');
                    setcookie('token3', '', time() - 3600, '/');
                    header('Location: /login.php');
                    exit();
                }
            }else{
                setcookie('token1', '', time() - 3600, '/');
                setcookie('token2', '', time() - 3600, '/');
                setcookie('token3', '', time() - 3600, '/');
                header('Location: /login.php');
                exit();
            }
        }
    }
    }catch(Exception $e){
        echo $e->getTraceAsString();
    }
}
?>