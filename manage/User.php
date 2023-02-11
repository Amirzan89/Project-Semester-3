<?php
    require "../data/database.php";
    class User extends DATABASE{
        function checkLogin($email,$password){
            // echo "mengecek <br>";
            return $this -> checkEmail($email) && $this -> checkPassword($email, $password);
        }
        function checkLoginTest($email,$password){
            return true;
        }
        function checkEmail($email){
            if(!$email == $this -> getDataKolomP("email","users","email",$email)){
                echo "email salah ";
                throw new Exception("Email tidak ada !");
            }else{
                return true;
            }
        }
        function checkPasswordTest($email, $password){
            if(!$password == $this -> getDataKolomP("password","users","email",$email)){
                throw new Exception("Password salah !");
            }else{
                return true;
            }
        }
        function checkPassword($email, $password){
            if(!password_verify($password,$this ->getDataKolomP("password","users","email",$email))){
                throw new Exception("Password salah !");
            }else{
                return true;
            }
        }
        function getIdKaryawan($email){
            if($this -> checkEmail($email)){
                $hasil = $this -> getDataKolomP("id_karyawan","users","email",$email);
                return $hasil;
            }
        }
        function getLevel($email){
            if($this -> checkEmail($email)){
                $hasil = $this -> getDataKolomP("level","users","email",$email);
                return $hasil;
            }
        }
        function getLevelTest($username){
            return "ADMIN";
        }
        function getUsername($email){
            if($this -> checkEmail($email)){
                $hasil = $this -> getDataKolomP("username","users","email",$email);
                return $hasil;
            }
        }
        function getNamaUserTest($username){
            return "random123";
        }
        function addUser(){
            //
        }
    }
?>