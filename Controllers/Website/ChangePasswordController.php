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
    public function showRegisterGoogle($data){
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
                    'div' => 'verifyDiv',
                    'title' => 'Buat Password',
                    'description' => 'createUser',
                    'code' => '',
                    'link' => ''
                ];
                extract($data);
                include('view/page/forgotPassword.php');
                exit();
            }
        }catch(Exception $e){
            // echo $e->getTraceAsString();
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