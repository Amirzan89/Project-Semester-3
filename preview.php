<?php
require_once(__DIR__ . '/web/koneksi.php');
class Preview{
    private static $sizeFile = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderEventDes = '/event/preview';
    private static $folderTempatDes = '/tempat/preview';
    private static $folderSenimanDes = '/seniman/preview';
    private static $folderPentasDes = '/pentas/preview';

    private static $folderEvent = __DIR__.'/DatabaseMobile/uploads/events';
    private static $folderSeniman = __DIR__.'/DatabaseMobile/data_seniman_mobile/uploads/seniman';
    private static $folderPerpanjangan = __DIR__.'/DatabaseMobile/data_seniman_mobile/uploads/perpanjangan';
    private static $folderSewa = __DIR__.'/DatabaseMobile/uploads/pinjam';
    private static $folderTempat = __DIR__.'/DatabaseMobile/uploads/tempat';
    private static $folderPentas = __DIR__.'/private/pentas';
    // private static $folderSeniman = __DIR__.'/private/seniman';
    // private static $folderSewa = __DIR__.'/private/tempat';
    // private static $folderTempat = __DIR__.'/public/img/tempat';
    // private static $folderPentas = __DIR__.'/private/pentas';
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
    }
    public static function getEvent($data){
        
    }
    //untuk admin
    public function previewEvent($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                throw new Exception('ID Seniman harus di isi !');
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
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
                throw new Exception('User tidak ditemukan !');
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                throw new Exception('Anda bukan admin !');
            }
            //check id_event
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = ? LIMIT 1";
                $file = self::$folderEvent;
            }else{
                throw new Exception('Deskripsi invalid !');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_event']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data event tidak ditemukan !');
            }
            $stmt[0]->close();
            $file = $file.$path;
            //return file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderEventDes)) {
                    mkdir(__DIR__.self::$folderEventDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderEventDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderEventDes .'/'. $randomString . '.'. $extension;
                if (copy($file, $des)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','data'=>"$previewURL"]);  
                    exit();
                    // $startTime = time();
                    // $timeout = 5;
                    // while (true) {
                        //     if (time() - $startTime >= $timeout) {
                    //         unlink($des);
                    //         exit();
                    //     }
                    // }
                } else {
                    throw new Exception('Sistem error !');
                }
            } else {
                throw new Exception('File tidak ditemukan !');
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
    public function previewSeniman($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus diisi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus diisi !');
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                throw new Exception('Deskripsi harus diisi !');
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
                throw new Exception('User tidak ditemukan !');
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                throw new Exception('Anda bukan admin !');
            }
            //check id_seniman
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT pass_foto FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/pass_foto';
            }else if($data['deskripsi'] == 'ktp'){
                $query = "SELECT ktp_seniman FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/ktp_seniman';
            }else if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/surat_keterangan';
            }else{
                throw new Exception('Deskripsi invalid !');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan !');
            }
            $stmt[0]->close();
            $file = $file.$path;
            //download file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderSenimanDes)) {
                    mkdir(__DIR__.self::$folderSenimanDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderSenimanDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderSenimanDes .'/'. $randomString . '.'. $extension;
                if (copy($file, $des)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','data'=>"$previewURL"]);
                    exit();
                } else {
                    throw new Exception('Sistem error !');
                }
            } else {
                throw new Exception('File tidak ditemukan !');
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
    public function previewPerpanjangan($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi !');
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
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
                throw new Exception('User tidak ditemukan !');
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                throw new Exception('Anda bukan admin !');
            }
            //check id_seniman
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT pass_foto FROM perpanjangan WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/pass_foto';
            }else if($data['deskripsi'] == 'ktp'){
                $query = "SELECT ktp_seniman FROM perpanjangan WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/ktp_seniman';
            }else if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_keterangan FROM perpanjangan WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/surat_keterangan';
            }else{
                throw new Exception('Deskripsi invalid !');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data perpanjangan tidak ditemukan !');
            }
            $stmt[0]->close();
            $file = $file.$path;
            //download file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderSenimanDes)) {
                    mkdir(__DIR__.self::$folderSenimanDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderSenimanDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderSenimanDes .'/'. $randomString . '.'. $extension;
                if (copy($file, $des)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','data'=>"$previewURL"]);
                    exit();
                } else {
                    throw new Exception('Sistem error !');
                }
            } else {
                throw new Exception('File tidak ditemukan !');
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
    // public function previewPentas($data){
    //     try{
    //         if(!isset($data['email']) || empty($data['email'])){
    //             throw new Exception('Email harus di isi !');
    //         }
    //         if(!isset($data['id_pentas']) || empty($data['id_pentas'])){
    //             throw new Exception('ID Pentas harus di isi !');
    //         }
    //         if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
    //             throw new Exception('Deskripsi harus di isi !');
    //         }
    //         //check email
    //         $query = "SELECT role FROM users WHERE BINARY email = ? LIMIT 1";
    //         $stmt[0] = self::$con->prepare($query);
    //         $stmt[0]->bind_param('s', $data['email']);
    //         $stmt[0]->execute();
    //         $role = '';
    //         $stmt[0]->bind_result($role);
    //         if (!$stmt[0]->fetch()) {
    //             $stmt[0]->close();
    //             throw new Exception('User tidak ditemukan !');
    //         }
    //         $stmt[0]->close();
    //         if($role == 'masyarakat'){
    //             throw new Exception('Anda bukan admin !');
    //         }
    //         //check id_advis
    //         if($data['deskripsi'] == 'surat'){
    //             $query = "SELECT surat_keterangan FROM surat_advis WHERE id_advis = ? LIMIT 1";
    //             $file = self::$folderPentas;
    //         }else{
    //             throw new Exception('Deskripsi invalid !');
    //         }
    //         $stmt[0] = self::$con->prepare($query);
    //         $stmt[0]->bind_param('s', $data['id_pentas']);
    //         $stmt[0]->execute();
    //         $path = '';
    //         $stmt[0]->bind_result($path);
    //         if (!$stmt[0]->fetch()) {
    //             $stmt[0]->close();
    //             throw new Exception('Data pentas tidak ditemukan !');
    //         }
    //         $stmt[0]->close();
    //         $file = $file.$path;
    //         //download file
    //         if (file_exists($file)) {
    //             $randomString = bin2hex(random_bytes(16));
    //             //buat folder
    //             if (!is_dir(__DIR__.self::$folderPentasDes)) {
    //                 mkdir(__DIR__.self::$folderPentasDes, 0777, true);
    //             }
    //             $extension = pathinfo($file, PATHINFO_EXTENSION);
    //             $des = __DIR__ . self::$folderPentasDes .'/'. $randomString . '.'. $extension;
    //             $previewURL = self::$folderPentasDes .'/'. $randomString . '.'. $extension;
    //             if (copy($file, $des)) {
    //                 header('Content-Type: application/json');
    //                 echo json_encode(['status'=>'success','data'=>"$previewURL"]);
    //                 exit();
    //                 // $startTime = time();
    //                 // $timeout = 5;
    //                 // while (true) {
    //                 //     if (time() - $startTime >= $timeout) {
    //                 //         unlink($des);
    //                 //         exit();
    //                 //     }
    //                 // }
    //             } else {
    //                 throw new Exception('Sistem error !');
    //             }
    //         } else {
    //             throw new Exception('File tidak ditemukan !');
    //         }
    //     }catch(Exception $e){
    //         $error = $e->getMessage();
    //         $errorJson = json_decode($error, true);
    //         if ($errorJson === null) {
    //             $responseData = array(
    //                 'status' => 'error',
    //                 'message' => $error,
    //             );
    //         }else{
    //             $responseData = array(
    //                 'status' => 'error',
    //                 'message' => $errorJson['message'],
    //             );
    //         }
    //         header('Content-Type: application/json');
    //         isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
    //         echo json_encode($responseData);
    //         exit();
    //     }
    // }
    public function previewSewa($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_sewa']) || empty($data['id_sewa'])){
                throw new Exception('ID sewa tempat harus di isi !');
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
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
                throw new Exception('User tidak ditemukan !');
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                throw new Exception('Anda bukan admin !');
            }
            //check id_sewa
            if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_ket_sewa FROM sewa_tempat WHERE id_sewa = ? LIMIT 1";
                $file = self::$folderSewa;
            }else{
                throw new Exception('Deskripsi invalid !');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_sewa']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data sewa tempat tidak ditemukan !');
            }
            $stmt[0]->close();
            $file = $file.$path;
            //download file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderTempatDes)) {
                    mkdir(__DIR__.self::$folderTempatDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderTempatDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderTempatDes .'/'. $randomString . '.'. $extension;
                if (copy($file, $des)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','data'=>"$previewURL"]);
                    exit();
                    // $startTime = time();
                    // $timeout = 5;
                    // while (true) {
                    //     if (time() - $startTime >= $timeout) {
                    //         unlink($des);
                    //         exit();
                    //     }
                    // }
                } else {
                    throw new Exception('Sistem error !');
                }
            } else {
                throw new Exception('File tidak ditemukan !');
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
    public function previewTempat($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                throw new Exception('Email harus di isi !');
            }
            if(!isset($data['id_tempat']) || empty($data['id_tempat'])){
                throw new Exception('ID Tempat harus di isi !');
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
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
                throw new Exception('User tidak ditemukan !');
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                throw new Exception('Anda bukan admin !');
            }
            //check id_tempat
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT foto_tempat FROM list_tempat WHERE id_tempat = ? LIMIT 1";
                $file = self::$folderTempat;
            }else{
                throw new Exception('Deskripsi invalid !');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_tempat']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception('Data List tempat tidak ditemukan !');
            }
            $stmt[0]->close();
            $file = $file.$path;
            //download file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderTempatDes)) {
                    mkdir(__DIR__.self::$folderTempatDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderTempatDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderTempatDes .'/'. $randomString . '.'. $extension;
                if (copy($file, $des)) {
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','data'=>"$previewURL"]);
                    exit();
                    // $startTime = time();
                    // $timeout = 5;
                    // while (true) {
                    //     if (time() - $startTime >= $timeout) {
                    //         unlink($des);
                    //         exit();
                    //     }
                    // }
                } else {
                    throw new Exception('Sistem error !');
                }
            } else {
                throw new Exception('File tidak ditemukan !');
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
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } elseif ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unsupported content type']);
            exit();
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $download = new Preview();
    $data = Preview::handle();
    if(!isset($data['item']) || empty($data['item'])){
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Item harus di isi']);
        exit();
    }else{
        if($data['item'] == 'seniman'){
            $download->previewSeniman($data);
        }else if($data['item'] == 'perpanjangan'){
            $download->previewPerpanjangan($data);
        }else if($data['item'] == 'sewa'){
            $download->previewSewa($data);
        }else if($data['item'] == 'tempat'){
            $download->previewTempat($data);
        }else if($data['item'] == 'pentas'){
            $download->previewPentas($data);
        }else if($data['item'] == 'event'){
            $download->previewEvent($data);
        }else{
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Item invalid']);
            exit();
        }
    }
}
?>