<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class PentasMobile{
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../public/img/event';
    }
    private static function isExistUser($data){
        $idUser = $data['id_user'];
        $query = "SELECT email FROM users WHERE BINARY id_user = ? LIMIT 1";
        $stmt = self::$con->prepare($query);
        $stmt->bind_param('s', $idUser);
        $stmt->execute();
        return $stmt->fetch();
    }
    public static function getEvent($data){
        //
    }
    public function buatSuratAdvis($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if (!isset($data['nama_user']) || empty($data['nama_user'])) {
            }
                throw new Exception('Nama harus di isi');
            if (!isset($data['alamat_user']) || empty($data['alamat_user'])) {
                throw new Exception('alamat user harus di isi');
            }
            if (strlen($data['alamat']) > 4000) {
                throw new Exception('deskripsi event maksimal 4000 karakter');
            }
            if (!isset($data['kategori_event']) || empty($data['kategori_event'])) {
                throw new Exception('Kategori event harus di isi');
            }else if(!in_array($data['kategori_event'],['olahraga','seni','budaya'])){
                throw new Exception('Kategori salah');
            }
            if (!isset($data['tanggal_awal']) || empty($data['tanggal_awal'])) {
                throw new Exception('Tanggal awal harus di isi');
            }else if (!isset($data['tanggal_akhir']) || empty($data['tanggal_akhir'])) {
                throw new Exception('Tanggal akhir harus di isi');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if ($stmt[0]->fetch()) {
            }else{
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
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
    //untuk masyarakat
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
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $EventMobile = new EventMobile();
    $EventMobile->tambahEventMasyarakat(EventMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'PUT'){
    $EventMobile = new EventMobile();
    EventMobile::editEvent(EventMobile::handle());
}
if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    $EventMobile = new EventMobile();
    EventMobile::hapusEvent(EventMobile::handle());
}
?>