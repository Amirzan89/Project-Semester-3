<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanWebsite{
    private static $database;
    private static $con;
    private static $folderPath;
    private static $jsonPath = __DIR__."/../../kategori_seniman.json";
    private static $constID = '411.302';
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
    }
    private function kategoriFile($data,$desc){
        try{
            $fileExist = file_exists(self::$jsonPath);
            if (!$fileExist) {
                //if file is delete will make new json file
                $query = "SELECT * FROM kategori_seniman";
                $stmt[0] = self::$con->prepare($query);
                if(!$stmt[0]->execute()){
                    $stmt[0]->close();
                    throw new Exception('Data kategori seniman tidak ditemukan');
                }
                $result = $stmt[0]->get_result();
                $kategoriData = [];
                while ($row = $result->fetch_assoc()) {
                    $kategoriData[] = $row;
                }
                $stmt[0]->close();
                if ($kategoriData === null) {
                    throw new Exception('Data kategori seniman tidak ditemukan');
                }
                $jsonData = json_encode($kategoriData, JSON_PRETTY_PRINT);
                if (!file_put_contents(self::$jsonPath, $jsonData)) {
                    echo "Gagal menyimpan file kategori json";
                }
            }
            if($desc == 'get'){
                //get kategori seniman
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $result = null;
                foreach($jsonData as $key => $item){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori_seniman']) {
                        $result = $jsonData[$key];
                    }
                }
                if($result === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $result;
            }else if($desc == 'tambah' && $fileExist == true){
                //tambah kategori seniman
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $new[$data['id_kategori_seniman']] = $data;
                $jsonData = array_merge($jsonData, $new);
                $jsonFile = json_encode($jsonData, JSON_PRETTY_PRINT);
                file_put_contents(self::$jsonPath, $jsonFile);
            }else if($desc == 'update'){
                //update kategori seniman
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                foreach($jsonData as $key => $item){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori_seniman']) {
                        $jsonData[$key] = $data;
                    }
                }
                $jsonData = array_values($jsonData);
                $jsonFile = json_encode($jsonData, JSON_PRETTY_PRINT);
                file_put_contents(self::$jsonPath, $jsonFile);
            }else if($desc == 'hapus'){
                //hapus kategori seniman
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                foreach($jsonData as $key => $item){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori_seniman']) {
                        unset($jsonData[$key]);
                    }
                }
                $jsonData = array_values($jsonData);
                $json = json_encode($jsonData, JSON_PRETTY_PRINT);
                file_put_contents(self::$jsonPath, $json);
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
    public static function getSeniman($data){
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
            if(($role != 'admin seniman' && $role != 'super admin') || $role == 'masyarakat'){
                throw new Exception('Invalid role');
            }
            //check and get data
            if($data['tanggal'] == 'semua'){
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM seniman WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM seniman WHERE status = 'ditolak' OR status = 'diterima' ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'data'){
                    if(!isset($data['kategori']) || empty($data['kategori'])){
                        throw new Exception('Kategori Seniman harus di isi !');
                    }
                    if($data['kategori'] == 'semua'){
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' ORDER BY id_seniman DESC";
                    }else{
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND seniman.id_kategori_seniman = ".$data['kategori']." ORDER BY id_seniman DESC";
                    }
                }else{
                    throw new Exception('Deskripsi invalid !');
                }
                $stmt[1] = self::$con->prepare($query);
            }else{
                if($data['desc'] == 'pengajuan'){
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM seniman WHERE (status = 'diajukan' OR status = 'proses') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM seniman WHERE (status = 'ditolak' OR status = 'diterima') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'data'){
                    if(!isset($data['kategori']) || empty($data['kategori'])){
                        throw new Exception('Kategori Seniman harus di isi !');
                    }
                    if($data['kategori'] == 'semua'){
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                    }else{
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND seniman.id_kategori_seniman = ".$data['kategori']." AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                    }
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
                throw new Exception('Data seniman tidak ditemukan');
            }
            $result = $stmt[1]->get_result();
            $eventsData = array();
            while ($row = $result->fetch_assoc()) {
                $eventsData[] = $row;
            }
            $stmt[1]->close();
            if ($eventsData === null) {
                throw new Exception('Data seniman tidak ditemukan');
            }
            if (empty($eventsData)) {
                throw new Exception('Data seniman tidak ditemukan');
            }
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data seniman berhasil didapatkan', 'data' => $eventsData]);
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
    public function tambahKategori($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['nama_kategori']) || empty($data['nama_kategori'])){
                throw new Exception('Kategori seniman harus di isi !');
            }
            if (strlen($data['nama_kategori']) > 50) {
                throw new Exception('Kategori seniman maksimal 50 huruf');
            }
            if(!isset($data['singkatan']) || empty($data['singkatan'])){
                throw new Exception('Singkatan kategori harus di isi !');
            }
            if (strlen($data['singkatan']) > 10) {
                throw new Exception('Singkatan kategori maksimal 10 huruf');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
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
            if($role != 'super admin' && $role != 'admin seniman'){
                throw new Exception('Anda bukan admin');
            }
            $query = "INSERT INTO kategori_seniman (nama_kategori, singkatan_kategori) VALUES (?, ?)";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("ss",$data['nama_kategori'], $data['singkatan']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                //update file
                $insertedId = self::$con->insert_id;
                $selectQuery = "SELECT * FROM kategori_seniman WHERE id_kategori_seniman = ?";
                $stmt[2] = self::$con->prepare($selectQuery);
                $stmt[2]->bind_param("i", $insertedId);
                $stmt[2]->execute();
                $result = $stmt[2]->get_result();
                $kategoriData = $result->fetch_assoc();
                $this->kategoriFile($kategoriData,'tambah');
                echo json_encode(['status'=>'success','message'=>'Data Kategori Seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Kategori Seniman gagal ditambahkan','code'=>500]));
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
    public function ubahKategori($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                throw new Exception('ID Kategori seniman harus di isi !');
            }
            if(!isset($data['nama_kategori']) || empty($data['nama_kategori'])){
                throw new Exception('Kategori seniman harus di isi !');
            }
            if (strlen($data['nama_kategori']) > 50) {
                throw new Exception('Kategori seniman maksimal 50 huruf');
            }
            if(!isset($data['singkatan']) || empty($data['singkatan'])){
                throw new Exception('Singkatan kategori harus di isi !');
            }
            if (strlen($data['singkatan']) > 10) {
                throw new Exception('Singkatan kategori maksimal 10 huruf');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
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
            if($role != 'super admin' && $role != 'admin seniman'){
                throw new Exception('Anda bukan admin');
            }
            $query = "UPDATE kategori_seniman SET nama_kategori = ?, singkatan_kategori = ? WHERE id_kategori_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("sss", $data['nama_kategori'], $data['singkatan'], $data['id_kategori']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                $kategori = [
                    "id_kategori_seniman"=>$data['id_kategori'],
                    "nama_kategori"=>$data['nama_kategori'],
                    "singkatan"=>$data['singkatan']
                ];
                $this->kategoriFile($kategori,'update');
                echo json_encode(['status'=>'success','message'=>'Data Kategori Seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Kategori Seniman gagal diubah','code'=>500]));
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
    public function hapusKategori($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                throw new Exception('Kategori seniman harus di isi !');
            }
            //check id_user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
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
            if($role != 'super admin' && $role != 'admin seniman'){
                throw new Exception('Anda bukan admin');
            }
            //delete data
            $query = "DELETE FROM kategori_seniman WHERE id_kategori_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_kategori']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                $this->kategoriFile(['id_kategori_seniman'=>$data['id_kategori']],'hapus');
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Kategori Seniman berhasil dihapus']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Kategori Seniman gagal dihapus','code'=>500]));
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
    // private function generateInpNIS($data){
    //     try{
    //         if(!isset($data['kategori']) || empty($data['kategori'])){
    //             throw new Exception('Kategori harus di isi');
    //         }
    //         if (array_key_exists($data['kategori'], self::$kategoriInp)) {
    //             $kategori = self::$kategoriInp[$data['kategori']];
    //         } else {
    //             throw new Exception('Kategori invalid');
    //         }
    //         //get last kategori
    //         $query = "SELECT COUNT(*) AS total FROM seniman WHERE KATEGORI = '$kategori'";
    //         $stmt[0] = self::$con->prepare($query);
    //         $stmt[0]->execute();
    //         $total = 0;
    //         $stmt[0]->bind_result($total);
    //         if(!$stmt[0]->fetch()){
    //             $total = 1;
    //         }else{
    //             $total++;
    //         }
    //         $stmt[0]->close();
    //         date_default_timezone_set('Asia/Jakarta');
    //         $total = str_pad($total, 3, '0', STR_PAD_LEFT);
    //         $nis = $kategori.'/'.$total.'/'.self::$constID.'/'.date('Y');
    //         return ['nis'=>$nis,'kategori'=>$kategori];
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
    //         isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
    //         echo json_encode($responseData);
    //         exit();
    //     }
    // }
    private function generateNIS($data,$desc){
        try{
            if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                throw new Exception('ID Kategori harus di isi');
            }
            $kategoriData = $this->kategoriFile(['id_kategori_seniman'=>$data['id_kategori']],'get');
            //get last NIS
            date_default_timezone_set('Asia/Jakarta');
            if($desc == 'diterima'){
                $query = "SELECT COUNT(*) AS total FROM seniman WHERE nomor_induk LIKE '%/".date('Y')."' AND id_kategori_seniman = '".$data['id_kategori']."'";
            }else if($desc == 'perpanjangan'){
                $query = "SELECT COUNT(*) AS total FROM seniman WHERE nomor_induk LIKE '%/".(date('Y')+1)."' AND id_kategori_seniman = '".$data['id_kategori']."'";
            }else{
                throw new Exception('Description invalid');
            }
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->execute();
            $total = 0;
            $stmt[0]->bind_result($total);
            if(!$stmt[0]->fetch()){
                $total = 1;
            }else{
                $total++;
            }
            $stmt[0]->close();
            $total = str_pad($total, 3, '0', STR_PAD_LEFT);
            if($desc == 'diterima'){
                $nis = $kategoriData['singkatan'].'/'.$total.'/'.self::$constID.'/'.date('Y');
            }else if($desc == 'perpanjangan'){
                $nis = $kategoriData['singkatan'].'/'.$total.'/'.self::$constID.'/'.(date('Y')+1);
            }
            return ['nis'=>$nis,'kategori'=>$data['id_kategori']];
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
    public function prosesSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                http_response_code(400);
                echo "<script>alert('ID User harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                http_response_code(400);
                echo "<script>alert('ID Seniman harus di isi !')</script>";
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
            //check id seniman
            $query = "SELECT status, id_kategori_seniman FROM seniman WHERE id_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $statusDB = '';
            $idKategori = '';
            $stmt[1]->bind_result($statusDB, $idKategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Seniman tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //check status seniman
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
            if($data['keterangan'] == 'proses'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $redirect = '/pengajuan.php';
                $status = 'proses';
                $query = "UPDATE seniman SET status = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("si", $status, $data['id_seniman']);
            }else if($data['keterangan'] == 'diterima'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $redirect = '/pengajuan.php';
                $status = 'diterima';
                $query = "UPDATE seniman SET nomor_induk = ?, status = ? WHERE id_seniman = ?";
                $nomorInduk = $this->generateNIS(['id_kategori'=>$idKategori],'diterima');
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ssi", $nomorInduk['nis'], $status, $data['id_seniman']);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/pengajuan.php';
                $status = 'ditolak';
                $query = "UPDATE seniman SET status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ssi", $status, $data['catatan'], $data['id_seniman']);
            }
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                echo "<script>alert('Status berhasil diubah')</script>";
                echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                exit();
            } else {
                $stmt[2]->close();
                echo "<script>alert('Status gagal diubah')</script>";
                echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
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
            http_response_code(400);
            echo "<script>alert('$error')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
    public function prosesPerpanjangan($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                echo "<script>alert('ID User harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                echo "<script>alert('ID Seniman harus di isi !')</script>";
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
            //check id seniman
            if($data['keterangan'] == 'diterima'){
                $query = "SELECT nomor_induk, status, id_kategori_seniman FROM seniman WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_seniman']);
                $stmt[1]->execute();
                $nomorIndukDB = '';
                $statusDB = '';
                $idKategori = '';
                $stmt[1]->bind_result($nomorIndukDB, $statusDB, $idKategori);
            }else{
                $query = "SELECT status FROM seniman WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_seniman']);
                $stmt[1]->execute();
                $statusDB = '';
                $stmt[1]->bind_result($statusDB);
            }
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Seniman tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //check status seniman
            if($statusDB == 'diajukan'){
                echo "<script>alert('Data Seniman sedan diajukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($statusDB == 'proses'){
                echo "<script>alert('Data seniman sedang di proses')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($statusDB == 'ditolak'){
                echo "<script>alert('Data seniman ditolak mohon cek kembali')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check perpanjangan
            $query = "SELECT status FROM perpanjangan WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_seniman']);
            $stmt[2]->execute();
            $statusPDB = '';
            $stmt[2]->bind_result($statusPDB);
            if(!$stmt[2]->fetch()){
                $stmt[2]->close();
                echo "<script>alert('Data perpanjangan tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check status perpanjangan
            if($data['keterangan'] ==  'proses' && ($statusPDB == 'diterima' || $statusPDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($statusPDB ==  'diajukan' && ($data['keterangan'] == 'diterima' || $data['keterangan'] == 'ditolak')){
                echo "<script>alert('Data harus di proses')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'ditolak' && $statusPDB == 'diterima'){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && $statusPDB == 'ditolak'){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //update data
            $redirect = '/perpanjangan.php';
            if($data['keterangan'] == 'proses'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $status = 'proses';
                $query = "UPDATE perpanjangan SET status = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("si", $status, $data['id_seniman']);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $status = 'ditolak';
                $query = "UPDATE perpanjangan SET status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ssi", $status, $data['catatan'], $data['id_seniman']);
            }else{
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo "<script>alert('Status berhasil diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                } else {
                    $stmt[2]->close();
                    echo "<script>alert('Status gagal diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                }
            }
            if($data['keterangan'] == 'diterima'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                //tambah histori
                $query = "INSERT INTO histori_nis (nis, tahun, id_seniman) VALUES (?, ?, ?)";
                $stmt[2] = self::$con->prepare($query);
                $tahun = explode("/", $nomorIndukDB);
                $tahun = end($tahun);
                $stmt[2]->bind_param("sss", $nomorIndukDB, $tahun, $data['id_seniman']);
                $stmt[2]->execute();
                if (!$stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo "<script>alert('Error tambah data histori nomor induk !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                //hapus data perpanjangan
                $query = "DELETE FROM perpanjangan WHERE id_seniman = ?";
                $stmt[3] = self::$con->prepare($query);
                $stmt[3]->bind_param('s', $data['id_seniman']);
                if (!$stmt[3]->execute()) {
                    $stmt[3]->close();
                    echo "<script>alert('Error hapus data perpanjangan seniman')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $stmt[3]->close();
                //update nis 
                $query = "UPDATE seniman SET nomor_induk = ? WHERE id_seniman = ?";
                $nomorInduk = $this->generateNIS(['kategori'=>$idKategori],'perpanjangan');
                $stmt[4] = self::$con->prepare($query);
                $stmt[4]->bind_param("ssi", $nomorInduk['nis'], $status, $data['id_seniman']);
                $stmt[4]->execute();
                if (!$stmt[4]->affected_rows > 0) {
                    $stmt[4]->close();
                    echo "<script>alert('Status gagal diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                }
                $stmt[4]->close();
                $redirect = '/perpanjangan.php';
                echo "<script>alert('Status berhasil diubah')</script>";
                echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
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
            http_response_code(400);
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
    include(__DIR__.'/../../notfound.php');
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $senimanWeb = new SenimanWebsite();
    $data = SenimanWebsite::handle();
    if(isset($data['_method'])){
        if($data['_method'] == 'PUT'){
            if(isset($data['desc']) && !empty($data['desc']) && !is_null($data['desc'])){
                if($data['desc'] == 'kategori'){
                    $senimanWeb->ubahKategori($data);
                }
                if($data['desc'] == 'perpanjangan'){
                    $senimanWeb->prosesPerpanjangan($data);
                }
            }
            if(isset($data['keterangan'])){
                $senimanWeb->prosesSeniman($data);
            }
        }else if($data['_method'] == 'DELETE'){
            if(isset($data['desc']) && !empty($data['desc']) && !is_null($data['desc']) && $data['desc'] == 'kategori'){
                $senimanWeb->hapusKategori($data);
            }
        }
    }
    if(isset($data['desc']) && !empty($data['desc']) && !is_null($data['desc'])){
        if($data['desc'] == 'kategori'){
            $senimanWeb->tambahKategori($data);
        }
        if($data['desc'] == 'pengajuan' || $data['desc'] == 'riwayat' || $data['desc'] == 'data'){
            $senimanWeb->getSeniman($data);
        }
    } 
}
?>