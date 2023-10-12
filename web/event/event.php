<?php
require_once('koneksi.php');
class EventWeb{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/public/img/event';
    }
    private static function isExistUser($data){
        $idUser = $data['id_user'];
        $query = "SELECT email FROM users WHERE BINARY id_user = ? LIMIT 1";
        $stmt = self::$con->prepare($query);
        $stmt->bind_param('s', $idUser);
        $stmt->execute();
        return $stmt->fetch();
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
    //khusus admin event dan super admin
    public static function prosesEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
            } elseif (strlen($data['nama_event']) < 5) {
                return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
            } elseif (strlen($data['nama_event']) > 50) {
                return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                return ['status'=>'error','message'=>'Deskripsi event harus di isi','code'=>400];
            } elseif (strlen($data['deskripsi']) > 4000) {
                return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
            }
            if (!isset($data['kategori']) || empty($data['kategori'])) {
                return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
            }else if(!in_array($data['kategori'],['olahraga','seni'])){
                return ['status'=>'error','message'=>'Kategori salah','code'=>400];
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
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
    public static function verfikasiEvent($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                return ['status'=>'error','message'=>'ID User harus di isi','code'=>400];
            }
            if (!isset($data['nama_event']) || empty($data['nama_event'])) {
                return ['status'=>'error','message'=>'Nama event harus di isi','code'=>400];
            } elseif (strlen($data['nama_event']) < 5) {
                return ['status'=>'error','message'=>'Nama event minimal 5 karakter','code'=>400];
            } elseif (strlen($data['nama_event']) > 50) {
                return ['status'=>'error','message'=>'Nama event maksimal 50 karakter','code'=>400];
            }
            if (!isset($data['deskripsi']) || empty($data['deskripsi'])) {
                return ['status'=>'error','message'=>'Deskripsi event harus di isi','code'=>400];
            } elseif (strlen($data['deskripsi']) > 4000) {
                return ['status'=>'error','message'=>'deskripsi event maksimal 4000 karakter','code'=>400];
            }
            if (!isset($data['kategori']) || empty($data['kategori'])) {
                return ['status'=>'error','message'=>'Kategori event harus di isi','code'=>400];
            }else if(!in_array($data['kategori'],['olahraga','seni'])){
                return ['status'=>'error','message'=>'Kategori salah','code'=>400];
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                return ['status'=>'error','message'=>'Tanggal awal harus di isi','code'=>400];
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                return ['status'=>'error','message'=>'Tanggal akhir harus di isi','code'=>400];
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
            // echo json_encode($responseData);
            echo "<script>alert('".json_encode($responseData)."')</script>";
            exit();
        }
    }
}
if(isset($_POST['proses'])){
    prosesEvent($_POST);
}
?>