<?php
namespace Controllers\Auth;
// // use App\Http\Controllers\Controller;
// // use App\Models\RefreshToken;
// // use App\Models\User;
// // use Illuminate\Http\Request;
// // use Illuminate\Http\Response;
// // use Tymon\JWTAuth\Facades\JWTAuth;
// // use Illuminate\Support\Facades\DB;
// // use Illuminate\Support\Facades\Hash;
// // use Firebase\JWT\JWT;
// // use Firebase\JWT\Key;
// // use Firebase\JWT\SignatureInvalidException;
// // use Firebase\JWT\BeforeValidException;
// // use Firebase\JWT\ExpiredException;
// // use UnexpectedValueException;
// // use Firebase\JWT\JWT;
// // use Firebase\JWT\SignatureInvalidException;
// // use Firebase\JWT\BeforeValidException;
// // use Firebase\JWT\ExpiredException;
// // use DomainException;
// // use InvalidArgumentException;
// // use UnexpectedValueException;
// // use Carbon\Carbon;
// // Use Closure;
class JwtController{
//     //cek jumlah login di database
//     // public function checkTotalLoginWebsite($data){
//         //     $email = $data['email'];
//         //     if(empty($email) || is_null($email)){
//     //         return ['status'=>'error','message'=>'email empty'];
//     //     }else{
//     //         if(RefreshToken::select("email")->whereRaw("BINARY email LIKE '%$email%' AND device = 'website'")->limit(1)->exists()){
//         //             $Iresult = RefreshToken::whereRaw("BINARY email LIKE '%$email%' AND device = 'website'")->count();
//     //             $result = json_decode(json_encode($Iresult));
//     //             // return response()->json('hasiull '.$result);
//     //             if(is_null($result) || empty($result) || $result <= 0){
//     //                 return ['status'=>'success','data'=>0];
//     //             }else{
//     //                 return ['status'=>'success','data'=>$result];
//     //             }
//     //         }else{
//         //             return ['status'=>'error','message'=>'belum login','data'=>0];
//         //         }
//         //     }
//         // }
//         //cek jumlah login di database
//     // public function checkTotalLoginMobile($data){
//     //     $email = $data['email'];
//     //     if(empty($email) || is_null($email)){
//     //         return ['status'=>'error','message'=>'email empty'];
//     //     }else{
//     //         if(RefreshToken::select("email")->whereRaw("BINARY email LIKE '%$email%' AND device = 'mobile'")->limit(1)->exists()){
//     //             $Iresult = RefreshToken::whereRaw("BINARY email LIKE '%$email%' AND device = 'mobile'")->count();
//     //             $result = json_decode(json_encode($Iresult));
//     //             if(is_null($result) || empty($result) || $result <= 0){
//     //                 return ['status'=>'error','message'=>'email empty'];
//     //             }else{
//     //                 return ['status'=>'success','data'=>$result];
//     //             }
//     //         }else{
//     //             return ['status'=>'error','message'=>'belum login'];
//     //         }
//     //     }
//     // }
//     //check token in database is exist 
//     public function checkExistRefreshWebsite($data){
//         $email = $data['email'];
//         $number = $data['number'];
//         if(empty($email) || is_null($email)){
//             return ['status'=>'error','message'=>'email empty'];
//         }else if(empty($number) || is_null($number)){
//             return ['status'=>'error','message'=>'token empty'];
//         }else{
//             // return response()->json(['status'=>'errorrr','data'=>RefreshToken::select("email")->whereRaw("BINARY email LIKE '%$email%' AND device = 'website' AND number = $number")->limit(1)->exists()]);
//             return RefreshToken::select("email")->whereRaw("BINARY email LIKE '%$email%' AND device = 'website' AND number = $number")->limit(1)->exists();
//         }
//     }
//     public function checkExistRefreshWebsiteNew($data){
//         $token = $data['token'];
//         // $number = $data['number'];
//         if(empty($token) || is_null($token)){
//             return ['status'=>'error','message'=>'email empty'];
//         }else{
//             // return response()->json(['status'=>'errorrr','data'=>RefreshToken::select("email")->whereRaw("BINARY email LIKE '%$email%' AND device = 'website' AND number = $number")->limit(1)->exists()]);
//             return RefreshToken::select("email")->whereRaw("BINARY token LIKE '%$token%' AND device = 'website'")->limit(1)->exists();
//         }
//     }

//     //check total refresh token website
//     public function checkTotalRefreshWebsite($data){
//         $email = $data['email'];
//         if(empty($email) || is_null($email)){
//             return ['status'=>'error','message'=>'email empty'];
//         }else{
//             if(RefreshToken::select("email")->where('email','LIKE','%'.$email.'%')->limit(1)->exists()){
//                 if(RefreshToken::select("email")->where('email','LIKE','%'.$email.'%')->limit(1)->exists()){
//                 }else{
//                     //
//                 }
//                 return ['status'=>'success','data'=>true];
//             }else{
//                 return ['status'=>'success','data'=>false];
//             }
//         }
//     }
//     //get refresh token from database
//     public function getRefreshWebsite(Request $request,Response $response){
//         $email = $request->input('email');
//         if(empty($email) || is_null($email)){
//             return response()->json('email empty',404);
//         }else{
//             $Itoken = RefreshToken::select('refresh_token')->where('email','=',$email)->limit(1)->get();
//             $token = json_decode(json_encode($Itoken));
//             if(is_null($token)){
//                 return response()->json('email not found',404);
//             }else{
//                 return response()->json($token);
//             }
//         }
//     }

//     //save token refresh to database 
//     public function saveRefreshWebsite(Request $request, RefreshToken $refreshToken){
//         $email = $request->input('email');
//         $token = $request->input('refresh_token');
//         if(empty($email) || is_null($email)){
//             return response()->json('email empty',404);
//         }
//         if(empty($token) || is_null($token)){
//             return response()->json('token empty',404);
//         }
//         $refreshToken->email = $email;
//         $refreshToken->token= $token;
//         if($refreshToken->save()){
//             return response()->json('saving token success1');
//         }else{
//             return response()->json('error saving token1',401);
//         }
//     }
    
//     //create token and refresh token 
//     public function createJWTWebsite($email, RefreshToken $refreshToken){
//         try{
//             // $email = $request->input('email');
//             if(empty($email) || is_null($email)){
//                 return ['status'=>'error','message'=>'email empty'];
//             }else{
//                 //check email is exist on database
//                 if(User::select("email")->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->exists()){
//                     //check total login on website
//                     // return $this->checkTotalLoginWebsite(['email'=>$email]);
//                     $number = $this->checkTotalLoginWebsite(['email'=>$email]);
//                     if($number['data'] >= 3){
//                         $dataDb = User::select()->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->get();
//                         $data = json_decode(json_encode($dataDb));
//                         $exp = time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED'));
//                         $expRefresh = time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED'));
//                         $payloadRefresh = [ 'data'=>$data, 'exp'=>$expRefresh];
//                         $secretKey = env('JWT_SECRET');
//                         $secretRefreshKey = env('JWT_SECRET_REFRESH_TOKEN');
//                         $Rtoken = JWT::encode($payloadRefresh, $secretRefreshKey, 'HS512');
//                         if(DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND device = 'website' AND number = 1")->delete()){
//                             for($i = 1; $i <= 3; $i++){
//                                 DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND device = 'website' AND number = $i")->update(['number'=>$i-1]);
//                             }
//                             $payload = ['data'=>$data, 'number'=> 3, 'exp'=>$exp];
//                             $token = JWT::encode($payload, $secretKey,'HS512');
//                             $refreshToken->email = $email;
//                             $refreshToken->token = $Rtoken;
//                             $refreshToken->device = 'website';
//                             $refreshToken->number = 3;
//                             if($refreshToken->save()){
//                                 return [
//                                     'status'=>'success',
//                                         'data'=>
//                                         [
//                                             'token'=>json_decode(json_encode($token),true),
//                                             'refresh'=>json_decode(json_encode($Rtoken),true)
//                                         ],
//                                         'number'=>3];
//                             }else{
//                                 return ['status'=>'error','message'=>'error saving token','code'=>500];
//                             }
//                         }else{
//                             return ['status'=>'error','message'=>'error delete old refresh token', 'code'=>500];
//                         }
//                     //if user has not login 
//                     }else{
//                         $dataDb = User::select()->whereRaw("BINARY email LIKE '%$email%'")->limit(1)->get();
//                         $data = json_decode(json_encode($dataDb));
//                         $exp = time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED'));
//                         $expRefresh = time() + intval(env('JWT_REFRESH_TOKEN_EXPIRED'));
//                         $payloadRefresh = [ 'data'=>$data, 'exp'=>$expRefresh];
//                         $secretKey = env('JWT_SECRET');
//                         $secretRefreshKey = env('JWT_SECRET_REFRESH_TOKEN');
//                         $Rtoken = JWT::encode($payloadRefresh, $secretRefreshKey, 'HS512');
//                         $refreshToken->email = $email;
//                         $refreshToken->token = $Rtoken;
//                         $refreshToken->device = 'website';
//                         $number = $this->checkTotalLoginWebsite(['email'=>$email]);
//                         if($number['status'] == 'error'){
//                             $refreshToken->number = 1;
//                             $payload = [ 'data'=>$data, 'number'=> 1,'exp'=>$exp];
//                             $token = JWT::encode($payload, $secretKey,'HS512');
//                             $json = [
//                                 'status'=>'success',
//                                 'data'=>
//                                 [
//                                     'token'=>json_decode(json_encode($token),true),
//                                     'refresh'=>json_decode(json_encode($Rtoken),true)
//                                 ],
//                                 'number' => 1];
//                             }else{
//                                 $payload = [ 'data'=>$data, 'number'=> $number['data']+1,'exp'=>$exp];
//                                 $token = JWT::encode($payload, $secretKey,'HS512');
//                                 $refreshToken->number = $number['data']+1;
//                                 $json = [
//                                     'status'=>'success',
//                                     'data'=>
//                                     [
//                                         'token'=>json_decode(json_encode($token),true),
//                                         'refresh'=>json_decode(json_encode($Rtoken),true)
//                                     ],
//                                     'number' => $number['data']+1];
//                             // $json = ['status'=>'success','data'=>json_decode(json_encode($token),true),'number'=>$number['data']+1];
//                         }
//                         if($refreshToken->save()){
//                             return $json;
//                         }else{
//                             return ['status'=>'error','message'=>'error saving token','code'=>500];
//                         }
//                     }
//                 }else{
//                     return ['status'=>'error','messsage'=>'email not found','code'=>400];
//                 }
//             }
//         }catch(UnexpectedValueException  $e){
//             return ['status'=>'error','message'=>$e->getMessage()];
//         }
//     }
//     //decode token
//     public function decode($data){
//         try{
//             $email = $data['email'];
//             $token = $data['token'];
//             $opt = $data['opt'];
//             if(empty($email) || is_null($email)){
//                 return ['status'=>'error','message'=>'email empty'];
//             }else if(empty($token) || is_null($token)){
//                 return ['status'=>'error','message'=>'token empty'];
//             }else if(empty($opt) || is_null($opt)){
//                 return ['status'=>'error','message'=>'option empty'];
//             }else{
//                 if($opt == 'token'){
//                     $decode = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS512'));
//                     $decoded = json_decode(json_encode($decode), true);
//                     return ['status'=>'success','data'=>json_decode(json_encode($decode), true)];

//                     if(strcmp($email,$decoded['data'][0]['email'] ?? null) === 0){
//                         return ['status'=>'success','data'=>json_decode(json_encode($decode), true)];
//                     }else{
//                         return ['status'=>'error','message'=>'invalid email'];
//                     }
//                 }else if($opt == 'refresh'){
//                     $decode = JWT::decode($token, new Key(env('JWT_SECRET_REFRESH_TOKEN'), 'HS512'));
//                     $decoded = json_decode(json_encode($decode), true);
//                     if(strcmp($email,$decoded['data'][0]['email'] ?? null) === 0){
//                         return ['status'=>'success','data'=>json_decode(json_encode($decode), true)];
//                     }else{
//                         return ['status'=>'error','message'=>'invalid email'];
//                     }
//                 }else{
//                     return ['status'=>'error','message'=>'invalid data'];
//                 }
//             }
//         }catch(ExpiredException $e){
//             return ['status'=>'error','message'=>$e->getMessage()];
//         } catch (SignatureInvalidException $e) {
//             return ['status'=>'error','message'=>$e->getMessage()];
//         } catch (BeforeValidException $e) {
//             return ['status'=>'error','message'=>$e->getMessage()];
//         }catch(UnexpectedValueException $e){
//             return ['status'=>'error','message'=>$e->getMessage()];
//         } catch (InvalidArgumentException $e) {
//             return ['status'=>'error','message'=>$e->getMessage()];
//         } catch (DomainException $e) {
//             return ['status'=>'error','message'=>$e->getMessage()];
//         // } catch (LogicException $e) {
//             // return ['status'=>'error','message'=>$e->getMessage()];
//         } catch (\Exception $e) {
//             return ['status'=>'error','message'=>$e->getMessage()];
//         }
//     }
//     public function updateTokenWebsite($data){
//         try{
//             if(empty($data) || is_null($data)){
//                 return ['status'=>'error','message'=>'data empty'];
//             }else{
//                 // return $data;
//                 // $dataDb = User::select()->whereRaw("BINARY email LIKE '%".$email."%'")->limit(1)->get();
//                 // $data = json_decode(json_encode($dataDb));
//                 $exp = time() + intval(env('JWT_ACCESS_TOKEN_EXPIRED'));
//                 $payload = ['data'=>$data, 'exp'=>$exp];
//                 $secretKey = env('JWT_SECRET');
//                 $token = JWT::encode($payload, $secretKey,'HS512');
//                 return ['status'=>'success','data'=>json_decode(json_encode($token),true)];
//             }
//         }catch(UnexpectedValueException $e){
//             return ['status'=>'error','message'=>$e->getMessage()];
//         }
//     }
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
//     //delete refresh token website 
//     public function deleteRefreshWebsite($email,$number = null){
//         try{
//             if(empty($email) || is_null($email)){
//                 return ['status'=>'error','message'=>'email empty','code'=>400];
//             // }else if(empty($number) || is_null($number)){
//             //     return ['status'=>'error','message'=>'token empty','code'=>400];
//             }else{
//                 if($number == null){
//                     $deleted = DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND device = 'website'")->delete();
//                     if($deleted){
//                         return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
//                     }else{
//                         return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
//                     }
//                 }else{
//                     $deleted = DB::table('refresh_token')->whereRaw("BINARY email LIKE '%$email%' AND number = $number AND device = 'website'")->delete();
//                     if($deleted){
//                         return ['status'=>'success','message'=>'success delete refresh token','code'=>200];
//                     }else{
//                         return ['status'=>'error','message'=>'failed delete refresh token','code'=>500];
//                     }
//                 }
//             }
//         }catch(\Exception $e){
//             return ['status'=>'error','message'=>$e->getMessage()];
//         }
//     }
}
?>