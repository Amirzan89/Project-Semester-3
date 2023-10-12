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
    //khusus admin seniman dan super admin
    public static function prosesTempat($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('id user harus di isi');
            }
            if (!isset($data['id_seniman']) || empty($data['id_seniman'])) {
                throw new Exception('id seniman harus di isi');
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
            if($role == 'super admin' || $role == 'admin seniman'){
                throw new Exception('invalid role');
            }
            //check id seniman
            $query = "SELECT id_seniman FROM seniman WHERE BINARY id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            //update status
            $query = "UPDATE seniman SET status = ? WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $status = 'proses';
            $stmt[2]->bind_param("ss", $status, $data['id_seniman']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo json_encode(['status'=>'success','message'=>'seniman berhasil dubah']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'seniman gagal diubah','code'=>500]));
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
            if (!isset($data['id_seniman']) || empty($data['id_seniman'])) {
                throw new Exception('id seniman harus di isi');
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
            if($role == 'super admin' || $role == 'admin seniman'){
                throw new Exception('invalid role');
            }
            //check id seniman
            $query = "SELECT id_seniman FROM seniman WHERE BINARY id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            //update status
            if($status == 'diterima'){
                $query = "UPDATE seniman SET status = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ss", $status, $data['id_seniman']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo json_encode(['status'=>'success','message'=>'seniman berhasil dubah']);
                    exit();
                } else {
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'seniman gagal diubah','code'=>500]));
                }
            }else if($status == 'ditolak'){
                $query = "UPDATE seniman SET status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ss", $status, $data['catatan'], $data['id_seniman']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo json_encode(['status'=>'success','message'=>'seniman berhasil dubah']);
                    exit();
                } else {
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'seniman gagal diubah','code'=>500]));
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