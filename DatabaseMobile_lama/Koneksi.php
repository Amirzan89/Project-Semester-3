<?php
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "hufflepuff_testing"; 

// Membuat koneksi
$konek = new mysqli($host, $username, $password, $database);

// Memeriksa koneksi
if ($konek->connect_error) {
    die("Koneksi gagal: " . $konek->connect_error);
}


?>
