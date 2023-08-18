<?php
namespace Database;
use Exception;
class Database{
    private static $instance;
    private $conn;
    private function __construct() {
        // Private constructor to prevent direct instance creation
    }
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->createConn();
        }
        return self::$instance;
    }
    private function createConn() {
        $this->conn = new \mysqli('p:'.$_SERVER['DB_HOST'].':'.$_SERVER['DB_PORT'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD'], $_SERVER['DB_DATABASE']);
        if ($this->conn->connect_error) {
            throw new Exception("Tidak bisa membuat koneksi");
        } else {
            // echo "nyambung cuyy<br>";
        }
    }
    public function getConnection() {
        return $this->conn;
    }
    private static $pool = [];
    //pool
    // public function closeKoneksi(){
    //     if($this -> close()){
    //         return "tutup koneksi";
    //     }else{
    //         return "gagal";
    //     }
    // }
    // public function getData($tabel,$kondisi){
    //     $query = "SELECT * FROM ".$tabel.$kondisi;
    //     $hasil = $this -> query($query);
    // }
    // public function getDataKolomP($kolom, $tabel,$kondisi,$value){
    //     $hasil = "";
    //     $mysqli = new mysqli($this->namaServer.$this -> PORT,$this->username,$this->password,$this->namaDb);
    //     $stmt = $mysqli ->prepare("SELECT $kolom FROM $tabel WHERE $kondisi = ? LIMIT 0,1");
    //     $stmt->bind_param("s", $value);
    //     $stmt->execute();
    //     $stmt->bind_result($hasil);
    //     $stmt->fetch();
    //     $mysqli->close();
    //     return $hasil;
    // }
    // public function getDataKolom($kolom,$tabel,$kondisi){
    //     $query = "SELECT ".$kolom." FROM ".$tabel." ".$kondisi;
    //     $hasil = $this-> query($query);
    //     $hasil;
    // }
}
?>