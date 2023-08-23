<?php
// namespace Controllers\Website;
require_once __DIR__.'/../Auth/JWTController.php';
class NotificationPageController
{ 
    public function showSuccessVerifyEmail($message, $data){
        $jwtController = new JwtController();
        $dataJwt = $jwtController->createJWTWebsite($data);
        if(is_null($dataJwt)){
            return ['status'=>'error','message'=>'create token error'];
        }else{
            if($dataJwt['status'] == 'error'){
                return ['status'=>'error','message'=>$dataJwt['message']];
            }else{
                $dataPage = [
                    'title'=>'Verify Email',
                    'message'=>$message,
                    'div'=>'green',
                    'code'=>200,
                    'div1'=>'dashboard'
                ];
                extract($dataPage);
                include('view/page/notification.php');
                $data1 = ['email'=>$data['email'],'number'=>$dataJwt['number'],'expire'=>time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED'])];
                $encoded = base64_encode(json_encode($data1));
                // header('Content-Type: application/json');
                setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                setcookie('token2', $dataJwt['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                setcookie('token3', $dataJwt['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                // return (['status'=>'success','message'=>'Login sukses silahkan masuk dashboard']);
                exit();
            }
        }
    }
    public function showFailVerifyEmail($message, $code = null){
        if(is_null($code) || empty($code)){
            $data = [
                'title'=>'Verify Email',
                'message'=>$message,
                'div'=>'red',
                'code'=>400,
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }else{
            $data = [
                'title'=>'Verify Email',
                'message'=>$message,
                'div'=>'red',
                'code'=>$code,
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }
    }
    public function showSuccessResetPass($message){
        $data = [
            'title'=>'Reset Password',
            'message'=>$message,
            'div'=>'green',
            'code'=>200,
            'div1'=>'dashboard'
        ];
        extract($data);
        include('view/page/notification.php');
        exit();
    }
    public function showFailResetPass($message, $code = null){
        if(is_null($code) || empty($code)){
            $data = [
                'title'=>'Reset Password',
                'message'=>$message,
                'div'=>'red',
                'code'=>400
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }else{
            $data = [
                'title'=>'Reset Password',
                'message'=>$message,
                'div'=>'red',
                'code'=>$code
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }
    }
    public function showRandom($message, $code){
        if($code == 200){
            $data = [
                'title'=>'Verify Email',
                'message'=>$message,
                'div'=>'green',
                'code'=>200
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }else{
            $data = [
                'title'=>'Verify Email',
                'message'=>$message,
                'div'=>'red',
                'code'=>400
            ];
            extract($data);
            include('view/page/notification.php');
            exit();
        }
    }
}
?>

