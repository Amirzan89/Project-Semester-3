<?php
namespace App\Http\Controllers\Website;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
class NotificationPageController
{ 
    public function showSuccessVerifyEmail($message){
        $data = [
            'title'=>'Verify Email',
            'message'=>$message,
            'div'=>'green',
            'code'=>200,
            'div1'=>'dashboard'
        ];
        extract($data);
        include('view/page/notification.php');
        exit();
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

