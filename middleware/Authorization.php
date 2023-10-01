<?php
require_once 'Controllers/Auth/JWTController.php';
require_once 'Controllers/UserController.php';
class Authorization
{
    //path khusus super admin
    private static $pathSpecial = ['/user/tambah/admin'];
    private static $pathAdminPentas = ['/pentas/verifikasi'];
    private static $pathAdminSeniman = ['/seniman/verifikasi'];
    private static $pathAdminTempat = ['/tempat/verifikasi'];
    private static $pathAdminEvent = ['/event/verifikasi'];
    private static $pathMasyarakat = ['/event/tambah','/pentas/tambah','/tempat/tambah','/seniman/tambah','/seniman/edit','/seniman'];
    private static $testingMasyakarat = ['/testing/tempat/dashboard','/testing/event/dashboard','/testing/pentas/dashboard',''];
    public function handle($request,$data){
        // if(in_array($data['uri'],self::$pathMasyarakat) && $request['role'] == 'masyarakat'){
        //     return ['status'=>'success'];
        // }else{
        //     return ['status'=>'error','message'=>'anda bukan masyarakat'];
        // }
        return ['status'=>'success','data'=>['random'=>'terserah']];
    }
}