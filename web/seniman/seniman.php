<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanWebsite{
    private static $database;
    private static $con;
    private static $folderPath;
    private static $constID = '411.302';
    private static $kategoriInp = [
        'campursari'=>'CAMP',
        'dalang'=>'DLG',
        'jaranan'=>'JKP',
        'karawitan'=>'KRW',
        'mc'=>'MC',
        'ludruk'=>'LDR',
        'organisasi kesenian musik'=>'OKM',
        'organisasi'=>'ORG',
        'pramugari tayup'=>'PRAM',
        'sanggar'=>'SGR',
        'sinden'=>'SIND',
        'vocalis'=>'VOC',
        'waranggono'=>'WAR',
        'barongsai'=>'BAR',
        'ketoprak'=>'KTR',
        'pataji'=>'PTJ',
        'reog'=>'REOG',
        'taman hiburan rakyat'=>'THR',
        'pelawak'=>'PLWK'
    ];
    private static $kategori = [
        'CAMP',
        'DLG',
        'JKP',
        'KRW',
        'MC',
        'LDR',
        'OKM',
        'ORG',
        'PRAM',
        'SGR',
        'SIND',
        'VOC',
        'WAR',
        'BAR',
        'KTR',
        'PTJ',
        'REOG',
        'THR',
        'PLWK'
    ];
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
    }
    public static function getData($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('id user harus di isi');
            }
            if($data['role'] != 'admin seniman' || $data['role'] == 'super admin'){
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
            $query = "INSERT INTO kategori_seniman (nama_kategori, singkatan) VALUES (?, ?)";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("ss",$data['nama_kategori'], $data['singkatan']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'Data kategori seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data kategori seniman gagal ditambahkan','code'=>500]));
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
            $query = "UPDATE seniman SET nama_kategori = ?, singkatan = ? WHERE id_kategori_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("sss", $data['nama_kategori'],$data['id_seniman']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal diubah','code'=>500]));
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
            $query = "DELETE FROM kategori_seniman WHERE id_kategori = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_kategori']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dihapus']);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal dihapus','code'=>500]));
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
            if(!isset($data['kategori']) || empty($data['kategori'])){
                throw new Exception('Kategori harus di isi');
            }
            if (!in_array($data['kategori'], self::$kategori)) {
                throw new Exception('Kategori invalid');
            }
            //get last NIS
            date_default_timezone_set('Asia/Jakarta');
            if($desc == 'tambah'){
                $query = "SELECT COUNT(*) AS total FROM seniman WHERE nomor_induk LIKE '%/".date('Y')."' AND kategori = '".$data['kategori']."'";
            }else if($desc == 'perpanjangan'){
                $query = "SELECT COUNT(*) AS total FROM seniman WHERE nomor_induk LIKE '%/".(date('Y')+1)."' AND kategori = '".$data['kategori']."'";
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
            if($desc == 'tambah'){
                $nis = $data['kategori'].'/'.$total.'/'.self::$constID.'/'.date('Y');
            }else if($desc == 'perpanjangan'){
                $nis = $data['kategori'].'/'.$total.'/'.self::$constID.'/'.(date('Y')+1);
            }
            return ['nis'=>$nis,'kategori'=>$data['kategori']];
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
            $query = "SELECT status, kategori FROM seniman WHERE id_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $statusDB = '';
            $kategori = '';
            $stmt[1]->bind_result($statusDB, $kategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Seniman tidak ditemukan')</script>";
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
                $nomorInduk = $this->generateNIS(['kategori'=>$kategori],'tambah');
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
                $query = "SELECT status, kategori FROM seniman WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_seniman']);
                $stmt[1]->execute();
                $statusDB = '';
                $kategori = '';
                $stmt[1]->bind_result($statusDB, $kategori);
            }else{
                $query = "SELECT nomor_induk, status, kategori FROM seniman WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_seniman']);
                $stmt[1]->execute();
                $nomor = '';
                $statusDB = '';
                $kategori = '';
                $stmt[1]->bind_result($nomor, $statusDB, $kategori);
            }
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Seniman tidak ditemukan')</script>";
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
            if($data['keterangan'] == 'proses'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $redirect = '/perpanjangan.php';
                $status = 'proses';
                $query = "UPDATE seniman SET status = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("si", $status, $data['id_seniman']);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/perpanjangan.php';
                $status = 'ditolak';
                $query = "UPDATE seniman SET status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ssi", $status, $data['catatan'], $data['id_seniman']);
            }
            if($data['keterangan'] == 'diterima'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                //tambah histori
                $query = "INSERT INTO histori_nis (nis, tahun, id_seniman) VALUES (?, ?, ?)";
                $stmt[2] = self::$con->prepare($query);
                $status = 'diajukan';
                $tahun = explode("/", $nomor);
                $tahun = end($tahun);
                $stmt[2]->bind_param("sss", $nomor, $tahun, $data['id_seniman']);
                $stmt[2]->execute();
                if (!$stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/perpanjangan.php';
                $status = 'diterima';
                $query = "UPDATE perpanjangan SET nomor_induk = ?, status = ? WHERE id_seniman = ?";
                $nomorInduk = $this->generateNIS(['kategori'=>$kategori],'perpanjangan');
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param("ssi", $nomorInduk, $status, $data['id_seniman']);
                $stmt[2]->execute();
                if ($stmt[2]->affected_rows > 0) {
                    $stmt[2]->close();
                    //delete perpanjangan
                    $query = "DELETE FROM perpanjangan WHERE id_seniman = ?";
                    $stmt[3] = self::$con->prepare($query);
                    $stmt[3]->bind_param('s', $data['id_seniman']);
                    if ($stmt[3]->execute()) {
                        $stmt[3]->close();
                        echo "<script>alert('Status berhasil diubah')</script>";
                        echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                        exit();
                    } else {
                        $stmt[3]->close();
                        echo "<script>alert('Status gagal diubah')</script>";
                        echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                        exit();
                    }
                } else {
                    $stmt[2]->close();
                }
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
    echo 'ilang';
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $senimanWeb = new SenimanWebsite();
    $data = SenimanWebsite::handle();
    if(isset($data['desc']) && $data['desc'] == 'kategori' && !empty($data['desc']) && !is_null($data['desc'])){
        $senimanWeb->tambahKategori($data);
    }
    if(isset($data['_method'])){
        if($data['_method'] == 'PUT'){
            if(isset($data['desc']) && $data['desc'] == 'kategori' && !empty($data['kategori']) && !is_null($data['kategori'])){
                $senimanWeb->ubahKategori($data);
            }
            if(isset($data['keterangan'])){
                $senimanWeb->prosesSeniman($data);
            }
        }
    }
}
?>