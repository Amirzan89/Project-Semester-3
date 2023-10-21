<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class EventMobile{
    private static $sizeFile = 5 * 1024 * 1024;
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
                throw new Exception('ID User harus di isi !');
            }
            if (!isset($data['nama_pengirim']) || empty($data['nama_pengirim'])) {
                throw new Exception('Nama event harus di isi !');
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                throw new Exception('Nama event harus di isi !');
            }
            if (strlen($data['nama_event']) < 5) {
                throw new Exception('Nama event minimal 5 karakter !');
            }
            if (strlen($data['nama_event']) > 50) {
                throw new Exception('Nama event maksimal 50 karakter !');
            }
            if (isset($data['deskripsi']) & !empty($data['deskripsi'])) {
                if (strlen($data['deskripsi']) > 4000) {
                    throw new Exception('deskripsi event maksimal 4000 karakter !');
                }
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                throw new Exception('Kategori event harus di isi !');
            }
            if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                throw new Exception('Kategori salah !');
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi !');
            }
            if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi !');
            }
            if (!isset($data['tempat']) || empty($data['tempat'])) {
                throw new Exception('Tempat event harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if (!$tanggal_awal) {
                throw new Exception('Format tanggal awal tidak valid !');
            }else if (!$tanggal_akhir) {
                throw new Exception('Format tanggal akhir tidak valid !');
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                throw new Exception('Tanggal akhir tidak boleh lebih awal dari tanggal awal !');
            }
            if ($tanggal_awal < $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            if($role != 'masyarakat'){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'anda bukan masyarakat','code'=>400]));
            }
            //check id_tempat
            if (isset($data['id_tempat']) & !empty($data['id_tempat'])) {
                $query = "SELECT id_tempat FROM list_tempat WHERE BINARY id_tempat = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_tempat']);
                $stmt[1]->execute();
                if (!$stmt[1]->fetch()) {
                    $stmt[1]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Data tempat tidak ditemukan','code'=>500]));
                }
                $stmt[1]->close();
            }
            //get last id event
            $query = "SELECT id_event FROM events ORDER BY id_event DESC LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idEvent = 1;
            $stmt[1]->bind_result($idEvent);
            if($stmt[1]->fetch()){
                $idEvent += 1;
            }
            $stmt[1]->close();
            //proses file
            $bulan = date_format(new DateTime($data['tanggal_awal']), "m");
            $tahun = date_format(new DateTime($data['tanggal_awal']), "Y");
            $base64Image = $data['poster_event'];
            $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Error decoding image','code'=>500]));
            }
            $fileTime = '/'.$tahun.'/'.$bulan;
            $nameFile = '/'.$idEvent.'.jpg';
            $filePath = self::$folderPath.$fileTime.$nameFile;
            if (!is_dir(self::$folderPath.$fileTime)) {
                mkdir(self::$folderPath.$fileTime, 0777, true);
            }
            if (!file_put_contents($filePath, $imageData)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Failed to save image','code'=>500]));
            }
            //tambah data
            $query = "INSERT INTO detail_events (nama_event, deskripsi, kategori_event, tempat_event, tanggal_awal, tanggal_akhir, link_pendaftaran, poster_event) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $data['kategori_event'] = strtoupper($data['kategori_event']);
            $fileDb = $fileTime.$nameFile;
            $stmt->bind_param("ssssssss",$data['nama_event'], $data['deskripsi'], $data['kategori_event'], $data['tempat'], $tanggal_awalDB, $tanggal_akhirDB, $data['link'],$fileDb);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $id = self::$con->insert_id;
                //tambah data
                $query = "INSERT INTO events (nama_pengirim, status, id_detail, id_sewa, id_user) VALUES (?, ?, ?, ?, ?)";
                $stmt = self::$con->prepare($query);
                $status = 'terkirim';
                $stmt->bind_param("sssss", $data['nama_pengirim'], $data[  'deskripsi'], $status, $data['id_user'], $data['id_tempat'],$id);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'event berhasil ditambahkan']);
                    exit();
                } else {
                    $stmt->close();
                    unlink($filePath);
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal ditambahkan','code'=>500]));
                }
            } else {
                $stmt->close();
                unlink($filePath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal ditambahkan','code'=>500]));
            }
        }catch(Exception $e){
            echo $e->getTraceAsString();
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
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public static function editEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi !');
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                throw new Exception('Nama event harus di isi !');
            }
            if (strlen($data['nama_event']) < 5) {
                throw new Exception('Nama event minimal 5 karakter !');
            }
            if (strlen($data['nama_event']) > 50) {
                throw new Exception('Nama event maksimal 50 karakter !');
            }
            if (isset($data['deskripsi']) && !empty($data['deskripsi'])) {
                if (strlen($data['deskripsi']) > 4000) {
                    throw new Exception('deskripsi event maksimal 4000 karakter !');
                }
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                throw new Exception('Kategori event harus di isi !');
            }
            if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                throw new Exception('Kategori salah !');
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi !');
            }
            if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                throw new Exception('Format tanggal awal tidak valid !');
            }else if (!$tanggal_akhir) {
                throw new Exception('Format tanggal akhir tidak valid !');
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                throw new Exception('Tanggal akhir tidak boleh lebih awal dari tanggal awal !');
            }
            if ($tanggal_awal > $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            if($role != 'masyarakat'){
                throw new Exception(json_encode(['status' => 'error', 'message' => 'anda bukan masyarakat','code'=>400]));
            }
            //check id_tempat
            if (isset($data['id_tempat']) && !empty($data['id_tempat'])) {
                $query = "SELECT id_tempat FROM list_tempat WHERE BINARY id_tempat = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_tempat']);
                $stmt[1]->execute();
                if (!$stmt[1]->fetch()) {
                    $stmt[1]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Data tempat tidak ditemukan','code'=>500]));
                }
                $stmt[1]->close();
            }
            //check data event
            $query = "SELECT id_detail, poster_event FROM events WHERE BINARY id_event = ? LIMIT 1 AND status = 'terkirim'";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            $stmt[1]->execute();
            $path = '';
            $id_detail = 0;
            $stmt[1]->bind_result($id_detail, $path);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $stmt[1]->close();
            if (isset($data['poster_event']) & !empty($data['poster_event'])) {
                $base64Image = $data['poster_event'];
                $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
                $imageData = base64_decode($base64Image);
                if ($imageData === false) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Error decoding image','code'=>500]));
                }
                $filePath = self::$folderPath.$path;
                //save file
                if (!file_put_contents($filePath, $imageData)) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Failed to save image','code'=>500]));
                }
            }
            //update database 
            $query = "UPDATE events SET nmaa_pengirim = ? WHERE id_event = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param("si", $data['nama_pengirim'], $data['id_event']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                //update database 
                $query = "UPDATE detail_events SET nama_event = ?, deskripsi = ?, kategori = ?, tempat_event = ?, tanggal_awal = ?, tanggal_akhir = ?, link_pendaftaran = ? WHERE id_event = ?";
                $stmt[2] = self::$con->prepare($query);
                $data['kategori_event'] = strtoupper($data['kategori_event']);
                $stmt[2]->bind_param("sssssssi", $data['nama_event'], $data['deskripsi'], $data['kategori_event'], $data['tempat'], $tanggal_awalDB, $tanggal_akhirDB, $data['link_pendaftaran'], $data['id_event']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'event berhasil diupdate']);
                    exit();
                } else {
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal diupdate','code'=>500]));
                }
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal diupdate','code'=>500]));
            }
        }catch(Exception $e){
            // echo $e->getTraceAsString();
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
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function hapusEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi !');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('User tidak ditemukan');
            }
            $stmt[0]->close();
            if(!in_array($role,['super admin','admin tempat'])){
                throw new Exception('Anda bukan admin');
            }
            //check id_event
            $query = "SELECT id_detail, poster_event FROM events WHERE BINARY id_event = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_event']);
            $stmt[0]->execute();
            $path = '';
            $idDetail = 0;
            $stmt[0]->bind_result($idDetail,$path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $stmt[0]->close();
            //delete file
            $fileSuratPath = self::$folderPath.$path;
            unlink($fileSuratPath);
            //delete data event
            $query = "DELETE FROM events WHERE id_event = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_event']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                //delete data detail event
                $query = "DELETE FROM detail_events WHERE id_detail = ?";
                $stmt[3] = self::$con->prepare($query);
                $stmt[3]->bind_param('s', $idDetail);
                if ($stmt[3]->execute()) {
                    $stmt[3]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'Data event berhasil dihapus']);
                    exit();
                } else {
                    $stmt[3]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Data event gagal dihapus','code'=>500]));
                }
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data event gagal dihapus','code'=>500]));
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
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
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
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } elseif ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
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
    $EventMobile->editEvent(EventMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $EventMobile = new EventMobile();
    $EventMobile->hapusEvent(EventMobile::handle());
}
?>