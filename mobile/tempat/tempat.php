<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class TempatMobile{
    private static $sizeFile = 10 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    private static $folderFile = __DIR__.'/../../private/sewa/file.json';
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/sewa';
    }
    private static function loadEnv($path = null){
        if(!$_SERVER['LOAD_ENV']){
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
                        $_SERVER['LOAD_ENV'] = true;
                    }
                }
            }
        }
    }
    private static function getBaseFileName($fileName) {
        preg_match('/^([^\(]+)(?:\((\d+)\))?(\.\w+)?$/', $fileName, $matches);
        if (isset($matches[1])) {
            $baseName = $matches[1];
            $number = isset($matches[2]) ? (int)$matches[2] : 0;
            return ['name' => $baseName, 'number' => $number];
        }
        return null;
    }
    private function manageFile($data, $desc, $opt = null){
        try{
            $filePath = self::$folderFile;
            $fileExist = file_exists($filePath);
            if (!$fileExist || empty($fileExist) || is_null($fileExist)) {
                //if file is delete will make new json file
                $query = "SELECT id_sewa, surat_ket_sewa FROM sewa_tempat";
                $stmt[0] = self::$con->prepare($query);
                if(!$stmt[0]->execute()){
                    $stmt[0]->close();
                    throw new Exception('Data file tidak ditemukan');
                }
                $result = $stmt[0]->get_result();
                $fileData = [];
                while ($row = $result->fetch_assoc()) {
                    $fileData[] = $row;
                }
                $stmt[0]->close();
                if (!empty($fileData) && $fileData !== null) {
                    $jsonData = json_encode($fileData, JSON_PRETTY_PRINT);
                    if (!file_put_contents($filePath, $jsonData)) {
                        throw new Exception('Gagal menyimpan file sistem');
                    }
                }
            }
            if($desc == 'tambah'){
                //check if file exist
            if (!$fileExist) {
                    //if file is delete will make new json file
                    $query = "SELECT id_sewa, surat_ket_sewa FROM sewa_tempat";
                    $stmt[0] = self::$con->prepare($query);
                    if(!$stmt[0]->execute()){
                        $stmt[0]->close();
                        throw new Exception('Data file tidak ditemukan');
                    }
                    $result = $stmt[0]->get_result();
                    $fileData = [];
                    while ($row = $result->fetch_assoc()) {
                        $fileData[] = $row;
                    }
                    $stmt[0]->close();
                    if (!empty($fileData) && $fileData !== null) {
                        $jsonData = json_encode($fileData, JSON_PRETTY_PRINT);
                        if (!file_put_contents($filePath, $jsonData)) {
                            throw new Exception('Gagal menyimpan file sistem');
                        }
                    }
                }else{
                    //tambah data file
                    $jsonFile = file_get_contents($filePath);
                    $jsonData = json_decode($jsonFile, true);
                    array_push($jsonData, $data);
                    $jsonFile = json_encode($jsonData, JSON_PRETTY_PRINT);
                    file_put_contents($filePath, $jsonFile);
                }
            }else if($desc == 'get'){
                if(!isset($data['nama_file']) || empty($data['nama_file'])){
                    throw new Exception('Nama file harus di isi');
                }
                $jsonFile = file_get_contents($filePath);
                $jsonData = json_decode($jsonFile, true);
                $fileNameNew = $data['nama_file'];
                $fileData = array();
                if($opt['col'] == 'surat'){
                    foreach($jsonData as $key => $item){
                        if (isset($item['surat_ket_sewa'])){
                            $file = self::getBaseFileName(pathinfo($item['surat_ket_sewa'])['filename']);
                            if($file['name'] == pathinfo($data['nama_file'])['filename']) {
                                array_push($fileData,['name'=>$file['name'],'number'=>$file['number']]);
                            }
                        }
                    }
                    //get number
                    $num = '';
                    if(is_null($fileData) || empty($fileData)){
                        $fileNameNew = $data['nama_file'];
                    }else{
                        foreach ($fileData as $file) {
                            if (isset($file['number']) && $file['number'] > $num) {
                                $num = $file['number'];
                            }
                        }
                        if(empty($num)){
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'(1).'.pathinfo($data['nama_file'])['extension'];
                        }else{
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'('.($num+1).').'.pathinfo($data['nama_file'])['extension'];
                        }
                    }
                }
                return '/'.$fileNameNew;
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
    public function getSewa($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_sewa']) || empty($data['id_sewa'])){
                throw new Exception('ID sewa tempat harus di isi !');
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
            //check id_sewa
            $query = "SELECT id_sewa, nik_sewa, nama_peminjam, nama_tempat, deskripsi_sewa_tempat, nama_kegiatan_sewa, jumlah_peserta, instansi, tgl_awal_peminjaman, tgl_akhir_peminjaman, status, catatan, id_tempat, id_user FROM sewa_tempat WHERE id_sewa = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_sewa']);
            $stmt[1]->execute();
            if ($stmt[1]->execute()) {
                $result = $stmt[1]->get_result();
                $sewaTempatData = $result->fetch_assoc();
                $stmt[1]->close();
                if ($sewaTempatData === null) {
                    throw new Exception('Data sewa tempat tidak ditemukan');
                }
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Data Sewa tempat berhasil didapatkan', 'data' => $sewaTempatData]);
                exit();
            }else{
                $stmt[1]->close();
                throw new Exception('Data sewa tempat tidak ditemukan');
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
    public function getTempat($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_tempat']) || empty($data['id_tempat'])){
                throw new Exception('ID sewa tempat harus di isi !');
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
            //check id_tempat
            $query = "SELECT id_tempat, nama_tempat, alamat_tempat, deskripsi_tempat, pengelola, contact_person FROM list_tempat WHERE id_tempat = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_tempat']);
            $stmt[1]->execute();
            if ($stmt[1]->execute()) {
                $result = $stmt[1]->get_result();
                $tempatData = $result->fetch_assoc();
                $stmt[1]->close();
                if ($tempatData === null) {
                    throw new Exception('Data tempat tidak ditemukan');
                }
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Data Sewa tempat berhasil didapatkan', 'data' => $tempatData]);
                exit();
            }else{
                $stmt[1]->close();
                throw new Exception('Data tempat tidak ditemukan');
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
    public function buatSewaTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_tempat']) || empty($data['id_tempat'])){
                $data['id_tempat'] = '';
            }else{
                //check id tempat
                $query = "SELECT id_tempat FROM list_tempat WHERE id_tempat = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_tempat']);
                $stmt[1]->execute();
                if(!$stmt[1]->fetch()){
                    $stmt[1]->close();
                    throw new Exception('Data tempat tidak ditemukan');
                }
                $stmt[1]->close();
            }
            if (!isset($data['nama_tempat']) || empty($data['nama_tempat'])) {
                throw new Exception('Nama tempat harus di isi !');
            }
            if (!isset($data['nik']) || empty($data['nik'])) {
                throw new Exception('Nik penyewa harus di isi !');
            }
            if (!is_numeric($data['nik'])) {
                throw new Exception('Nik penyewa harus berisi hanya angka !');
            }
            if (strlen($data['nik']) > 16) {
                throw new Exception('Nik penyewa maksimal 16 angka !');
            }
            if (!isset($data['nama_peminjam']) || empty($data['nama_peminjam'])) {
                throw new Exception('Nama peminjam harus di isi !');
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                throw new Exception('Deskripsi sewa tempat harus di isi !');
            }
            if (strlen($data['deskripsi']) > 100) {
                throw new Exception('Deskripsi sewa tempat maksimal 100 karakter !');
            }
            if (!isset($data['nama_kegiatan_sewa']) || empty($data['nama_kegiatan_sewa'])) {
                throw new Exception('Nama kegiatan harus di isi !');
            }
            if (strlen($data['nama_kegiatan_sewa']) > 50) {
                throw new Exception('Nama kegiatan maksimal 50 karakter !');
            }
            if (!isset($data['jumlah_peserta']) || empty($data['jumlah_peserta'])) {
                throw new Exception('Jumlah peserta harus di isi !');
            }
            if (!is_numeric($data['jumlah_peserta'])) {
                throw new Exception('Jumlah peserta harus berisi hanya angka !');
            }
            if (strlen($data['jumlah_peserta']) > 10) {
                throw new Exception('Jumlah peserta maksimal 10 angka !');
            }
            if (!isset($data['instansi']) || empty($data['instansi'])) {
                $data['instansi'] = '';
            }
            if (!isset($data['tanggal_awal_sewa']) || empty($data['tanggal_awal_sewa'])) {
                throw new Exception('Tanggal awal sewa harus di isi !');
            }
            if (!isset($data['tanggal_akhir_sewa']) || empty($data['tanggal_akhir_sewa'])) {
                throw new Exception('Tanggal akhir sewa harus di isi !');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keterangan harus di isi !');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload file !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal_sewa']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir_sewa']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            //tambah 1 minggu
            $tanggal_sekarang = strtotime('+1 week', $tanggal_sekarang);
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
            if ($tanggal_awal < $tanggal_sekarang){
                throw new Exception('Tanggal harus kurang dari 7 hari dari sekarang !');
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
            self::loadEnv();
            //get last id sewa
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'sewa_tempat' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idSewa = 1;
            $stmt[1]->bind_result($idSewa);
            $stmt[1]->fetch();
            $stmt[1]->close();
            //create folder
            if (!is_dir(self::$folderPath)) {
                mkdir(self::$folderPath, 0777, true);
            }
            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['tmp_name']);
            if (in_array($extension,['pdf'])) {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format surat keterangan harus pdf','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get', ['col'=>'surat']);
            $fileSuratPath = self::$folderPath.$nameFile;
            $fileSuratDB = $nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            //save data
            $query = "INSERT INTO sewa_tempat (nik_sewa, nama_tempat, nama_peminjam, deskripsi_sewa_tempat, nama_kegiatan_sewa, jumlah_peserta, instansi, surat_ket_sewa, tgl_awal_peminjaman, tgl_akhir_peminjaman, status, created_at, updated_at, id_tempat, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $stmt[2]->bind_param("sssssssssssssii", $data['nik'], $data['nama_tempat'], $data['nama_peminjam'], $data['deskripsi'],$data['nama_kegiatan_sewa'], $data['jumlah_peserta'], $data['instansi'], $fileSuratDB, $tanggal_awalDB, $tanggal_akhirDB, $status, $tanggal_sekarangDB, $tanggal_sekarangDB, $data['id_tempat'], $data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                self::manageFile(['id_sewa'=>self::$con->insert_id, 'surat_keterangan'=>$fileSuratDB],'tambah');
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data sewa tempat berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                unlink($fileSuratPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data sewa tempat gagal ditambahkan','code'=>500]));
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
    public function editSewaTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_sewa']) || empty($data['id_sewa'])){
                throw new Exception('ID sewa harus di isi !');
            }
            if(!isset($data['id_tempat']) || empty($data['id_tempat'])){
                $data['id_tempat'] == '';
            }else{
                //check id tempat
                $query = "SELECT id_tempat FROM list_tempat WHERE id_tempat = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_tempat']);
                $stmt[1]->execute();
                if(!$stmt[1]->fetch()){
                    $stmt[1]->close();
                    throw new Exception('Data tempat tidak ditemukan');
                }
                $stmt[1]->close();
            }
            if (!isset($data['nama_tempat']) || empty($data['nama_tempat'])) {
                throw new Exception('Nama tempat harus di isi !');
            }
            if (!isset($data['nik_penyewa']) || empty($data['nik_penyewa'])) {
                throw new Exception('Nik penyewa harus di isi !');
            }
            if (!is_numeric($data['nik_penyewa'])) {
                throw new Exception('Nik penyewa harus berisi hanya angka !');
            }
            if (strlen($data['nik_penyewa']) > 16) {
                throw new Exception('Nik penyewa maksimal 16 angka !');
            }
            if (!isset($data['nama_peminjam']) || empty($data['nama_peminjam'])) {
                throw new Exception('Nama peminjam harus di isi !');
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                throw new Exception('Deskripsi sewa tempat harus di isi !');
            }
            if (strlen($data['deskripsi']) > 100) {
                throw new Exception('Deskripsi sewa tempat maksimal 100 karakter !');
            }
            if (!isset($data['nama_kegiatan_sewa']) || empty($data['nama_kegiatan_sewa'])) {
                throw new Exception('Nama kegiatan harus di isi !');
            }
            if (strlen($data['nama_kegiatan_sewa']) > 50) {
                throw new Exception('Nama kegiatan maksimal 50 karakter !');
            }
            if (!isset($data['jumlah_peserta']) || empty($data['jumlah_peserta'])) {
                throw new Exception('Jumlah peserta harus di isi !');
            }
            if (!is_numeric($data['jumlah_peserta'])) {
                throw new Exception('Jumlah peserta harus berisi hanya angka !');
            }
            if (strlen($data['jumlah_peserta']) > 10) {
                throw new Exception('Jumlah peserta maksimal 10 angka !');
            }
            if (!isset($data['instansi']) || empty($data['instansi'])) {
                $data['instansi'] = '';
            }
            if (!isset($data['tanggal_awal_sewa']) || empty($data['tanggal_awal_sewa'])) {
                throw new Exception('Tanggal awal sewa harus di isi !');
            } 
            if (!isset($data['tanggal_akhir_sewa']) || empty($data['tanggal_akhir_sewa'])) { 
                throw new Exception('Tanggal akhir sewa harus di isi !');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keternangan harus di isi !');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload file !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal_sewa']);
            $tanggal_awalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggal_akhir = strtotime($data['tanggal_akhir_sewa']);
            $tanggal_akhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            //tambah 1 minggu
            $tanggal_sekarang = strtotime('+1 week', $tanggal_sekarang);
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
            if ($tanggal_awal < $tanggal_sekarang){
                throw new Exception('Tanggal harus kurang dari 7 hari dari sekarang !');
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
            //check id sewa
            $query = "SELECT status, surat_ket_sewa  FROM sewa_tempat WHERE id_sewa = ? LIMIT 1";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_sewa']);
            $stmt[2]->execute();
            $statusDB = '';
            $suratDB = '';
            $stmt[2]->bind_result($statusDB, $suratDB);
            if(!$stmt[2]->fetch()){
                $stmt[2]->close();
                throw new Exception('Data tempat tidak ditemukan');
            }
            $stmt[2]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //check if user upload file
            $updateSurat = false;
            if(isset($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']['name'])){
                //proses file
                $fileSurat = $_FILES['surat_keterangan'];
                $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
                $size = filesize($fileSurat['tmp_name']);
                if (in_array($extension,['pdf'])) {
                    if ($size >= self::$sizeFile) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format surat keterangan harus pdf','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get', ['col'=>'surat']);
                $fileSuratPath = self::$folderPath.$nameFile;
                $fileSuratDB = $nameFile;
                if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                unlink(self::$folderPath.$suratDB);
                $updateSurat = true;
            }
            //update data
            $query = "UPDATE sewa_tempat SET nik_sewa = ?, nama_tempat = ?, nama_peminjam = ?, deskripsi_sewa_tempat = ?, nama_kegiatan_sewa = ?, jumlah_peserta = ?, instansi = ?, surat_ket_sewa = ?, tgl_awal_peminjaman = ?, tgl_akhir_peminjaman = ?, updated_at = ? WHERE id_sewa = ?";
            $stmt[3] = self::$con->prepare($query);
            $stmt[3]->bind_param("sssssssssssi", $data['nik_penyewa'], $data['nama_tempat'], $data['nama_peminjam'], $data['deskripsi'], $data['nama_kegiatan_sewa'], $data['jumlah_peserta'], $data['instansi'], $fileSuratDB, $tanggal_awalDB, $tanggal_akhirDB, $tanggal_sekarangDB, $data['id_sewa']);
            $stmt[3]->execute();
            if ($stmt[3]->affected_rows > 0) {
                $stmt[3]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data sewa tempat berhasil diubah']);
                exit();
            } else {
                $stmt[3]->close();
                if($updateSurat == true){
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'Data sewa tempat berhasil diubah']);
                    exit();
                }
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data sewa tempat gagal diubah','code'=>500]));
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
    public function hapusSewaTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_sewa']) || empty($data['id_sewa'])){
                throw new Exception('ID sewa harus di isi !');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE id_user = ? LIMIT 1";
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
            //check id_sewa
            $query = "SELECT surat_ket_sewa,status FROM sewa_tempat WHERE id_sewa = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_sewa']);
            $stmt[0]->execute();
            $path = '';
            $statusDB = '';
            $stmt[0]->bind_result($path,$statusDB);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data sewa tempat tidak ditemukan');
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
            //delete data
            $query = "DELETE FROM sewa_tempat WHERE id_sewa = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_sewa']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data sewa tempat berhasil dihapus']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data sewa tempat gagal dihapus','code'=>500]));
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
    include(__DIR__.'/../../notfound.php');
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
$tempatMobile = new TempatMobile();
if($_SERVER['APP_TESTING']){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $data = TempatMobile::handle();
        if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan'])){
            if($data['keterangan'] == 'get sewa'){
                $tempatMobile->getSewa($data);
            }else if($data['keterangan'] == 'get tempat'){
                $tempatMobile->getTempat($data);
            }
        }
        if(isset($data['_method'])){
            if($data['_method'] == 'PUT'){
                $tempatMobile->editSewaTempat($data);
            }
            if($data['_method'] == 'DELETE'){
                $tempatMobile->hapusSewaTempat($data);
            }
        }else{
            $tempatMobile->buatSewaTempat($data);
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $tempatMobile->editSewaTempat(TempatMobile::handle());
    }
    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $tempatMobile->hapusSewaTempat(TempatMobile::handle());
    }
}
?>