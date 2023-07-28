<?php
    class Session{
        public $username;
        public $email;
        public $level;
        public $page;
        public function __construct($username, $email, $level,$page){
            $this -> username = $username;
            $this -> email = $email;
            $this -> level = $level;
            $this -> page = $page;
        }
    }
?>