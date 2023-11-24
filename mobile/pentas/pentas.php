<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class PentasMobile{
    private static $sizeFile = 10 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    private static $jsonPath = __DIR__."/../../kategori_seniman.json";
    private static $folderFile = __DIR__.'/../../private/pentas/file.json';
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/pentas';
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
                $query = "SELECT id_advis, surat_keterangan FROM surat_advis";
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
                    $query = "SELECT id_advis, surat_keterangan FROM surat_advis";
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
                        if (isset($item['surat_keterangan'])){
                            $file = self::getBaseFileName(pathinfo($item['surat_keterangan'])['filename']);
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
    public function getPentas($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_pentas']) || empty($data['id_pentas'])){
                throw new Exception('ID Pentas harus di isi !');
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
            //check id_pentas and get data
            $query = "SELECT nomor_induk, nama_advis, alamat_advis, deskripsi_advis, tgl_awal, tgl_selesai, tempat_advis FROM surat_advis WHERE id_advis = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_pentas']);
            if ($stmt[1]->execute()) {
                $result = $stmt[1]->get_result();
                $pentasData = $result->fetch_assoc();
                $stmt[1]->close();
                if ($pentasData === null) {
                    throw new Exception('Data pentas tidak ditemukan');
                }
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Data pentas berhasil didapatkan', 'data' => $pentasData]);
                exit();
            }else{
                $stmt[1]->close();
                throw new Exception('Data pentas tidak ditemukan');
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
    public function kategori($data, $desc){
        try{
            if($desc == 'check'){
                if(!isset($data['kategori']) || empty($data['kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
            }else if($desc == 'get'){
                if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
            }else if($desc == 'getINI'){
                if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
            }
            $jsonFile = file_get_contents(self::$jsonPath);
            $jsonData = json_decode($jsonFile, true);
            $result = null;
            foreach($jsonData as $key => $item){
                if($desc == 'check'){
                    if (isset($item['nama_kategori']) && $item['nama_kategori'] == $data['kategori']) {
                        $result = $item['id_kategori_seniman'];
                    }
                }else if($desc == 'get'){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori']) {
                        $result = $jsonData['nama_kategori'];
                    }
                }else if($desc == 'getINI'){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori']) {
                        $result = $item['singkatan_kategori'];
                    }
                }
            }
            if($result === null){
                throw new Exception('Data kategori tidak ditemukan');
            }
            return $result;
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
    public function tambahPentas($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi !');
            }
            if(!isset($data['nomor_induk']) || empty($data['nomor_induk'])){
                throw new Exception('ID Seniman harus di isi !');
            }
            if(!isset($data['nama_advis']) || empty($data['nama_advis'])){
                throw new Exception('Nama advis harus di isi !');
            }
            if (!isset($data['alamat_advis']) || empty($data['alamat_advis'])) {
                throw new Exception(' Alamat harus di isi !');
            }
            if (strlen($data['alamat_advis']) > 100) {
                throw new Exception(' Alamat maksimal 100 karakter !');
            }
            if (!isset($data['deskripsi_advis']) || empty($data['deskripsi_advis'])) {
                throw new Exception(' Deskripsi harus di isi !');
            }
            if (strlen($data['deskripsi_advis']) > 25) {
                throw new Exception(' Deskripsi maksimal 25 angka !');
            }
            if(!isset($data['nama_pentas']) || empty($data['nama_pentas'])){
                throw new Exception('Nama pentas harus di isi !');
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi !');
            }
            if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi !');
            }
            if (!isset($data['tempat_advis']) || empty($data['tempat_advis'])) {
                throw new Exception(' Tempat pentas harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggalAwalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggalAkhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            // Check if the date formats are valid
            if (!$tanggal_awal) {
                throw new Exception('Format tanggal awal tidak valid !');
            }
            if (!$tanggal_akhir) {
                throw new Exception('Format tanggal selesai tidak valid !');
            }
            if ($tanggal_awal > $tanggal_akhir) {
                throw new Exception('Tanggal akhir tidak boleh lebih awal dari tanggal awal !');
            }
            if ($tanggal_awal < $tanggal_sekarang){
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
            if($role != 'masyarakat'){
                throw new Exception('invalid role');
            }
            //check seniman
            $query = "SELECT nomor_induk, id_kategori_seniman, status FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $nisDB = '';
            $kategori = '';
            $statusDB = '';
            $stmt[1]->bind_result($nisDB, $kategori, $statusDB);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'diajukan'){
                throw new Exception('Data seniman sedang diajukan');
            }else if($statusDB == 'proses'){
                throw new Exception('Data seniman sedang diproses');
            }else if($statusDB == 'ditolak'){
                throw new Exception('Data seniman ditolak mohon cek kembali');
            }
            if($data['nomor_induk'] !== $nisDB){
                throw new Exception('Nomor induk seniman tidak cocok mohon cek kembali');
            }
            //check time
            // $currentHour = date('G'); //format 0-23
            // $kategori = $this->kategori(['id_kategori'=>$kategori],'getINI');
            // if($kategori == 'DLG'){
            //     if ($currentHour >= 21) {
            //         throw new Exception('Permintaan anda tidak boleh lebih dari jam 9 malam');
            //     }
            // }else{
            //     if ($currentHour >= 17) {
            //         throw new Exception('Permintaan anda tidak boleh lebih dari jam 5 sore');
            //     }
            // }
            //save data
            $query = "INSERT INTO surat_advis (nomor_induk, nama_advis, alamat_advis, deskripsi_advis, tgl_awal, tgl_selesai, tempat_advis, status, created_at, updated_at, id_user, id_seniman) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $stmt[2]->bind_param("ssssssssssii", $data['nomor_induk'], $data['nama_advis'], $data['alamat_advis'], $data['deskripsi_advis'], $tanggalAwalDB, $tanggalAkhirDB, $data['tempat_advis'], $status, $tanggal_sekarangDB, $tanggal_sekarangDB, $data['id_user'], $data['id_seniman']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Pentas berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Pentas gagal ditambahkan','code'=>500]));
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
    public function editPentas($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_advis']) || empty($data['id_advis'])){
                throw new Exception('ID Advis harus di isi !');
            }
            if(!isset($data['nama']) || empty($data['nama'])){
                throw new Exception('Nama pengirim harus di isi !');
            }
            if (!isset($data['alamat']) || empty($data['alamat'])) {
                throw new Exception(' Alamat harus di isi !');
            }
            if (strlen($data['alamat']) > 25) {
                throw new Exception(' Alamat maksimal 25 angka !');
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                throw new Exception(' Deskripsi harus di isi !');
            }
            if (strlen($data['deskripsi']) > 25) {
                throw new Exception(' Deskripsi maksimal 25 angka !');
            }
            if(!isset($data['nama_pentas']) || empty($data['nama_pentas'])){
                throw new Exception('Nama pentas harus di isi !');
            }
            if (!isset($data['tanggal']) || empty($data['tanggal'])) {
                throw new Exception('Tanggal harus di isi !');
            }
            if (!isset($data['tempat_pentas']) || empty($data['tempat_pentas'])) {
                throw new Exception(' Tempat pentas harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal = strtotime($data['tanggal']);
            $tanggalDB = date('Y-m-d H:i:s', $tanggal);
            $tanggal_sekarangDB = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            // Check if the date formats are valid
            if (!$tanggal) {
                throw new Exception('Format tanggal awal tidak valid !');
            }
            // Compare the dates
            if ($tanggal < $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh kurang dari sekarang !');
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
            //check seniman
            $query = "SELECT nomor_induk, id_kategori_seniman FROM seniman WHERE id_seniman = ? AND status = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $status = 'diterima';
            $stmt[1]->bind_param('ss', $data['id_seniman'],$status);
            $stmt[1]->execute();
            $nisDB = '';
            $kategori = '';
            $stmt[1]->bind_result($nisDB, $kategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            //check id advis
            $query = "SELECT status, surat_keterangan FROM surat_advis WHERE BINARY id_advis = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_advis']);
            $stmt[1]->execute();
            $statusDB = '';
            $suratDB = '';
            $stmt[1]->bind_result($statusDB, $suratDB);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data Pentas tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //update data
            $query = "UPDATE surat_advis SET nama_advis = ?, alamat_advis = ?, deskripsi_advis = ?, tgl_advis = ?, tempat_advis = ?, updated_at = ?, WHERE id_advis = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param("ssssssi", $data['nama'], $data['alamat'], $data['deskripsi'], $tanggalDB, $data['tempat_pentas'], $tanggal_sekarangDB, $data['id_advis']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Pentas berhasil diubah']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Pentas gagal diubah','code'=>500]));
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
    public function hapusPentas($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_advis']) || empty($data['id_advis'])){
                throw new Exception('ID pentas harus di isi !');
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
            if($role != 'masyarakat'){
                throw new Exception('invalid role');
            }
            //check id_advis
            $query = "SELECT status FROM surat_advis WHERE id_advis = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_advis']);
            $stmt[0]->execute();
            $statusDB = '';
            $stmt[0]->bind_result($statusDB);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data advis tidak ditemukan');
            }
            $stmt[0]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //delete data 
            $query = "DELETE FROM surat_advis WHERE id_advis = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_advis']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data tempat berhasil dihapus']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data tempat gagal dihapus','code'=>500]));
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
        // } elseif ($contentType === "application/x-www-form-urlencoded") {
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
$pentasMobile = new PentasMobile();
if($_SERVER['APP_TESTING']){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $data = PentasMobile::handle();
        if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan']) && $data['keterangan'] == 'get'){
            $pentasMobile->getPentas($data);
        }
        if(isset($data['_method'])){
            if($data['_method'] == 'PUT'){
                $pentasMobile->editPentas($data);
            }
            if($data['_method'] == 'DELETE'){
                $pentasMobile->hapusPentas($data);
            }
        }else{
            $pentasMobile->tambahPentas($data);
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $pentasMobile->editPentas(PentasMobile::handle());
    }
    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $pentasMobile->hapusPentas(PentasMobile::handle());
    }
}
$tambahPentas = function ($data) use ($pentasMobile){
    $pentasMobile->tambahPentas($data);
};
$updatePentas = function ($data) use ($pentasMobile){
    $pentasMobile->editPentas($data);
};
?>