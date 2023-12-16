<?php
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "hufflepuff_testing"; 
// $host = "localhost:3306"; 
// $username = "tifz1761_elok"; 
// $password = "tifnganjuk321"; 
// $database = "tifz1761_hufflepuff"; 

// Membuat koneksi
$konek = new mysqli($host, $username, $password, $database);

// Memeriksa koneksi
if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}


?>
