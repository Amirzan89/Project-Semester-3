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
    public function getFullEvent($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi !');
            }
            //check email
            $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['email']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('User tidak ditemukan');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check id_event and get data
            $query = "SELECT * FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail
            WHERE events.id_event = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            if (!$stmt[1]->execute()) {
                $stmt[1]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = $result->fetch_assoc();
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data event tidak ditemukan');
            }
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data event berhasil didapatkan', 'data' => $eventsData]);
            exit();
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
    public function getEvent($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi !');
            }
            //check email
            $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['email']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('User tidak ditemukan');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check id_event and get data
            $query = "SELECT * FROM events WHERE id_event = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            if (!$stmt[1]->execute()) {
                $stmt[1]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = $result->fetch_assoc();
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data event tidak ditemukan');
            }
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data event berhasil didapatkan', 'data' => $eventsData]);
            exit();
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
    public function getDetailEvent($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi !');
            }
            //check email
            $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['email']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('User tidak ditemukan');
            }
            $stmt[0]->close();
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check id_event and get data
            $query = "SELECT events.id_detail, nama_event, deskripsi, kategori, tempat_event, tanggal_awal, tanggal_akhir, link_pendaftaran FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            if (!$stmt[1]->execute()) {
                $stmt[1]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = $result->fetch_assoc();
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data event tidak ditemukan');
            }
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data event berhasil didapatkan', 'data' => $eventsData]);
            exit();
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
    //untuk masyarakat
    public function tambahEventFull($data){
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
            if (!isset($data['tempat_event']) || empty($data['tempat_event'])) {
                throw new Exception('Tempat event harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggalSekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggalSekarangDB);
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
            // //check id_tempat
            // if (isset($data['id_tempat']) & !empty($data['id_tempat'])) {
            //     $query = "SELECT id_tempat FROM list_tempat WHERE BINARY id_tempat = ? LIMIT 1";
            //     $stmt[1] = self::$con->prepare($query);
            //     $stmt[1]->bind_param('s', $data['id_tempat']);
            //     $stmt[1]->execute();
            //     if (!$stmt[1]->fetch()) {
            //         $stmt[1]->close();
            //         throw new Exception(json_encode(['status' => 'error', 'message' => 'Data tempat tidak ditemukan','code'=>500]));
            //     }
            //     $stmt[1]->close();
            // }
            //get last id event
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'events' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idEvent = 1;
            $stmt[1]->bind_result($idEvent);
            $stmt[1]->fetch();
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
            $query = "INSERT INTO detail_events (nama_event, deskripsi, tempat_event, tanggal_awal, tanggal_akhir, link_pendaftaran, poster_event) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $data['kategori_event'] = strtoupper($data['kategori_event']);
            $fileDb = $fileTime.$nameFile;
            $stmt->bind_param("sssssss",$data['nama_event'], $data['deskripsi'], $data['tempat'], $tanggal_awalDB, $tanggal_akhirDB, $data['link'],$fileDb);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $id = self::$con->insert_id;
                //tambah data
                $query = "INSERT INTO events (nama_pengirim, status, created_at, updated_at, id_detail, id_user) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = self::$con->prepare($query);
                $status = 'diajukan';
                $stmt->bind_param("ssssss", $data['nama_pengirim'], $status, $tanggalSekarangDB, $tanggalSekarangDB, $id, $data['id_user']);
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
    public function tambahEvent($data){
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
            if (!isset($data['tempat_event']) || empty($data['tempat_event'])) {
                throw new Exception('Tempat event harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggalSekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggalSekarangDB);
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
            //get last id event
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'events' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idEvent = 1;
            $stmt[1]->bind_result($idEvent);
            $stmt[1]->fetch();
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
            $query = "INSERT INTO detail_events (nama_event, deskripsi, kategori, tempat_event, tanggal_awal, tanggal_akhir, link_pendaftaran, poster_event) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = self::$con->prepare($query);
            $data['kategori_event'] = strtoupper($data['kategori_event']);
            $fileDb = $fileTime.$nameFile;
            $stmt->bind_param("ssssssss",$data['nama_event'], $data['deskripsi'], $data['kategori_event'], $data    ['tempat'], $tanggal_awalDB, $tanggal_akhirDB, $data['link'],$fileDb);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $id = self::$con->insert_id;
                //tambah data
                $query = "INSERT INTO events (nama_pengirim, status, created_at, updated_at, id_detail, id_user) VALUES (?, ?, ?, ?, ?, ?)";
                $query = "INSERT INTO events (nama_pengirim, status, created_at, updated_at, id_detail, id_user) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = self::$con->prepare($query);
                $status = 'diajukan';
                $stmt->bind_param("ssssss", $data['nama_pengirim'], $status, $tanggalSekarangDB, $tanggalSekarangDB, $id, $data['id_user']);
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
            $tanggal_sekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
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
            $query = "SELECT events.id_detail, status, poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            $stmt[1]->execute();
            $path = '';
            $statusDB = '';
            $id_detail = 0;
            $stmt[1]->bind_result($id_detail, $statusDB, $path);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //if upload file then replace file
            if (isset($data['poster_event']) & !empty($data['poster_event'])) {
                $base64Image = $data['poster_event'];
                $base64Image = str_replace('data:image/jpeg;base64,', '', $base64Image);
                $imageData = base64_decode($base64Image);
                if ($imageData === false) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Error decoding image','code'=>500]));
                }
                //replace file
                $filePath = self::$folderPath.$path;
                unlink($filePath);
                if (!file_put_contents($filePath, $imageData)) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Failed to save image','code'=>500]));
                }
            }
            //update database 
            $query = "UPDATE events SET nama_pengirim = ?, updated_at = ? WHERE id_event = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param("ssi", $data['nama_pengirim'],$tanggal_sekarangDB, $data['id_event']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                //update database
                $query = "UPDATE detail_events SET nama_event = ?, deskripsi = ?, kategori = ?, tempat_event = ?, tanggal_awal = ?, tanggal_akhir = ?, link_pendaftaran = ? WHERE id_detail = ?";
                $stmt[3] = self::$con->prepare($query);
                $data['kategori_event'] = strtoupper($data['kategori_event']);
                $stmt[3]->bind_param("sssssssi", $data['nama_event'], $data['deskripsi'], $data['kategori_event'], $data['tempat'], $tanggal_awalDB, $tanggal_akhirDB, $data['link_pendaftaran'],  $id_detail);
                $stmt[3]->execute();
                if ($stmt[3]->affected_rows > 0) {
                    $stmt[3]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'event berhasil diupdate']);
                    exit();
                } else {
                    $stmt[3]->close();
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
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check id_event
            $query = "SELECT events.id_detail, status, poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_event']);
            $stmt[0]->execute();
            $statusDB = '';
            $path = '';
            $idDetail = 0;
            $stmt[0]->bind_result($idDetail,$statusDB,$path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data event tidak ditemukan');
            }
            $stmt[0]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
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
        // } else {
        //     http_response_code(400);
        //     header('Content-Type: application/json');
        //     echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
        //     exit();
        }
    }
}
function loadEnv($path = null){
    if($path == null){
        $path = __DIR__."/../../.env";
    }
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
                $_SERVER[trim($key)] = trim($value);
            }
        }
    }
};
loadEnv();
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include(__DIR__.'/../../notfound.php');
}
$eventMobile = new EventMobile();
if(isset($_SERVER['APP_TESTING']) && $_SERVER['APP_TESTING'] == 'true'){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $data = eventMobile::handle();
        if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan'])){
            if($data['keterangan'] == 'get full event'){
                $eventMobile->getFullEvent($data);
            }else if($data['keterangan'] == 'get event'){
                $eventMobile->getEvent($data);
            }else if($data['keterangan'] == 'get detail'){
                $eventMobile->getDetailEvent($data);
            }
        } 
        $eventMobile->tambahEventFull(eventMobile::handle());
    }
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $eventMobile->editEvent(eventMobile::handle());
    }
    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $eventMobile->hapusEvent(eventMobile::handle());
    }
}

$getEvent = function ($data) use ($eventMobile){
    $eventMobile->getEvent($data);
};
$getDetailEvent = function ($data) use ($eventMobile){
    $eventMobile->getDetailEvent($data);
};
$tambahEvent = function ($data) use ($eventMobile){
    $eventMobile->tambahEvent($data);
};
$updateEvent = function ($data) use ($eventMobile){
    $eventMobile->editEvent($data);
};
?>