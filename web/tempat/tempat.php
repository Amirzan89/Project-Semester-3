<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class TempatWebsite{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
    }
    public static function getData($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('id user harus di isi');
            }
            if($data['role'] != 'admin tempat' || $data['role'] == 'super admin'){
                throw new Exception('invalid role');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('user tidak ditemukan');
            }
            $stmt[0]->close();
            if($role == $data['role']){
                throw new Exception('invalid role');
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
    public static function tambahTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($data['nama_tempat']) || empty($data['nama_tempat'])) {
                throw new Exception('Nama Tempat harus di isi');
            }
            if (!isset($data['nik_Tempat']) || empty($data['nik_Tempat'])) {
                throw new Exception('nik Tempat harus di isi');
            }
            if (!isset($data['alamat']) || empty($data['alamat'])) {
                throw new Exception('Alamat harus di isi');
            }
            if (!isset($data['no_telpon']) || empty($data['no_telpon'])) {
                throw new Exception('Nomor telpon harus di isi');
            }
            if (strlen($data['no_telpon']) > 16) {
                throw new Exception('Nama event maksimal 16 karakter');
            }
            if (!isset($data['jenis_kelamin_Tempat']) || empty($data['jenis_kelamin_Tempat'])) {
                throw new Exception('Jenis kelamin harus di isi');
            }else if(!in_array($data['jenis_kelamin_Tempat'],['laki-laki','perempuan'])){
                throw new Exception('Jenis kelamin salah');
            }
            if (!isset($data['tempat_lahir']) || empty($data['tempat_lahir'])) {
                throw new Exception('Tempat lahir harus di isi');
            }
            if (!isset($data['tanggal_lahir']) || empty($data['tanggal_lahir'])) {
                throw new Exception('Tanggal lahir harus di isi');
            }
            if (!isset($data['nama_organisasi']) || empty($data['nama_organisasi'])) {
                throw new Exception('Nama organisasi harus di isi');
            }
            if (!isset($data['anggota_organisasi']) || empty($data['anggota_organisasi'])) {
                throw new Exception('Jumlah anggota harus di isi');
            }
            if (!isset($_FILES['foto_ktp']) || empty($_FILES['foto_ktp'])) {
                throw new Exception('foto ktp harus di isi');
            }
            if (!isset($_FILES['pass_foto']) || empty($_FILES['pass_foto'])) {
                throw new Exception('pass foto harus di isi');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keternangan harus di isi');
            }
            if ($_FILES['foto_ktp']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload ktp file');
            }
            if ($_FILES['pass_foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload foto file');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload pdf file');
            }
            $tanggal_lahir = date('Y-m-d H:i:s',strtotime($data['tanggal_lahir']));
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('user tidak ditemukan');
            }
            $stmt[0]->close();
            if($role != 'masyarakat'){
                throw new Exception('invalid role');
            }
            //get last id Tempat
            $query = "SELECT id_Tempat FROM Tempat ORDER BY id_Tempat DESC LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idSeniman = 1;
            $stmt[1]->bind_result($idSeniman);
            $stmt[1]->fetch();
            $stmt[1]->close();
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            if (!is_dir(self::$folderPath.$folderKtp)) {
                mkdir(self::$folderPath.$folderKtp, 0777, true);
            }
            if (!is_dir(self::$folderPath.$folderPassFoto)) {
                mkdir(self::$folderPath.$folderPassFoto, 0777, true);
            }
            if (!is_dir(self::$folderPath.$folderSurat)) {
                mkdir(self::$folderPath.$folderSurat, 0777, true);
            }
            //proses file
            $fileKtp = $_FILES['foto_ktp'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= 5 * 1024 * 1024) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;  
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            $fileKtpDB = $folderKtp.$nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= 5 * 1024 * 1024) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            $fileFotoDB = $folderPassFoto.$nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['name']);
            if ($extension === 'pdf' || $extension === 'docx') {
                if ($size >= 5 * 1024 * 1024) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            $fileSuratDB = $folderSurat.$nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "INSERT INTO Tempat (nomor_induk, nik, nama_Tempat,jenis_kelamin, tempat_lahir, tanggal_lahir, alamat_Tempat, no_telpon, nama_organisasi,jumlah_anggota,ktp_Tempat,pass_foto, surat_keterangan, tgl_pembuatan,tgl_berlaku,status, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori_event'] = strtoupper($data['kategori_event' ]);
            $nomerInduk = rand(1,9999);
            $now = date('Y-m-d');
            $stmt[2]->bind_param("sssssssssssssssss", $nomerInduk, $data['nik_Tempat'], $data['nama_Tempat'], $data['jenis_kelamin_Tempat'],$data['tempat_lahir'],$data['tanggal_lahir'], $data['alamat'],$data['no_telpon'], $data['nama_organisasi'], $data['anggota_organisasi'],$fileKtpDB,$fileFotoDB, $fileSuratDB,$now,$now, $status, $data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'event berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'event gagal ditambahkan','code'=>500]));
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
    //khusus admin Tempat dan super admin
    public static function prosesTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('id user harus di isi');
            }
            if (!isset($data['id_Tempat']) || empty($data['id_Tempat'])) {
                throw new Exception('id Tempat harus di isi');
            }
            if (!isset($data['keterangan']) || empty($data['keterangan'])) {
                throw new Exception('keterangan harus di isi');
            }
            if($data['keterangan'] != 'proses'){
                throw new Exception('keterangan invalid');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('user tidak ditemukan');
            }
            $stmt[0]->close();
            if($role == 'super admin' || $role == 'admin Tempat'){
                throw new Exception('invalid role');
            }
            //check id Tempat
            $query = "SELECT id_Tempat FROM Tempat WHERE BINARY id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data Tempat tidak ditemukan');
            }
            //update status
            $query = "UPDATE Tempat SET status = ? WHERE id_Tempat = ?";
            $stmt[2] = self::$con->prepare($query);
            $status = 'proses';
            $stmt[2]->bind_param("ss", $status, $data['id_Tempat']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'Tempat berhasil dubah']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Tempat gagal diubah','code'=>500]));
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
    public static function verifikasiTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('id user harus di isi');
            }
            if (!isset($data['id_Tempat']) || empty($data['id_Tempat'])) {
                throw new Exception('id Tempat harus di isi');
            }
            if (!isset($data['keterangan']) || empty($data['keterangan'])) {
                throw new Exception('keterangan harus di isi');
            }
            if($data['keterangan'] != 'setuju'){
                throw new Exception('keterangan invalid');
            }else{
                $status = 'diterima';
            }
            if($data['keterangan'] != 'tolak'){
                throw new Exception('keterangan invalid');
            }else{
                if (!isset($data['catatan']) || empty($data['catata'])) {
                    throw new Exception('catatan harus di isi');
                }else{
                    $status = 'ditolak';
                }
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('user tidak ditemukan');
            }
            $stmt[0]->close();
            if($role == 'super admin' || $role == 'admin Tempat'){
                throw new Exception('invalid role');
            }
            //check id Tempat
            $query = "SELECT id_Tempat FROM Tempat WHERE BINARY id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data Tempat tidak ditemukan');
            }
            //update status
            if($status == 'diterima'){
                $query = "UPDATE Tempat SET status = ? WHERE id_Tempat = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ss", $status, $data['id_Tempat']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo json_encode(['status'=>'success','message'=>'Tempat berhasil dubah']);
                    exit();
                } else {
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Tempat gagal diubah','code'=>500]));
                }
            }else if($status == 'ditolak'){
                $query = "UPDATE Tempat SET status = ?, catatan = ? WHERE id_Tempat = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ss", $status, $data['catatan'], $data['id_Tempat']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo json_encode(['status'=>'success','message'=>'Tempat berhasil dubah']);
                    exit();
                } else {
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Tempat gagal diubah','code'=>500]));
                }
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
        // } elseif ($contentType === "multipart/form-data") {
        //     $requestData = $_POST;
        //     return $requestData;
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo 'ilang';
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $data = TempatWebsite::handle();
    if($data['keterangan'] == 'proses'){
        TempatWebsite::prosesTempat($data);
    }else{
        TempatWebsite::verifikasiTempat($data);
    }
}
?>