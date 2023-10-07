<?php
require_once("koneksi.php");
class Jwt{ 
    private static $database;
    private static $con;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
    }
    private static function loadEnv($path = null){
        if($path == null){
            $path = ".env";
        }
        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                    $_SERVER[trim($key)] = trim($value);
                }
            }
        }
    }
    private static function base64url_encode($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
    private static function generate_jwt($headers, $payload, $secret) {
        $headers_encoded = self::base64url_encode(json_encode($headers));
        $payload_encoded = self::base64url_encode(json_encode($payload));
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = self::base64url_encode($signature);
        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        return $jwt;
    }
    public function checkExistRefreshWebsiteNew($data,$con){
        $token = $data['token'];
        if(empty($token) || is_null($token)){
            return ['status'=>'error','message'=>'token empty'];
        }else{
            $query = "SELECT number FROM refresh_token WHERE BINARY token = ? AND device = 'website' LIMIT 1";
            $stmt = $con->prepare($query);
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $result = '';
            $stmt->bind_result($result);
            if ($stmt->fetch()) {
                $stmt->close();
                return true;
            }else{
                $stmt->close();
                return false;
            }
            // return RefreshToken::select("email")->whereRaw("BINARY token = ? AND device = 'website'",[$token])->limit(1)->exists();
        }
    }
    public static function decode_and_validate_jwt($data) {
        $jwt = $data['token'];
        $opt = $data['opt'];
        $email = $data['email'];
        self::loadEnv();
        if($opt == 'refresh'){
            $secret = $_SERVER['JWT_SECRET_REFRESH_TOKEN'];
        }else if($opt == 'token'){
            $secret = $_SERVER['JWT_SECRET'];
        }
        // Split the JWT
        $tokenParts = explode('.', $jwt);
        if (count($tokenParts) !== 3) {
            return false; // Invalid JWT format
        }
        // Decode the header and payload
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);

        // Check the expiration time - note this will cause an error if there is no 'exp' claim in the JWT
        $expiration = json_decode($payload)->exp;
        if ($expiration < time()) {
            return ['status'=>'error','message'=>'Expired token'];
        }
        // Build a signature based on the header and payload using the secret
        $base64UrlHeader = self::base64url_encode($header);
        $base64UrlPayload = self::base64url_encode($payload);
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64url_encode($signature);
        
        // Verify it matches the signature provided in the JWT
        if ($base64UrlSignature !== $tokenParts[2]) {
            return ['status'=>'error','message'=>'invalid signature'];
        }
        $payload = json_decode($payload,true);
        if(isset($payload['email'])){
            if($email == $payload['email']){
                return ['status'=>'success','data'=>$payload];
            }else{
                return ['status'=>'error','message'=>'invalid email'];
            }
        }else{
            return ['status'=>'error','message'=>'invalid email'];
        }
    }
    public function updateTokenWebsite($data){
        try{
            if(empty($data) || is_null($data)){
                return ['status'=>'error','message'=>'data empty'];
            }else{
                // $payload = ['data'=>$data, 'exp'=>self::$exp];
                $now1 = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                $now1 = $now1->format('Y-m-d H:i:s');
                $headers = array('alg'=>'HS256','typ'=>'JWT');
                $key = $_SERVER['JWT_SECRET'];
                $exp = (time()+intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                $data['exp'] = $exp;
                $token = self::generate_jwt($headers, $data, $key);
                // echo 'token '.$token;
                return ['status'=>'success','data'=>$token];
            }
        }catch(UnexpectedValueException $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    private static function checkTotalLoginWebsite($data,$con){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty'];
            }else{
                $query = "SELECT COUNT(*) AS total FROM refresh_token WHERE BINARY email = ? AND device = 'website'";
                $stmt = $con->prepare($query);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = '';
                $stmt->bind_result($result);
                if ($stmt->fetch()) {
                    $stmt->close();
                    if(is_null($result) || empty($result) || $result <= 0){
                        return ['status'=>'success','data'=>0];
                    }else{
                        return ['status'=>'success','data'=>$result];
                    }
                }else{
                    return ['status'=>'error','message'=>'belum login','data'=>0];
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
            echo "<script>alert('$responseData')</script>";
            exit();
        }
    }
    public function deleteRefreshWebsite($email,$number = null){
        try{
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty','code'=>400];
            }else{
                if($number == null){
                    $query = "DELETE FROM refresh_token WHERE BINARY email = ? AND device = 'website'";
                    $stmt = self::$con->prepare($query);
                    $stmt->bind_param('s', $email);
                    if ($stmt->execute()) {
                        $stmt->close();
                        return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
                    }else{
                        $stmt->close();
                        return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
                    }
                }else{
                    $query = "DELETE FROM refresh_token WHERE BINARY email = ? AND device = 'website' AND number = ?";
                    $stmt = self::$con->prepare($query);
                    $stmt->bind_param('si', $email, $number);
                    if ($stmt->execute()) {
                        $stmt->close();
                        return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
                    }else{
                        $stmt->close();
                        return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
                    }
                    // $deleted = DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND number = $number AND device = 'website'")->delete();
                    // if($deleted){
                    //     return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
                    // }else{
                    //     return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
                    // }
                }
            }
        }catch(Exception $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
    public static function createToken($data,$con, $loadEnv = null){
        try{
            $emailInput = $data['email'];
            if(empty($emailInput) || is_null($emailInput)){
                return ['status'=>'error','message'=>'email empty'];
            }else{
                //check email is exist on database
                $userColumns = ['id_user','nama_lengkap','no_telpon','jenis_kelamin','tanggal_lahir','tempat_lahir','email','role'];
                $columns = implode(',', $userColumns);
                $query = "SELECT $columns FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = $con->prepare($query);
                $stmt[0]->bind_param('s', $emailInput);
                $stmt[0]->execute();
                $bindResultArray = [];
                foreach ($userColumns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt[0], 'bind_result'], $bindResultArray);
                $resultDb = [];
                if ($stmt[0]->fetch()) {
                    if(!is_null($loadEnv)){
                        $loadEnv();
                    }
                    $headers = array('alg'=>'HS256','typ'=>'JWT');
                    foreach($userColumns as $column) {
                        $resultDb[$column] = $$column;
                    }
                    $stmt[0]->close();
                    $now1 = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                    $now1 = $now1->format('Y-m-d H:i:s');
                    //check total login on website
                    $number = self::checkTotalLoginWebsite(['email'=>$emailInput],$con);
                    $device = 'website';
                    if($number['status'] == 'error'){
                        return $number;
                    }else{
                        $key = $_SERVER['JWT_SECRET'];
                        $Rkey = $_SERVER['JWT_SECRET_REFRESH_TOKEN'];
                        $exp = (time()+intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']));
                        $Rexp = (time()+intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']));
                        //get id_user
                        $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                        $stmt[0] = $con->prepare($query);
                        $stmt[0]->bind_param('s', $data['email']);
                        $stmt[0]->execute();
                        $idUser = '';
                        $stmt[0]->bind_result($idUser);
                        $stmt[0]->fetch();
                        $stmt[0]->close();
                        if($number['data'] >= 3){
                            $query = "DELETE FROM refresh_token WHERE BINARY email = ? AND device = 'website' AND number = 1";
                            $stmt[1] = $con->prepare($query);
                            $stmt[1]->bind_param('s', $emailInput);
                            if ($stmt[1]->execute()) {
                                $stmt[1]->close();
                                $query = "UPDATE refresh_token SET number = number - 1 WHERE BINARY email = ? AND device = 'website' AND number BETWEEN 1 AND 3";
                                $stmt[2] = $con->prepare($query);
                                $stmt[2]->bind_param('s', $emailInput);
                                $stmt[2]->execute();
                                $stmt[2]->close();
                                $query = "INSERT INTO refresh_token (email,token, device, number, created_at, updated_at,id_user) VALUES (?, ?, ?, ?, ?, ?, ?)";
                                $stmt[3] = $con->prepare($query);
                                $number['data'] = 3;
                                $resultDb['number'] = 3;
                                $resultDb['exp'] = $exp;
                                $token = self::generate_jwt($headers, $resultDb, $key);
                                $resultDb['exp'] = $Rexp;
                                $Rtoken = self::generate_jwt($headers, $resultDb,$Rkey);
                                $stmt[3]->bind_param("sssisss", $emailInput, $Rtoken,$device, $number['data'], $now1, $now1,$idUser);
                                $stmt[3]->execute();
                                if ($stmt[3]->affected_rows > 0) {
                                    $stmt[3]->close();
                                    return [
                                        'status'=>'success',
                                        'data'=>
                                        [
                                            'token'=>$token,
                                            'refresh'=>$Rtoken,
                                        ]];
                                }else{
                                    $stmt[3]->close();
                                    return ['status'=>'error','message'=>'error saving token','code'=>500];
                                }
                            }else{
                                $stmt[1]->close();
                                return ['status'=>'error','message'=>'error delete old refresh token','code'=>500];
                            }
                        //if user has not login 
                        }else{
                            $number = self::checkTotalLoginWebsite(['email'=>$emailInput],$con);
                            if($number['status'] == 'error'){
                                $number['data'] = 1;
                                $resultDb['number'] = 1;
                                $resultDb['exp'] = (time()+$exp);
                                $token = self::generate_jwt($headers, $resultDb, $key);
                                $resultDb['exp'] = (time()+$Rexp);
                                $Rtoken = self::generate_jwt($headers, $resultDb,$Rkey);
                                $json = [
                                    'status'=>'success',
                                    'data'=>
                                    [
                                        'token'=>$token,
                                        'refresh'=>$Rtoken,
                                    ]
                                ];
                            }else{
                                $number['data']+= 1;
                                $resultDb['number'] = $number['data'];
                                $resultDb['exp'] = (time()+$exp);
                                $token = self::generate_jwt($headers, $resultDb, $key);
                                $resultDb['exp'] = (time()+$Rexp);
                                $Rtoken = self::generate_jwt($headers, $resultDb,$Rkey);
                                $json = [
                                    'status'=>'success',
                                    'data'=>
                                    [
                                        'token'=>$token,
                                        'refresh'=>$Rtoken,
                                    ]
                                ];
                            }
                            $query = "INSERT INTO refresh_token (email,token, device, number, created_at, updated_at,id_user) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $stmt[1] = $con->prepare($query);
                            $stmt[1]->bind_param("sssisss", $emailInput, $Rtoken, $device, $number['data'], $now1, $now1,$idUser);
                            $stmt[1]->execute();
                            if ($stmt[1]->affected_rows > 0) {
                                $stmt[1]->close();
                                return $json;
                            }else{
                                $stmt[1]->close();
                                return ['status'=>'error','message'=>'Error saving token','code'=>500];
                            }
                        }
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'Email tidak ditemukan'];
                }
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
// $headers = array('alg'=>'HS256','typ'=>'JWT');
// $payload = array('sub'=>'1234567890','name'=>'John Doe', 'admin'=>true, 'exp'=>(time() + 60));
// $jwt = generate_jwt($headers, $payload);
?>