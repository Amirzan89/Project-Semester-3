<?php
require_once(__DIR__ . '/../../web/koneksi.php');
require_once(__DIR__ . '/../pentas/pentas.php');
class AdvisMobile{
    private static $sizeFile = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    private static $kategoriInp = [
        'CAMP'=>'campursari',
        'DLG'=>'dalang',
        'JKP'=>'jaranan',
        'KRW'=>'karawitan',
        'MC'=>'mc',
        'LDR'=>'ludruk',
        'OKM'=>'organisasi kesenian musik',
        'ORG'=>'organisasi',
        'PRAM'=>'pramugari tayup',
        'SGR'=>'sanggar',
        'SIND'=>'sinden',
        'VOC'=>'vocalis',
        'WAR'=>'waranggono',
        'BAR'=>'barongsai',
        'KTR'=>'ketoprak',
        'PTJ'=>'pataji',
        'REOG'=>'reog',
        'THR'=>'taman hiburan rakyat',
        'PLWK'=>'pelawak'
    ];
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/pentas';
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
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_pentas']);
            if ($stmt[2]->execute()) {
                $result = $stmt[2]->get_result();
                $pentasData = $result->fetch_assoc();
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Data Seniman berhasil didapatkan', 'data' => $pentasData]);
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
            if(!isset($data['nama']) || empty($data['nama'])){
                throw new Exception('Nama harus di isi !');
            }
            if (!isset($data['alamat']) || empty($data['alamat'])) {
                throw new Exception(' Alamat harus di isi !');
            }
            if (strlen($data['alamat']) > 100) {
                throw new Exception(' Alamat maksimal 100 karakter !');
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
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi !');
            }
            if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi !');
            }
            if (!isset($data['tempat_pentas']) || empty($data['tempat_pentas'])) {
                throw new Exception(' Tempat pentas harus di isi !');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_awal = strtotime($data['tanggal_awal']);
            $tanggal_akhir = strtotime($data['tanggal_akhir']);
            $tanggalAwalDB = date('Y-m-d H:i:s', $tanggal_awal);
            $tanggalAkhirDB = date('Y-m-d H:i:s', $tanggal_akhir);
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
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
            $query = "SELECT nomor_induk, id_kategori_seniman FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $nisDB = '';
            $kategori = '';
            $stmt[1]->bind_result($nisDB, $kategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            $currentHour = date('G'); //format 0-23
            $seniman = new SenimanMobile();
            $kategori = $seniman->kategori(['id_kategori'=>$kategori],'getINI');
            if($kategori == 'DLG'){
                if ($currentHour >= 21) {
                    throw new Exception('Permintaan anda tidak boleh lebih dari jam 9 malam');
                }
            }else{
                if ($currentHour >= 17) {
                    throw new Exception('Permintaan anda tidak boleh lebih dari jam 5 sore');
                }
            }
            //get last id advis
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'surat_advis' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idAdvis = 1;
            $stmt[1]->bind_result($idAdvis);
            $stmt[1]->fetch();
            $stmt[1]->close();
            //create folder
            if (!is_dir(self::$folderPath)) {
                mkdir(self::$folderPath, 0777, true);
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
            $nameFile = '/'.$idAdvis.'.'.$extension;
            $fileSuratPath = self::$folderPath.$nameFile;
            $fileSuratDB = $nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            //save data
            $query = "INSERT INTO surat_advis (nomor_induk, nama_advis, alamat_advis, deskripsi_advis, tgl_awal, tgl_selesai, tempat_advis, surat_keterangan, status, id_user, id_seniman) VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $stmt[2]->bind_param("sssssssssii", $nisDB, $data['nama'], $data['alamat'], $data['deskripsi'], $tanggalAwalDB, $tanggalAkhirDB, $data['tempat_pentas'], $fileSuratDB, $status, $data['id_user'], $data['id_seniman']);
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
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
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
            //check id advis
            $query = "SELECT status FROM surat_advis WHERE BINARY id_advis = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_advis']);
            $stmt[1]->execute();
            $statusDB = '';
            $stmt[1]->bind_result($statusDB);
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
            $query = "UPDATE surat_advis SET nama_advis = ?, alamat_advis = ?, deskripsi_advis = ?, tgl_advis = ?, tempat_advis = ? WHERE id_advis = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param("sssssi", $data['nama'], $data['alamat'], $data['deskripsi'], $tanggalDB, $data['tempat_pentas'], $data['id_advis']);
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
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    echo 'ilang';
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pentasMobile = new AdvisMobile();
    $data = AdvisMobile::handle();
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
    $pentasMobile = new AdvisMobile();
    $pentasMobile->editPentas(AdvisMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $pentasMobile = new AdvisMobile();
    $pentasMobile->hapusPentas(AdvisMobile::handle());
}
?>