<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class PentasWebsite{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
    }
    public static function getPentas($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi');
            }
            if(!isset($data['tanggal']) || empty($data['tanggal'])){
                throw new Exception('Tanggal harus di isi !');
            }
            if(!isset($data['desc']) || empty($data['desc'])){
                throw new Exception('Deskripsi harus di isi !');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['email']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('user tidak ditemukan');
            }
            $stmt[0]->close();
            if(($role != 'admin tempat' && $role != 'super admin') || $role == 'masyarakat'){
                throw new Exception('Invalid role');
            }
            //check and get data
            if($data['tanggal'] == 'semua'){
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM surat_advis WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_advis DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, kode_verifikasi FROM surat_advis WHERE status = 'ditolak' OR status = 'diterima' ORDER BY id_advis DESC";
                }else{
                    throw new Exception('Deskripsi invalid !');
                }
                $stmt[1] = self::$con->prepare($query);
            }else{
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM surat_advis WHERE (status = 'diajukan' OR status = 'proses') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_advis DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_advis, nomor_induk, nama_advis, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, kode_verifikasi FROM surat_advis WHERE (status = 'ditolak' OR status = 'diterima') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_advis DESC";
                }else{
                    throw new Exception('Deskripsi invalid !');
                }
                $stmt[1] = self::$con->prepare($query);
                $tanggal = explode('-',$data['tanggal']);
                $month = $tanggal[0];
                $year = $tanggal[1];
                $stmt[1]->bind_param('ss', $month, $year);
            }
            if (!$stmt[1]->execute()) {
                $stmt[1]->close();
                throw new Exception('Data pentas tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = array();
            while ($row = $result->fetch_assoc()) {
                $eventsData[] = $row;
            }
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data pentas tidak ditemukan');
            }
            if (empty($eventsData)) {
                throw new Exception('Data pentas tidak ditemukan');
            }
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data pentas berhasil didapatkan', 'data' => $eventsData]);
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
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    //khusus admin pentas dan super admin
    public static function prosesPentas($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_pentas']) || empty($data['id_pentas'])){
                echo "<script>alert('ID pentas harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['keterangan']) || empty($data['keterangan'])){
                echo "<script>alert('Keterangan harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }else{
                if($data['keterangan'] == 'diajukan'){
                    echo "<script>alert('Keterangan invalid !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }else if(!in_array($data['keterangan'],['proses','diterima','ditolak'])){
                    echo "<script>alert('Keterangan invalid !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
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
                echo "<script>alert('User tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if(($role != 'admin seniman' && $role != 'super admin') || $role == 'masyarakat'){
                echo "<script>alert('Invalid role !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id advis
            $query = "SELECT status FROM surat_advis WHERE id_advis = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_pentas']);
            $stmt[1]->execute();
            $statusDB = '';
            $stmt[1]->bind_result($statusDB);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Pentas tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //check status
            if($data['keterangan'] ==  'proses' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($statusDB ==  'diajukan' && ($data['keterangan'] == 'diterima' || $data['keterangan'] == 'ditolak')){
                echo "<script>alert('Data harus di proses')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'ditolak' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            // if($data['keterangan'] ==  'ditolak' && $statusDB == 'diterima'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            // if($data['keterangan'] ==  'diterima' && $statusDB == 'ditolak'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            //update data
            $query = "UPDATE surat_advis SET kode_verifikasi = ?, status = ?, catatan = ? WHERE id_advis = ?";
            $stmt[2] = self::$con->prepare($query);
            if($data['keterangan'] == 'proses'){
                $status = 'proses';
                $redirect = '/pengajuan.php';
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $code = '';
            }else if($data['keterangan'] == 'diterima'){
                $status = 'diterima';
                $redirect = '/pengajuan.php';
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $code = substr(uniqid(), 0 ,10);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/pengajuan.php';
                $status = 'ditolak';
                $code = '';
            }
            $stmt[2]->bind_param("sssi", $code, $status, $data['catatan'], $data['id_pentas']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo "<script>alert('Status berhasil diubah')</script>";
                echo "<script>window.location.href = '/pentas". $redirect . "'; </script>";
                exit();
            } else {
                $stmt[2]->close();
                echo "<script>alert('Status gagal diubah')</script>";
                echo "<script>window.location.href = '/pentas". $redirect . "'; </script>";
                exit();
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
                    'message' => $errorJson->message,
                );
            }
            echo "<script>alert('$error')</script>";
            echo "<script>window.history.back();</script>";
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
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pentasWeb = new PentasWebsite();
    $data = PentasWebsite::handle();
    if(isset($data['_method'])){
        if($data['_method'] == 'PUT'){
            if(isset($data['keterangan'])){
                $pentasWeb->prosesPentas($data);
            }
        }
    }
    if(isset($data['desc'])){
        if($data['desc'] == 'pengajuan' || $data['desc'] == 'riwayat'){
            $pentasWeb->getPentas($data);
        }
    }
}
?>