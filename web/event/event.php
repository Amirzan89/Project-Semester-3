<?php
require_once(__DIR__ . '/../../web/koneksi.php');
require_once(__DIR__ . '/../../Date.php');
class EventWebsite{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/public/img/event';
    }
    public static function hapusEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID event harus di isi');
            }
            $query = "DELETE FROM event WHERE id_event = ? AND id_user = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('ss', $data['id_event'],$data['id_user']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'event berhasil dihapus']);
            } else {
                $stmt[2]->close();
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
                    'message' => $errorJson->message,
                );
            }
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
            if(!isset($data['tanggal']) || empty($data['tanggal'])){
                throw new Exception('Tanggal harus di isi !');
            }
            if(!isset($data['desc']) || empty($data['desc'])){
                throw new Exception('Deskripsi harus di isi !');
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
            if(($role != 'admin event' && $role != 'super admin') || $role == 'masyarakat'){
                throw new Exception('Invalid role');
            }
            //check and get data
            if($data['tanggal'] == 'semua'){
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_event, nama_pengirim, nama_event, DATE(created_at) AS tanggal, status FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_event DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_event, nama_pengirim, nama_event, DATE(created_at) AS tanggal, status FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE status = 'ditolak' OR status = 'diterima' ORDER BY id_event DESC";
                }else{
                    throw new Exception('Deskripsi invalid !');
                }
                $stmt[1] = self::$con->prepare($query);
            }else{
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_event, nama_pengirim, nama_event, DATE(created_at) AS tanggal, status FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE (status = 'diajukan 'OR status = 'proses') AND MONTH(updated_at) = ? AND YEAR(updated_at) = ? ORDER BY id_event DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_event, nama_pengirim, nama_event, DATE(created_at) AS tanggal, status FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE (status = 'ditolak 'OR status = 'diterima') AND MONTH(updated_at) = ? AND YEAR(updated_at) = ? ORDER BY id_event DESC";
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
                throw new Exception('Data event tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = array();
            while ($row = $result->fetch_assoc()) {
                $eventsData[] = $row;
            }
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data event tidak ditemukan');
            }
            if (empty($eventsData)) {
                throw new Exception('Data event tidak ditemukan');
            }
            $eventsData = changeMonth($eventsData);
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
    //khusus admin event dan super admin
    public function prosesEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                http_response_code(400);
                echo "<script>alert('ID User harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                http_response_code(400);
                echo "<script>alert('ID Event harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['keterangan']) || empty($data['keterangan'])){
                http_response_code(400);
                echo "<script>alert('Keterangan harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }else{
                if($data['keterangan'] == 'diajukan'){
                    http_response_code(400);
                    echo "<script>alert('Keterangan invalid !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }else if(!in_array($data['keterangan'],['proses','diterima','ditolak'])){
                    http_response_code(400);
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
                http_response_code(400);
                echo "<script>alert('User tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if(($role != 'admin event' && $role != 'super admin') || $role == 'masyarakat'){
                http_response_code(400);
                echo "<script>alert('Invalid role !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id event
            $query = "SELECT status FROM events WHERE id_event = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_event']);
            $stmt[1]->execute();
            $statusDB = '';
            $stmt[1]->bind_result($statusDB);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                http_response_code(400);
                echo "<script>alert('Data event tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //check status
            if($data['keterangan'] ==  'proses' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                http_response_code(400);
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($statusDB ==  'diajukan' && ($data['keterangan'] == 'diterima' || $data['keterangan'] == 'ditolak')){
                http_response_code(400);
                echo "<script>alert('Data harus di proses')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'ditolak' && $statusDB == 'diterima'){
                http_response_code(400);
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && $statusDB == 'ditolak'){
                http_response_code(400);
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //update data
            $query = "UPDATE events SET status = ?, catatan = ? WHERE id_event = ?";
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
                    http_response_code(400);
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/pengajuan.php';
                $status = 'ditolak';
            }
            $stmt[2]->bind_param("ssi", $status, $data['catatan'], $data['id_event']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo "<script>alert('Status berhasil diubah')</script>";
                echo "<script>window.location.href = '/event". $redirect . "'; </script>";
                exit();
            } else {
                $stmt[2]->close();
                http_response_code(500);
                echo "<script>alert('Status gagal diubah')</script>";
                echo "<script>window.location.href = '/event". $redirect . "'; </script>";
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
                    'message' => $errorJson->message,
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo "<script>alert('$error')</script>";
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
// if($_SERVER['REQUEST_METHOD'] == 'GET'){
//     include(__DIR__.'/../../notfound.php');
// }
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $eventWeb = new EventWebsite();
    $data = EventWebsite::handle();
    if(isset($data['_method'])){
        if($data['_method'] == 'PUT'){
            if(isset($data['keterangan'])){
                $eventWeb->prosesEvent($data);
            }
        }
    }
    if(isset($data['desc'])){
        if($data['desc'] == 'pengajuan' || $data['desc'] == 'riwayat'){
            $eventWeb->getEvent($data);
        }
    }
}
?>