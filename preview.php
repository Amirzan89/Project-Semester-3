<?php
require_once(__DIR__ . '/web/koneksi.php');
class Preview{
    private static $sizeFile = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderEvent = __DIR__.'/public/img/event';
    private static $folderEventDes = '/event/preview';
    private static $folderSeniman = __DIR__.'/private/seniman';
    private static $folderSenimanDes = '/seniman/preview';
    private static $folderSewa = __DIR__.'/private/tempat';
    private static $folderTempat = __DIR__.'/public/img/tempat';
    private static $folderTempatDes = '/tempat/preview';
    private static $folderPentas = __DIR__.'/private/pentas';
    private static $folderPentasDes = '/pentas/preview';
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
                echo "<script>alert('Email harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_event']) || empty($data['id_event'])){
                echo "<script>alert('ID Seniman harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                echo "<script>alert('Deskripsi harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('User tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda bukan admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_event
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT poster_event FROM events INNER JOIN detail_events ON events.id_detail = detail_events.id_detail WHERE id_event = ? LIMIT 1";
                $file = self::$folderEvent;
            }else{
                echo "<script>alert('Deskripsi invalid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_event']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Data event tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                    // exit();
                    // header("Location: $previewURL");
                    // flush();
                    // exit();
                    $startTime = time();
                    $timeout = 5;
                    while (true) {
                        if (time() - $startTime >= $timeout) {
                            unlink($des);
                            exit();
                        }
                    }
                } else {
                    echo "<script>alert('Sistem error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
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
                echo "<script>alert('Email harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                echo "<script>alert('ID Seniman harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                echo "<script>alert('Deskripsi harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('User tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda bukan admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_seniman
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT pass_foto FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/pass_foto';
            }else if($data['deskripsi'] == 'ktp'){
                $query = "SELECT ktp_seniman FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/ktp';
            }else if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
                $file = self::$folderSeniman.'/surat_keterangan';
            }else{
                echo "<script>alert('Deskripsi invalid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Data seniman tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                    // $startTime = time();
                    // $timeout = 5;
                    // while (true) {
                    //     if (time() - $startTime >= $timeout) {
                            // unlink($des);
                            // exit();
                    //     }
                    // }
                } else {
                    echo "<script>alert('Sistem error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
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
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function previewPentas($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                echo "<script>alert('Email harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_pentas']) || empty($data['id_pentas'])){
                echo "<script>alert('ID Pentas harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                echo "<script>alert('Deskripsi harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('User tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda bukan admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_advis
            if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_keterangan FROM surat_advis WHERE id_advis = ? LIMIT 1";
                $file = self::$folderPentas;
            }else{
                echo "<script>alert('Deskripsi invalid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_pentas']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Data Pentas tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            $file = $file.$path;
            //download file
            if (file_exists($file)) {
                $randomString = bin2hex(random_bytes(16));
                //buat folder
                if (!is_dir(__DIR__.self::$folderPentasDes)) {
                    mkdir(__DIR__.self::$folderPentasDes, 0777, true);
                }
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $des = __DIR__ . self::$folderPentasDes .'/'. $randomString . '.'. $extension;
                $previewURL = self::$folderPentasDes .'/'. $randomString . '.'. $extension;
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
                    echo "<script>alert('Sistem error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
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
                    'message' => $errorJson['message'],
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function previewSewa($data){
        try{
            if(!isset($data['email']) || empty($data['email'])){
                echo "<script>alert('Email harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_sewa']) || empty($data['id_sewa'])){
                echo "<script>alert('ID Sewa harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                echo "<script>alert('Deskripsi harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('User tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda bukan admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_sewa
            if($data['deskripsi'] == 'surat'){
                $query = "SELECT surat_ket_sewa FROM sewa_tempat WHERE id_sewa = ? LIMIT 1";
                $file = self::$folderSewa.'/surat_keterangan';
            }else{
                echo "<script>alert('Deskripsi invalid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_sewa']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Data Sewa Tempat tidak ditemukan ')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                    echo "<script>alert('Sistem error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
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
                echo "<script>alert('Email harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_tempat']) || empty($data['id_tempat'])){
                echo "<script>alert('ID Tempat harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['deskripsi']) || empty($data['deskripsi'])){
                echo "<script>alert('Deskripsi harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('User tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda bukan admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_tempat
            if($data['deskripsi'] == 'foto'){
                $query = "SELECT foto_tempat FROM list_tempat WHERE id_tempat = ? LIMIT 1";
                $file = self::$folderTempat;
            }else{
                echo "<script>alert('Deskripsi invalid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_tempat']);
            $stmt[0]->execute();
            $path = '';
            $stmt[0]->bind_result($path);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Data List tempat tidak ditemukan ')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                    echo "<script>alert('Sistem error')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
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
        echo "<script>alert('Item harus di isi !')</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }else{
        if($data['item'] == 'seniman'){
            $download->previewSeniman($data);
        }else if($data['item'] == 'sewa'){
            $download->previewSewa($data);
        }else if($data['item'] == 'tempat'){
            $download->previewTempat($data);
        }else if($data['item'] == 'pentas'){
            $download->previewPentas($data);
        }else if($data['item'] == 'event'){
            $download->previewEvent($data);
        }else{
            echo "<script>alert('Invalid item')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
}
?>