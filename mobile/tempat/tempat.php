<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class TempatMobile{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../public/img/event';
    }
    private static function isExistUser($data){
        $idUser = $data['id_user'];
        $query = "SELECT email FROM users WHERE BINARY id_user = ? LIMIT 1";
        $stmt = self::$con->prepare($query);
        $stmt->bind_param('s', $idUser);
        $stmt->execute();
        return $stmt->fetch();
    }
    public static function getEvent($data){
        //
    }
    //untuk masyarakat
    public function tambahEventMasyarakat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                throw new Exception('Nama event harus di isi');
            } elseif (strlen($data['nama_event']) < 5) {
                throw new Exception('Nama event minimal 5 karakter');
            } elseif (strlen($data['nama_event']) > 50) {
                throw new Exception('Nama event maksimal 50 karakter');
            }
            if (strlen($data['deskripsi']) > 4000) {
                throw new Exception('deskripsi event maksimal 4000 karakter');
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                throw new Exception('Kategori event harus di isi');
            }else if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                throw new Exception('Kategori salah');
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi');
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            echo "<br>";
            echo 'tanggal baru sekarang '.$tanggal_sekarang;
            echo "<br>";
            // Check if the date formats are valid
            echo 'hasil ';
            var_dump($tanggal_awal < $tanggal_sekarang);
            exit();
            echo "<br>";    
            if (!$tanggal_awal) {
                throw new Exception('Format tanggal awal tidak valid');
            }else if (!$tanggal_akhir) {
                throw new Exception('Format tanggal akhir tidak valid');
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                throw new Exception('Tanggal akhir tidak boleh lebih awal dari tanggal awal');
            }
            if ($tanggal_awal < $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if ($stmt[0]->fetch()) {
                if($role == 'masyarakat'){
                    $stmt[0]->close();
                    $bulan = date_format(new DateTime($tanggal_awal), "m");
                    $tahun = date_format(new DateTime($tanggal_awal), "Y");
                    $base64Image = $data['poster_event'];
                    $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
                    $imageData = base64_decode($base64Image);
                    if ($imageData === false) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Error decoding image','code'=>500]));
                    } else {
                        $fileTime = '/'.$tahun.'/'.$bulan;
                        $nameFile = '/'.$data['nama_event'].'.jpg';
                        $filePath = self::$folderPath.$fileTime.$nameFile;
                        if (!is_dir(self::$folderPath.$fileTime)) {
                            mkdir(self::$folderPath.$fileTime, 0777, true);
                        }
                        if (file_put_contents($filePath, $imageData)) {
                            $query = "INSERT INTO events (nama_event,deskripsi_event, kategori_event, tanggal_awal_event, tanggal_akhir_event, link_pendaftaran, poster_event,id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt = self::$con->prepare($query);
                            // $status = 'terkirim';
                            $data['kategori_event'] = strtoupper($data['kategori_event']);
                            $fileDb = $fileTime.$nameFile;
                            $stmt->bind_param("ssssssss", $data['nama_event'], $data[  'deskripsi'], $data['kategori_event'],$tanggal_awal, $tanggal_akhir, $data['link'],$fileDb,$data['id_user']);
                            $stmt->execute();
                            if ($stmt->affected_rows > 0) {
                                echo json_encode(['status'=>'success','message'=>'event berhasil ditambahkan']);
                                exit();
                            } else {
                                $stmt->close();
                                throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal ditambahkan','code'=>500]));
                            }
                        } else {
                            throw new Exception(json_encode(['status' => 'error', 'message' => 'Failed to save image','code'=>500]));
                        }
                    }
                }else{
                    $stmt[0]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'anda bukan masyarakat','code'=>400]));
                }
            }else{
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson['message'],
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public static function editEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi')</script>";
                exit();
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                echo "<script>alert('ID event harus di isi')</script>";
                exit();
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                echo "<script>alert('Nama event harus di isi')</script>";
                exit();
            } elseif (strlen($data['nama_event']) < 5) {
                echo "<script>alert('Nama event minimal 5 karakter')</script>";
                exit();
            } elseif (strlen($data['nama_event']) > 50) {
                echo "<script>alert('Nama event maksimal 50 karakter')</script>";
                exit();
            }
            if (strlen($data['deskripsi_event']) > 4000) {
                echo "<script>alert('Deskripsi event maksimal 4000 karakter')</script>";
                exit();
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                echo "<script>alert('Kategori event harus di isi')</script>";
                exit();
            }else if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                echo "<script>alert('Kategori salah')</script>";
                exit();
            }
            if (!isset($data['tanggal_awal_event']) || empty($data['tanggal_awal_event'])) {
                echo "<script>alert('Tanggal awal harus di isi')</script>";
                exit();
            }else if (!isset($data['tanggal_akhir_event']) || empty($data['tanggal_akhir_event'])) {
                echo "<script>alert('Tanggal akhir harus di isi')</script>";
                exit();
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal_event']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir_event']));
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                echo "<script>alert('Format tanggal awal tidak valid')</script>";
                exit();
            }else if (!$tanggal_akhir) {
                echo "<script>alert('Format tanggal akhir tidak valid')</script>";
                exit();
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                echo "<script>alert('Tanggal akhir tidak boleh lebih awal dari tanggal awal')</script>";
                exit();
            }
            $query = "UPDATE events SET nama_event = ?, deskripsi_event = ?, kategori_event = ?, tanggal_awal_event = ?, tanggal_akhir_event = ?, link_pendaftaran = ?, poster_event = ?, status = ? WHERE id_user = ? AND id_event = ?";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori_event'] = strtoupper($data['kategori_event']);
            $stmt->bind_param("ssssssssii", $data['nama_event'], $data['deskripsi_event'], $data['kategori_event'], $tanggal_awal, $tanggal_akhir, $data['link_pendaftaran'], $data['poster_event'], $status, $data['id_user'], $data['id_event']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                echo "<script>alert('event berhasil diupdate')</script>";
                exit();
            } else {
                $stmt->close();
                echo "<script>alert('event gagal diupdate')</script>";
                exit();
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $errorJson->message,
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public static function hapusEvent($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi')</script>";
                exit();
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                echo "<script>alert('ID event harus di isi')</script>";
                exit();
            }
            $query = "DELETE FROM event WHERE id_event = ? AND id_user = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('ss', $data['id_event'],$data['id_user']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                echo "<script>alert('event berhasil dihapus')</script>";
                exit();
            } else {
                $stmt[2]->close();
                echo "<script>alert('event gagal dihapus')</script>";
                exit();
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $erorr = json_decode($error, true);
            if ($erorr === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'message' => $erorr->message,
                );
            }
            echo "<script>alert('$responseData')</script>";
            exit();
        }
    }
    public static function handle(){
        $contentType = $_SERVER["CONTENT_TYPE"];
        if ($contentType === "application/json") {
            $rawData = file_get_contents("php://input");
            $requestData = json_decode($rawData, true);
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } elseif ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $EventMobile = new EventMobile();
    $EventMobile->tambahEventMasyarakat(EventMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    $EventMobile = new EventMobile();
    EventMobile::editEvent(EventMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $EventMobile = new EventMobile();
    EventMobile::hapusEvent(EventMobile::handle());
}
?>