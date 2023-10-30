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
    //khusus admin pentas dan super admin
    public static function prosesPentas($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_advis']) || empty($data['id_advis'])){
                echo "<script>alert('ID sewa harus di isi !')</script>";
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
            $stmt[1]->bind_param('s', $data['id_advis']);
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
            if($data['keterangan'] ==  'ditolak' && $statusDB == 'diterima'){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && $statusDB == 'ditolak'){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //update data
            $query = "UPDATE surat_advis SET status = ?, catatan = ? WHERE id_advis = ?";
            $stmt[2] = self::$con->prepare($query);
            if($data['keterangan'] == 'proses'){
                $status = 'proses';
                $redirect = '/pengajuan.php';
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
            }else if($data['keterangan'] == 'diterima'){
                $status = 'diterima';
                $redirect = '/pengajuan.php';
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/pengajuan.php';
                $status = 'ditolak';
            }
            $stmt[2]->bind_param("ssi", $status, $data['catatan'], $data['id_advis']);
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
    echo 'ilang';
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $pentasWeb = new PentasWebsite();
    $data = PentasWebsite::handle();
    if(isset($data['keterangan'])){
        $pentasWeb->prosesPentas($data);
    }
}
?>