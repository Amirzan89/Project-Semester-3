<?php
class ChangePasswordController{ 
    public function showCreatePass($data){
        try{
            $email = $data('email');
            $nama = $data['nama'];
            if(!isset($email) || empty($email)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak boleh kosong',   'code'=>400]));
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email yang anda masukkan invalid', 'code'=>400]));
            }else if(!isset($nama) || empty($nama)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Nama tidak boleh kosong',    'code'=>400]));
            }else{
            // $costum = new Request();
            // $costum->replace(['email'=>$decoded['data'][0][0]['email'],'mode'=>'count']);
            // $total = $deviceController->getDevice($costum,$device)->getData();
            // return view('page.dashboard',['total'=>$total->data, 'penuh'=>0, 'kosong'=>0,'email'=>$decoded['data']   [0][0]['email'],'nama'=>$decoded['data'][0][0]['nama'],'number'=>$number]);
            $data = [
                'email' => $email,
                'nama' => $nama,
                'div' => 'register',
                'title' => 'Lupa Password',
                'description' => 'changePass',
                'code' => '',
                'link' => ''
            ];
            
            extract($data);
            include('view/page/forgotPassword.php');
            exit();
            // return view('page.forgotPassword',['email'=>$email,'nama'=>$nama,'div'=>'register','title'=>'Lupa   Password','description'=>'changePass','code'=>'','link'=>'']);
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
    //register user using google
    public function showVerify($data){
        try{
            $email = $data['email'];
            $nama = $data['nama'];
            if(!isset($email) || empty($email)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Email tidak boleh kosong',   'code'=>400]));
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception(json_encode(['status'=>'error','message'=>'Email yang anda masukkan invalid', 'code'=>400]));
            }else if(!isset($nama) || empty($nama)){
                throw new Exception(json_encode(['status'=>'error','message'=>'Nama tidak boleh kosong',    'code'=>400]));
            }else{
                $data = [
                    'email' => $email,
                    'nama' => $nama,
                    'div' => 'register',
                    'title' => 'Lupa Password',
                    'description' => 'createUser',
                    'code' => '',
                    'link' => ''
                ];
                extract($data);
                include('view/page/forgotPassword.php');
                // return view('page.forgotPassword',['email'=>$email,'nama'=>$nama,'div'=>'verifyDiv','title'=>'Register Google','description'=>'createUser','code'=>'','link'=>'']);
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
}
?>