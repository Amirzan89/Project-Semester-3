<?php
    class DATABASE extends mysqli{
        // membuat variabel untuk koneksi 
        private $PORT = "3306";
        private $namaServer = "localhost:";
        private $username = "root";
        private $password = "";
        private $namaDb = "website";
        private $jumlahKoneksi = 0;
        public $conn;
        public function createConn(){
            $conn = new mysqli($this->namaServer.$this -> PORT,$this->username,$this->password,$this->namaDb);
            if($conn->connect_error){
                throw new Exception("Tidak bisa membuat koneksi");
            }else{
                echo "jumlah koneksi ". $this->jumlahKoneksi."<br>";
                $this->jumlahKoneksi++;
                echo "nyambung cuyy<br>";
                echo "jumlah koneksi ". $this->jumlahKoneksi."<br>";
            }
        }
        public function cekKoneksi(){
            // return $this->jumlahKoneksi;
            return $this ->jumlahKoneksi;
        }
        public function closeKoneksi(){
            if($this -> close()){
                return "tutup koneksi";
            }else{
                return "gagal";
            }
        }
        public function getData($tabel,$kondisi){
            $query = "SELECT * FROM ".$tabel.$kondisi;
            $hasil = $this -> query($query);

        }
        public function getDataKolomP($kolom, $tabel,$kondisi,$value){
            $hasil = "";
            $mysqli = new mysqli($this->namaServer.$this -> PORT,$this->username,$this->password,$this->namaDb);
            $stmt = $mysqli ->prepare("SELECT $kolom FROM $tabel WHERE $kondisi = ? LIMIT 0,1");
            $stmt->bind_param("s", $value);
            $stmt->execute();
            $stmt->bind_result($hasil);
            $stmt->fetch();
            $mysqli->close();
            return $hasil;
        }
        public function getDataKolom($kolom,$tabel,$kondisi){
            $query = "SELECT ".$kolom." FROM ".$tabel." ".$kondisi;
            $hasil = $this-> query($query);
            $hasil;
        }
    }
?>