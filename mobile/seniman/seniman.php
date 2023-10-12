<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanMobile{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
    }
//untuk masyarakat
    public static function regisrasiSeniman($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                throw new Exception('Nama seniman harus di isi');
            }
            if (!isset($data['nik_seniman']) || empty($data['nik_seniman'])) {
                throw new Exception('nik seniman harus di isi');
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
            if (!isset($data['jenis_kelamin_seniman']) || empty($data['jenis_kelamin_seniman'])) {
                throw new Exception('Jenis kelamin harus di isi');
            }else if(!in_array($data['jenis_kelamin_seniman'],['laki-laki','perempuan'])){
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
            //get last id seniman
            $query = "SELECT id_seniman FROM seniman WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $idSeniman = '';
            $stmt[0]->bind_result($idSeniman);
            if(!$stmt[0]->fetch()){
                $idSeniman = 1;
            }
            $stmt[0]->close();
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
            $query = "INSERT INTO seniman (nomor_induk,nama_seniman,jenis_kelamin, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi,jumlah_anggota,ktp_seniman,pass_foto, surat_keterangan, tgl_pembuatan,tgl_berlaku,status, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?)";
            $stmt[1] = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori_event'] = strtoupper($data['kategori_event' ]);
            $nomerInduk = rand(1,9999);
            $now = date('Y-m-d');
            $stmt[1]->bind_param("ssssssssssssssss", $nomerInduk, $data['nama_seniman'], $data['jenis_kelamin_seniman'],$data['tempat_lahir'],$data['tanggal_lahir'], $data['alamat'],$data['no_telpon'], $data['nama_organisasi'], $data['anggota_organisasi'],$fileKtpDB,$fileFotoDB, $fileSuratDB,$now,$now, $status, $data['id_user']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'event berhasil ditambahkan']);
                exit();
            } else {
                $stmt[1]->close();
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
    public static function editSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                exit();
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                exit();
            } elseif (strlen($data['nama_seniman']) < 5) {
                exit();
            } elseif (strlen($data['nama_seniman']) > 50) {
                exit();
            }
            if (strlen($data['deskripsi_seniman']) > 4000) {
                exit();
            }
            if (!isset($data['kategori_seniman']) || empty($data['kategori_seniman'])) {
                exit();
            }else if(!in_array($data['kategori_seniman'],['olahraga','seni','budaya'])){
                exit();
            }
            if (!isset($data['tanggal_awal_seniman']) || empty($data['tanggal_awal_seniman'])) {
                exit();
            }else if (!isset($data['tanggal_akhir_seniman']) || empty($data['tanggal_akhir_seniman'])) {
                exit();
            }
            $tanggal_awal = date('Y-m-d H:i:s',strtotime($data['tanggal_awal_seniman']));
            $tanggal_akhir = date('Y-m-d H:i:s',strtotime($data['tanggal_akhir_seniman']));
            if (!$tanggal_awal) {
                exit();
            }else if (!$tanggal_akhir) {
                exit();
            }
            // Compare the dates
            if ($tanggal_awal > $tanggal_akhir) {
                exit();
            }
            $query = "UPDATE seniman SET nama_seniman = ?, deskripsi_seniman = ?, kategori_seniman = ?, tanggal_awal_seniman = ?, tanggal_akhir_seniman = ?, link_pendaftaran = ?, poster_seniman = ?, status = ? WHERE id_user = ? AND id_seniman = ?";
            $stmt = self::$con->prepare($query);
            $status = 'terkirim';
            $data['kategori'] = strtoupper($data['kategori']);
            $stmt->bind_param("ssssssssii", $data['nama_seniman'], $data['deskripsi_seniman'], $data['kategori_seniman'], $tanggal_awal, $tanggal_akhir, $data['link_pendaftaran'], $data['poster_seniman'], $status, $data['id_user'], $data['id_seniman']);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $stmt->close();
                exit();
            } else {
                $stmt->close();
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
            exit();
        }
    }
    public static function hapusSeniman($data, $uri = null){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                exit();
            }
            $query = "DELETE FROM seniman WHERE id_seniman = ? AND id_user = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('ss', $data['id_seniman'],$data['id_user']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                exit();
            } else {
                $stmt[2]->close();
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
            exit();
        }
    }
    //khusus admin seniman dan super admin
    public static function prosesSeniman($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            exit();
        }
        if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
            return ['status'=>'error','message'=>'Nama seniman harus di isi','code'=>400];
        } elseif (strlen($data['nama_seniman']) < 5) {
            return ['status'=>'error','message'=>'Nama seniman minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_seniman']) > 50) {
            return ['status'=>'error','message'=>'Nama seniman maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi seniman harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi seniman maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori seniman harus di isi','code'=>400];
        }else if(!in_array($data['kategori'],['olahraga','seni'])){
            return ['status'=>'error','message'=>'Kategori salah','code'=>400];
        }
        if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
            return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
        }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
            return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
        }
    }
    public static function verfikasiSeniman($data, $uri = null){
        if(!isset($data['id_user']) || empty($data['id_user'])){
            return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
        }
        if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
            return ['status'=>'error','message'=>'Nama seniman harus di isi','code'=>400];
        } elseif (strlen($data['nama_seniman']) < 5) {
            return ['status'=>'error','message'=>'Nama seniman minimal 5 karakter','code'=>400];
        } elseif (strlen($data['nama_seniman']) > 50) {
            return ['status'=>'error','message'=>'Nama seniman maksimal 50 karakter','code'=>400];
        }
        if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
            return ['status'=>'error','message'=>'Deskripsi seniman harus di isi','code'=>400];
        } elseif (strlen($data['deskripsi']) > 4000) {
            return ['status'=>'error','message'=>'deskripsi seniman maksimal 4000 karakter','code'=>400];
        }
        if (!isset($data['kategori']) || empty($data['kategori'])) {
            return ['status'=>'error','message'=>'Kategori seniman harus di isi','code'=>400];
        }else if(!in_array($data['kategori'],['olahraga','seni'])){
            return ['status'=>'error','message'=>'Kategori salah','code'=>400];
        }
        if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
            return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
        }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
            return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
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
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $SenimanMobile = new SenimanMobile();
    $SenimanMobile->regisrasiSeniman(SenimanMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    $SenimanMobile = new SenimanMobile();
    SenimanMobile::editSeniman(SenimanMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $SenimanMobile = new SenimanMobile();
    SenimanMobile::hapusSeniman(SenimanMobile::handle());
}
// if(isset($_POST['tambah'])){

//     // if (is_uploaded_file($_FILES['file']['tmp_name'])) { 
//     tambahSeniman($_POST);
// }
// if(isset($_POST['edit'])){
//     editSeniman($_POST);
// }
// if(isset($_POST['hapus'])){
//     hapusSeniman($_POST);
// }
// if(isset($_POST['proses'])){
//     prosesSeniman($_POST);
// }
?>