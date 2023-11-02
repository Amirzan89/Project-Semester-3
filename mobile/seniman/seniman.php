<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanMobile{
    private static $sizeFile = 5 * 1024 * 1024;
    private static $sizeImg = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    private static $constID = '411.302';
    private static $kategori = [
        'campursari'=>'CAMP',
        'dalang'=>'DLG',
        'jaranan'=>'JKP',
        'karawitan'=>'KRW',
        'mc'=>'MC',
        'ludruk'=>'LDR',
        'organisasi kesenian musik'=>'OKM',
        'organisasi'=>'ORG',
        'pramugari tayup'=>'PRAM',
        'sanggar'=>'SGR',
        'sinden'=>'SIND',
        'vocalis'=>'VOC',
        'waranggono'=>'WAR'
    ];
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
    }
    public function getSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID seniman harus di isi !');
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
            //check id_seniman and get data
            $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kategori, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota FROM seniman WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_seniman']);
            if ($stmt[2]->execute()) {
                $result = $stmt[2]->get_result();
                $senimanData = $result->fetch_assoc();
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData]);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman tidak ditemukan', 'code' => 400]));
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
    public function regisrasiSeniman($data){
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
            if(!isset($data['kategori']) || empty($data['kategori'])){
                throw new Exception('Kategori harus di isi');
            }
            if (!array_key_exists($data['kategori'], self::$kategori)) {
                throw new Exception('Kategori invalid');
            }
            if (!isset($data['kecamatan']) || empty($data['kecamatan'])) {
                throw new Exception('Kecamatan harus di isi');
            }else if(!in_array($data['kecamatan'],['bagor','baron','berbek','gondang','jatikalen','kertosono','lengkong','loceret','nganjuk','ngetos','ngluyu','ngronggot','pace','patianrowo','prambon','rejoso','sawahan','sukomoro','tanjunganom','wilangan'])){
                throw new Exception('Kecamatan tidak ditemukan');
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
            $tanggal_lahir = strtotime($data['tanggal_lahir']);
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if ($tanggal_lahir > $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
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
            //get last id seniman
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'seniman' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idSeniman = 1;
            $stmt[1]->bind_result($idSeniman);
            $stmt[1]->fetch();
            $stmt[1]->close();
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //create folder
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
            $size = filesize($fileKtp['size']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus jpg, png, jpeg','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;  
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            $fileKtpDB = $nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['size']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus png, jpeg, jpg','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            $fileFotoDB = $nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['size']);
            if ($extension === 'pdf') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus pdf','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$idSeniman.'.'.$extension;
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            $fileSuratDB = $nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "INSERT INTO seniman (nik, nama_seniman,jenis_kelamin, kategori,kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi,jumlah_anggota,ktp_seniman,pass_foto, surat_keterangan, tgl_pembuatan,tgl_berlaku,status, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $now = date('Y-m-d');
            $end = date('Y-m-d',strtotime('12/31/' . date('Y')));
            $stmt[2]->bind_param("ssssssssssssssssss", $data['nik_seniman'], $data['nama_seniman'], $data['jenis_kelamin_seniman'], $data['kategori'], $data['kecamatan'], $data['tempat_lahir'], $data['tanggal_lahir'], $data['alamat'],$data['no_telpon'], $data['nama_organisasi'], $data['anggota_organisasi'],$fileKtpDB,$fileFotoDB, $fileSuratDB, $now, $end, $status, $data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                unlink($fileSuratPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal ditambahkan','code'=>500]));
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
    public function editSeniman($data){
        try{
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
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
            if(!isset($data['kategori']) || empty($data['kategori'])){
                throw new Exception('Kategori harus di isi');
            }
            if (!array_key_exists($data['kategori'], self::$kategori)) {
                throw new Exception('Kategori invalid');
            }
            if (!isset($data['kecamatan']) || empty($data['kecamatan'])) {
                throw new Exception('Kecamatan harus di isi');
            }else if(!in_array($data['kecamatan'],['bagor','baron','berbek','gondang','jatikalen','kertosono','lengkong','loceret','nganjuk','ngetos','ngluyu','ngronggot','pace','patianrowo','prambon','rejoso','sawahan','sukomoro','tanjunganom','wilangan'])){
                throw new Exception('Kecamatan tidak ditemukan');
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
            $tanggal_lahir = strtotime($data['tanggal_lahir']);
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if ($tanggal_lahir > $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            //check id seniman
            $query = "SELECT status FROM seniman WHERE BINARY id_seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $statusDB = '';
            $stmt[0]->bind_result($statusDB);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //proses file
            $fileKtp = $_FILES['foto_ktp'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;  
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['name']);
            if ($extension === 'pdf' || $extension === 'docx') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "UPDATE seniman SET nik = ?, nama_seniman = ?, jenis_kelamin = ?, kategori = ?, kecamatan = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat_seniman = ?, no_telpon = ?, nama_organisasi = ?, jumlah_anggota = ? WHERE id_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("ssssssssssss", $data['nik_seniman'], $data['nama_seniman'], $data['jenis_kelamin_seniman'], $data['kategori'], $data['kecamatan'], $data['tempat_lahir'],$data['tanggal_lahir'], $data['alamat'],$data['no_telpon'], $data['nama_organisasi'], $data['anggota_organisasi'],$data['id_seniman']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal diubah','code'=>500]));
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
    public function hapusSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID seniman harus di isi !');
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
            //check id_seniman
            $query = "SELECT status, ktp_seniman, pass_foto, surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $statusDB = '';
            $pathKTP = '';
            $pathFoto = '';
            $pathSurat = '';
            $stmt[1]->bind_result($statusDB, $pathKTP, $pathFoto,$pathSurat);
            if (!$stmt[1]->fetch()) {
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //delete file
            $fileKtpPath = self::$folderPath.$pathKTP;
            $fileFotoPath = self::$folderPath.$pathFoto;
            $fileSuratPath = self::$folderPath.$pathSurat;
            unlink($fileKtpPath);
            unlink($fileFotoPath);
            unlink($fileSuratPath);
            //delete data
            $query = "DELETE FROM seniman WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_seniman']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dihapus']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal dihapus','code'=>500]));
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
    public function buatPerpanjangan($data){
        try{
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
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
            //check seniman
            $query = "SELECT status, tgl_berlaku FROM seniman WHERE seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['seniman']);
            $stmt[0]->execute();
            $statusDB = '';
            $berlaku = '';
            $stmt[0]->bind_result($statusDB, $berlaku);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            //check tanggal berlaku
            $berlaku = strtotime($berlaku);
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if ($tanggal_sekarang < $berlaku){
                throw new Exception('Anda tidak bisa update data seniman !');
            }
            //file
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //proses file
            $fileKtp = $_FILES['foto_ktp'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['size']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;  
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            $fileKtpDB = $nameFile;
            //replace file
            unlink($fileKtpPath);
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['size']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            $fileFotoDB = $nameFile;
            //replace file
            unlink($fileFotoPath);
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['size']);
            if ($extension === 'pdf') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            $fileSuratDB = $nameFile;
            //replace file
            unlink($fileSuratPath);
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "INSERT INTO perpanjangan (nik, ktp_seniman, pass_foto, surat_keterangan, status, id_seniman) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $stmt[2]->bind_param("ssssss", $data['nik_seniman'], $fileKtpDB, $fileFotoDB, $fileSuratDB, $status, $data['id_seniman']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                unlink($fileSuratPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal ditambahkan','code'=>500]));
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
    public function editPerpanjangan($data){
        try{
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
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
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            //check id seniman
            $query = "SELECT status FROM seniman WHERE BINARY id_seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $statusDB = '';
            $berlaku = '';
            $stmt[0]->bind_result($statusDB, $berlaku);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //check tanggal berlaku
            $berlaku = strtotime($berlaku);
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if ($tanggal_sekarang < $berlaku){
                throw new Exception('Anda tidak bisa update data seniman !');
            }
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //proses file
            $fileKtp = $_FILES['foto_ktp'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;  
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['name']);
            if ($extension === 'pdf' || $extension === 'docx') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'file terlalu besar','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'file aneh','code'=>500]));
            }
            //simpan file
            $nameFile = '/'.$data['id_seniman'].'.'.$extension;
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "UPDATE perpanjangan SET nik = ? WHERE id_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("ss", $data['nik_seniman'],$data['id_seniman']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal diubah','code'=>500]));
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
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    $SenimanMobile = new SenimanMobile();
    $data = SenimanMobile::handle();
    if($data['keterangan'] == 'perpanjang'){
        $senimanMobile->editPerpanjangan($data);
    }else{
        $SenimanMobile->editSeniman(SenimanMobile::handle());
    }
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $SenimanMobile = new SenimanMobile();
    $SenimanMobile->hapusSeniman(SenimanMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $SenimanMobile = new SenimanMobile();
    $data = SenimanMobile::handle();
    if(isset($_POST['_method'])){
        if($_POST['_method'] == 'PUT'){
            if($data['keterangan'] == 'perpanjang'){
                $senimanMobile->editPerpanjangan($data);
            }else{
                $SenimanMobile->editSeniman($data);
            }
        }else if($_POST['_method'] == 'DELETE'){
            $SenimanMobile->hapusSeniman($data);
        }
    }else{
        if($data['keterangan'] == 'perpanjang'){
            $senimanMobile->buatPerpanjangan($data);
        }else{
            $SenimanMobile->regisrasiSeniman($data);
        }
    }
}
?>