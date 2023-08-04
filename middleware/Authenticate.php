<?php
// namespace App\Http\Middleware;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\Auth\JWTController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\URL;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Cookie;
// use Illuminate\Http\Response;
// use App\Models\User;
// use Closure;
use Controllers\UserController;
use controllers\Auth\JwtController;
class Authenticate
{
    public function handle($request){
        $userController = new UserController();
        $jwtController = new JwtController();
        $pathh = $request->path();
        $previousUrl = url()->previous();
        // echo PHP_URL_PATH;
        $path = parse_url($previousUrl, PHP_URL_PATH);
        // return response()->json(parse_url($previousUrl, PHP_URL_PATH));
        if($request->hasCookie("token1") && $request->hasCookie("token2") && $request->hasCookie("token3")){
            $token1 = $request->cookie('token1');
            $token2 = $request->cookie('token2');
            $token3 = $request->cookie('token3');
            $tokenDecode1 = json_decode(base64_decode($token1),true);
            $email = $tokenDecode1['email'];
            $number = $tokenDecode1['number'];
            $authPage = ['login','register','password/reset','verify/password','verify/email','auth/redirect','auth/google','/'];
            if(in_array($request->path(),$authPage) && $request->isMethod("get")){
                $auth = ['/login','/register','/password/reset','/verify/password','/verify/email','/auth/redirect','/auth/google','/'];
                if (in_array('/'.ltrim($path,'/'), $authPage)) {
                    $response = header('Location: /page/dashboard');
                } else {
                    $response = redirect($path);
                }
                $cookies = $response->headers->getCookies();
                foreach ($cookies as $cookie) {
                    if ($cookie->getName() === 'token1') {
                        $expiryTime = $cookie->getExpiresTime();
                        $currentTime = time();
                        if ($expiryTime && $expiryTime < $currentTime) {
                            $response->withCookie(Cookie::forget('token1'));
                        }
                        // $response->withCookie('token1', $token1, $cookie->getExpiresTime());
                    } else if ($cookie->getName() === 'token3') {
                        $expiryTime = $cookie->getExpiresTime();
                        $currentTime = time();
                        if ($expiryTime && $expiryTime < $currentTime) {
                            $response->withCookie(Cookie::forget('token3'));
                        }
                        // $response->withCookie('token3', $token3, $cookie->getExpiresTime());
                    }
                }
                return $response;
            }else{
                $decode = [
                    'email'=>$email,
                    'token'=>$token2,
                    'opt'=>'token'
                ];
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
                $reqOld = [
                    'email'=>$email,
                    'number'=>$number
                ];
                //check user is exist in database
                $exist = $userController->isExistUser($email);
                if($exist['status'] == 'error'){
                    return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                }else{
                    if(!$exist['data']){
                        return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                    }else{
                        //check token if exist in database
                        if($jwtController->checkExistRefreshWebsiteNew(['token'=>$token3])){
                            $decodedRefresh = $jwtController->decode($decodeRefresh);
                            if($decodedRefresh['status'] == 'error'){
                                if($decodedRefresh['message'] == 'Expired token'){
                                    return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                                }else if($decodedRefresh['message'] == 'invalid email'){
                                    return redirect('/login')->withCookies([Cookie::forget('token1'),   Cookie::forget('token2'),Cookie::forget('token3')]);
                                }
                            //if token refresh success decoded and not expired
                            }else{
                                $decoded = $jwtController->decode($decode);
                                if($decoded['status'] == 'error'){
                                    if($decoded['message'] == 'Expired token'){
                                        $updated = $jwtController->updateTokenWebsite($decodedRefresh['data']['data']);
                                        if($updated['status'] == 'error'){
                                            return response()->json(['status'=>'error','message'=>'update token error'],500);
                                        }else{
                                            $request->request->add([$updated['data']]);
                                            $response = $next($request);
                                            $cookies = $response->headers->getCookies();
                                            foreach ($cookies as $cookie) {
                                                if ($cookie->getName() === 'token1') {
                                                    $response->cookie('token1',$token1,$cookie->getExpiresTime());
                                                }else if ($cookie->getName() === 'token3') {
                                                    $response->cookie('token3',$token3,$cookie->getExpiresTime());
                                                }
                                            }
                                            $response->cookie('token2', $updated['data'], time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED')));
                                            return $response;
                                        }
                                    }else{
                                        return response()->json(['status'=>'error','message'=>$decoded['message']],500);
                                    }
                                //if success decode
                                }else{
                                    $decoded['data'][0][0]['number'] = $number;
                                    if($request->path() === 'users/google' && $request->isMethod("get")){
                                        $data = [$decoded['data'][0][0]];
                                        $request->request->add($data);
                                        return response()->json($request->all());
                                    }
                                    return $next($request);
                                }
                            }
                        //if token is not exist in database
                        }else{
                            $delete = $jwtController->deleteRefreshWebsite($email,$number);
                            if($delete['status'] == 'error'){
                                return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                            }else{
                                return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                            }
                        }
                    }
                }
                return $next($request);
            }
        //if cookie gone
        }else{
            $page = ['/page/dashboard','/page/device','/users/pengaturan','/page/laporan','/page/edukasi'];
            if(in_array('/'.$request->path(),$page)){
                if($request->hasCookie("token1")){
                    $token1 = $request->cookie('token1');
                    $token1 = json_decode(base64_decode($token1),true);
                    $email = $token1['email'];
                    $number = $token1['number'];
                    $delete = $jwtController->deleteRefreshWebsite($email,$number);
                    if($delete['status'] == 'error'){
                        return response()->json(['status'=>'error','message'=>'delete token error'],500);
                    }else{
                        return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                    }
                }else{
                    return redirect('/login')->withCookies([Cookie::forget('token1'),Cookie::forget('token2'),Cookie::forget('token3')]);
                }
            }
            return $next($request); 
        }
    }
}
?>