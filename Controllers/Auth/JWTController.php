<?php
// namespace Controllers\Auth;
use Database\Database;
use Illuminate\Support\Facades\Hash;
// use Firebase\JWT\JWT;
// use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Builder as JwtBuilder;
use Lcobucci\JWT\Signer\Key;
use lcobucci\JWT\Signer\Hmac\Sha512;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\jwt\Validation\Validator;
// use Lcobucci\JWT\Signer\Hmac\Sha512;
// use Firebase\JWT\Key;
use Carbon\Carbon;
class JwtController{
    private static $database;
    private static $con;
    private static $userColumns;
    private static $exp; 
    private static $expRefresh;
    private static $secretKey;
    private static $secretKeyMobile;
    private static $secretRefreshKey;
    private static $secretRefreshKeyMobile;
    private static $key;
    private static $keyRefresh; 
    private static $algorithm;
    public function __construct(){
        self::$userColumns = ['id_user','email', 'password','nama','email_verified','level','created_at','updated_at'];
        self::$database = Database::getInstance();
        self::$con = self::$database->getConnection();
        self::$exp = intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']);
        self::$expRefresh = intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']);
        self::$secretKey = InMemory::plainText($_SERVER['JWT_SECRET']);
        self::$secretRefreshKey = InMemory::plainText($_SERVER['JWT_SECRET_REFRESH_TOKEN']);
        self::$secretKeyMobile = InMemory::plainText($_SERVER['JWT_SECRET_ANDROID']);
        self::$secretRefreshKeyMobile = InMemory::plainText($_SERVER['JWT_SECRET_REFRESH_TOKEN_ANDROID']);
        self::$algorithm = new Sha256();
        // self::$key = new Key(self::$secretKey,'HS512');
        // self::$keyRefresh = new Key(self::$secretKey,'HS512');
    }
    //cek jumlah login di database
    public function checkTotalLoginWebsite($data){
        try{
        // $data = $data['request'];
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email empty'];
        }else{
            $query = "SELECT COUNT(*) AS total FROM refresh_token WHERE BINARY email = ? AND device = 'website'";
            $stmt = self::$con->prepare($query);
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
                return $responseData;
            }
        }
    //cek jumlah login di database
    public function checkTotalLoginMobile($data){
        $data = $data['request'];
        $email = $data['email'];
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email empty'];
        }else{
            $query = "SELECT COUNT(*) AS total FROM refresh_token WHERE BINARY email = ? AND device = 'mobile'";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = '';
            $stmt->bind_result($result);
            if ($stmt->fetch()) {
                $stmt->close();
                if(is_null($result) || empty($result) || $result <= 0){
                    return ['status'=>'error','message'=>'email empty'];
                }else{
                    return ['status'=>'success','data'=>$result];
                }
            }else{
                return ['status'=>'error','message'=>'belum login'];
            }
        }
    }
    //check token in database is exist 
    public function checkExistRefreshWebsite($data){
        // $data = $data['request'];
        $email = $data['email'];
        $number = $data['number'];
        $token = $data['token'];
        if(!empty($token) || !is_null($token)){
            $query = "SELECT COUNT(*) AS total FROM refresh_token WHERE BINARY token = ? AND device = 'website'";
            $stmt = self::$con->prepare($query);
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
        }else{
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty'];
            }else if(empty($number) || is_null($number)){
                return ['status'=>'error','message'=>'number empty'];
            }else{
                $query = "SELECT COUNT(*) AS total FROM refresh_token WHERE BINARY email = ? AND number = ? AND device = 'website'";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param('si', $email,$number);
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
            }
        }
    }
    public function checkExistRefreshWebsiteNew($data){
        // $data = $data['request'];
        $token = $data['token'];
        if(empty($token) || is_null($token)){
            return ['status'=>'error','message'=>'token empty'];
        }else{
            $query = "SELECT number FROM refresh_token WHERE BINARY token = ? AND device = 'website' LIMIT 1";
            $stmt = self::$con->prepare($query);
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
    //get refresh token from database
    public function getRefreshWebsite($data){
        try{
            $data = $data['request'];
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email tidak boleh kosong'];
            }else{
                $query = "SELECT token FROM refresh_token WHERE BINARY email = ? AND number = ? AND device = 'website'";
                $stmt = self::$con->prepare($query);
                $number = 1;
                $stmt->bind_param('si', $email,$number);
                $stmt->execute();
                $token = '';
                $stmt->bind_result($token);
                if ($stmt->fetch()) {
                    $stmt->close();
                    header('Content-Type: application/json');
                    return $token;
                }else{
                    return ['status'=>'error','message'=>'Email tidak ditemukan'];
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
            return $responseData;
        }
    }

    //save token refresh to database 
    public function saveRefreshWebsite($data, $opt){
        try{
            $data = $data['request'];
            $email = $data['email'];
            $token = $data['refresh_token'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'Email kosong'];
            }
            if(empty($token) || is_null($token)){
                return ['status'=>'error','message'=>'token kosong'];
            }
            if($opt == 'website'){
                $query = "INSERT INTO refresh_token (email,token, device, number, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
                $number = false;
                $stmt = self::$con->prepare($query);
                $now = Carbon::now('Asia/Jakarta');
                $device = 'website';
                $stmt->bind_param("sssbss", $email,$token, $device,$number, $now, $now);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    return ['status'=>'success','message'=>'saving token success website'];
                }else{
                    return ['status'=>'error','message'=>'error saving token','code'=>500];
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
            return $responseData;
        }
    }
    
    //create token and refresh token 
    public function createJWTWebsite($data, $uri = null){
        try{
            $email = $data['email'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty'];
            }else{
                //check email is exist on database
                $query = "SELECT * FROM users WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $bindResultArray = [];
                foreach (self::$userColumns as $column) {
                    $bindResultArray[] = &$$column;
                }
                call_user_func_array([$stmt[0], 'bind_result'], $bindResultArray);
                $resultDb = [];
                if ($stmt[0]->fetch()) {
                    foreach (self::$userColumns as $column) {
                        $resultDb[$column] = $$column;
                    }
                    $stmt[0]->close();
                    $now1 = Carbon::now('Asia/Jakarta');
                    $now   = new DateTimeImmutable();
                    //check total login on website
                    $number = $this->checkTotalLoginWebsite(['email'=>$email]);
                    // var_dump($number);
                    $device = 'website';
                    if($number['status'] == 'error'){
                        return $number;
                    }else{
                        if($number['data'] >= 3){
                            $payloadRefresh = [ 'data'=>$resultDb, 'exp'=>self::$expRefresh];
                            $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                            $Rtoken = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://localhost')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$expRefresh ." seconds"))->withClaim('data', $payloadRefresh)->getToken(self::$algorithm, self::$secretRefreshKey)->toString();
                            $query = "DELETE FROM refresh_token WHERE BINARY email = ? AND device = 'website' AND number = 1";
                            $stmt[1] = self::$con->prepare($query);
                            $stmt[1]->bind_param('s', $email);
                            $result = '';
                            $stmt[1]->execute();
                            if ($stmt[1]->execute()) {
                                $stmt[1]->close();
                                $query = "UPDATE refresh_token SET number = number - 1 WHERE BINARY email = ? AND device = 'website' AND number BETWEEN 1 AND 3";
                                $stmt[2] = self::$con->prepare($query);
                                $stmt[2]->bind_param('s', $email);
                                $result = '';
                                $stmt[2]->execute();
                                $stmt[2]->close();
                                $payload = ['data'=>$resultDb, 'number'=> 3, 'exp'=>self::$exp];
                                $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                                $token = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://localhost')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$exp ." seconds"))->withClaim('data', $payload)->getToken(self::$algorithm, self::$secretKey)->toString();
                                $query = "INSERT INTO refresh_token (email,token, device, number, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
                                $stmt[3] = self::$con->prepare($query);
                                $number['data'] = 3;
                                $stmt[3]->bind_param("sssiss", $email, $Rtoken,$device, $number['data'], $now1, $now1);
                                $stmt[3]->execute();
                                if ($stmt[3]->affected_rows > 0) {
                                    $stmt[3]->close();
                                    return [
                                        'status'=>'success',
                                        'data'=>
                                        [
                                            'token'=>$token,
                                            'refresh'=>$Rtoken
                                        ],
                                        'number'=>3];
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
                            $payloadRefresh = [ 'data'=>$resultDb, 'exp'=>self::$expRefresh];
                            $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                            $Rtoken = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://localhost')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$expRefresh ." seconds"))->withClaim('data', $payloadRefresh)->getToken(self::$algorithm, self::$secretRefreshKey)->toString();
                            $number = $this->checkTotalLoginWebsite(['email'=>$email]);
                            if($number['status'] == 'error'){
                                $number['data'] = 1;
                                $payload = [ 'data'=>$resultDb, 'number'=> 1,'exp'=>self::$exp];
                                $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                                $token = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://localhost')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$exp ." seconds"))->withClaim('data', $payload)->getToken(self::$algorithm, self::$secretKey)->toString();
                                $json = [
                                    'status'=>'success',
                                    'data'=>
                                    [
                                        'token'=>$token,
                                        'refresh'=>$Rtoken
                                    ],
                                    'number' => 1];
                            }else{
                                echo '';
                                $payload = [ 'data'=>$resultDb, 'number'=> $number['data']+1,'exp'=>self::$exp];
                                $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                                $token = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://localhost')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$exp ." seconds"))->withClaim('data', $payload)->getToken(self::$algorithm, self::$secretKey)->toString();
                                $number['data']+= 1;
                                $json = [
                                    'status'=>'success',
                                    'data'=>
                                    [
                                        'token'=>$token,
                                        'refresh'=>$Rtoken,
                                    ],
                                    'number' => $number['data']];
                            }
                            $query = "INSERT INTO refresh_token (email,token, device, number, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt[1] = self::$con->prepare($query);
                            $stmt[1]->bind_param("sssiss", $email, $Rtoken, $device, $number['data'], $now1, $now1);
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
            echo $e->getTraceAsString();
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
    //decode token
    public function decode($data){
        try{
            $email = $data['email'];
            $tokenValue = $data['token'];
            $opt = $data['opt'];
            if(empty($email) || is_null($email)){
                return ['status'=>'error','message'=>'email empty'];
            }else if(empty($tokenValue) || is_null($tokenValue)){
                return ['status'=>'error','message'=>'token empty'];
            }else if(empty($opt) || is_null($opt)){
                return ['status'=>'error','message'=>'option empty'];
            }else{
                $token = (new Parser(new JoseEncoder()))->parse($tokenValue);
                if($opt == 'token'){
                    if ($token->isExpired(new DateTimeImmutable())) {
                        return ['status'=>'error','message'=>'Expired token'];
                    }
                    $decoded = $token->claims()->get('data');
                    if(isset($decoded['data']['email'])){
                        if($email == $decoded['data']['email']){
                            return ['status'=>'success','data'=>$decoded, true];
                        }else{
                            return ['status'=>'error','message'=>'invalid email'];
                        }
                    }else{
                        return ['status'=>'error','message'=>'invalid email'];
                    }
                }else if($opt == 'refresh'){
                    if ($token->isExpired(new DateTimeImmutable())) {
                        return ['status'=>'error','message'=>'Expired token'];
                    }
                    $decoded = $token->claims()->get('data');
                    if(isset($decoded['data']['email'])){
                        if($email == $decoded['data']['email']){
                            return ['status'=>'success','data'=>$decoded, true];
                        }else{
                            return ['status'=>'error','message'=>'invalid email'];
                        }
                    }else{
                        return ['status'=>'error','message'=>'invalid email'];
                    }
                    // exit();
                }else{
                    return ['status'=>'error','message'=>'invalid data'];
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
            return $responseData;
        }
    }
    public function updateTokenWebsite($data){
        try{
            if(empty($data) || is_null($data)){
                return ['status'=>'error','message'=>'data empty'];
            }else{
                $payload = ['data'=>$data, 'exp'=>self::$exp];
                $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
                $now   = new DateTimeImmutable();
                $token = $tokenBuilder->issuedBy('http://localhost')->permittedFor('http://example.org')->issuedAt($now)->canOnlyBeUsedAfter($now)->expiresAt($now->modify("+". self::$exp ." seconds"))->withClaim('data', $payload)->getToken(self::$algorithm, self::$secretKey)->toString();
                // $token = JWT::encode($payload, $secretKey,'HS512');
                return ['status'=>'success','data'=>$token];
            }
        }catch(UnexpectedValueException $e){
            return ['status'=>'error','message'=>$e->getMessage()];
        }
    }
//     //update refresh token website
//     public function updateRefreshWebsite($email){
//         try{
//             // $email = $request->input('email');
//             if(empty($email) || is_null($email)){
//                 return ['status'=>'error','message'=>'email adios'];
//             }else{
//                 $dataDb = User::select()->whereRaw("BINARY email LIKE '%".$email."%'")->limit(1)->get();
//                 $data = json_decode(json_encode($dataDb));
//                 $expRefresh = time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED'));
//                 $payloadRefresh = [ $data, 'exp'=>$expRefresh];
//                 $secretRefreshKey = env('JWT_SECRET_REFRESH_TOKEN');
//                 $token = JWT::encode($payloadRefresh, $secretRefreshKey, 'HS512');
//                 if(is_null(DB::table('refresh_token')->whereRaw("BINARY email LIKE '%".$email."%'")->update(['token'=>$token, 'updated_at' => Carbon::now()]))){
//                     return ['status'=>'error','message'=>'error update refresh token'];
//                 }else{
//                     return ['status'=>'success','message'=>'success update refresh token'];
//                 }
//             }
//         }catch(\Exception $e){
//             return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
//         }
//     }

//     //change refresh token website
//     public function changeRefreshWebsite($email){
//         // try{
//         //     if(empty($email) || is_null($email)){
//         //         return response()->json('email empty',404);
//         //     }else{
//         //         $deleted = DB::table('refresh_token')->where('email', $email)->delete();
//         //         if($deleted){
//         //             return ['status'=>'success','message'=>'success change refresh token'];
//         //         }else{
//         //             return ['status'=>'error','message'=>'failed change refresh token'];
//         //         }
//         //     }
//         // }catch(\Exception $e){
//         //     return ['status'=>'error','message'=>$e->getMessage()];
//         // }
//     }
    //delete refresh token website 
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
                        return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
                    }else{
                        return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
                    }
                }else{
                    $query = "DELETE FROM refresh_token WHERE BINARY email = ? AND device = 'website' AND number = ?";
                    $stmt = self::$con->prepare($query);
                    $stmt->bind_param('si', $email, $number);
                    if ($stmt->execute()) {
                        return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
                    }else{
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
}
?>