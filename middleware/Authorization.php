<?php
require_once 'Controllers/Auth/JWTController.php';
require_once 'Controllers/UserController.php';
class Authorization
{
    public function handle($request,$data = null){
        return ['status'=>'success','data'=>['random'=>'terserah']];
    }
}