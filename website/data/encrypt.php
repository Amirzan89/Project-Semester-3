<?php
    function createSha256($pass){
        return hash("sha256",$pass);
    }
    function createSha512($pass){
        return hash("sha512",$pass);
    }
    function verifyHash($hash1,$hash2){
        if(hash_equals($hash1,$hash2)){
            return true;
        }else{
            return false;
        }
    }
?>