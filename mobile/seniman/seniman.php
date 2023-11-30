<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanMobile{
    private static $sizeFile = 5 * 1024 * 1024;
    private static $sizeImg = 4 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    private static $perpanjanganPath;
    private static $jsonPath = __DIR__."/../../kategori_seniman.json";
    private static $senimanFile = __DIR__.'/../../private/seniman/file.json';
    private static $perpanjanganFile = __DIR__.'/../../private/perpanjangan/file.json';
    private static $constID = '411.302';
    public function __construct(){
        self::$database = koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../../private/seniman';
        self::$perpanjanganPath = __DIR__.'/../../private/perpanjangan';
    }
    private static function getBaseFileName($fileName) {
        preg_match('/^([^\(]+)(?:\((\d+)\))?(\.\w+)?$/', $fileName, $matches);
        if (isset($matches[1])) {
            $baseName = $matches[1];
            $number = isset($matches[2]) ? (int)$matches[2] : 0;
            return ['name' => $baseName, 'number' => $number];
        }
        return null;
    }
    private function manageFile($data, $desc, $opt){
        try{
            $filePath = '';
            if($opt['table'] == 'seniman'){
                $filePath = self::$senimanFile;
            }else if($opt['table'] == 'perpanjangan'){
                $filePath = self::$perpanjanganFile;
            }
            $fileExist = file_exists($filePath);
            if (!$fileExist || empty($fileExist) || is_null($fileExist)) {
                //if file is delete will make new json file
                if($opt['table'] == 'seniman'){
                    $query = "SELECT id_seniman, ktp_seniman, pass_foto, surat_keterangan FROM seniman";
                }else if($opt['table'] == 'perpanjangan'){
                    $query = "SELECT id_perpanjangan, ktp_seniman, pass_foto, surat_keterangan FROM perpanjangan";
                }
                $stmt[0] = self::$con->prepare($query);
                if(!$stmt[0]->execute()){
                    $stmt[0]->close();
                    throw new Exception('Data file tidak ditemukan');
                }
                $result = $stmt[0]->get_result();
                $fileData = [];
                while ($row = $result->fetch_assoc()) {
                    $fileData[] = $row;
                }
                $stmt[0]->close();
                if (!empty($fileData) && $fileData !== null) {
                    $jsonData = json_encode($fileData, JSON_PRETTY_PRINT);
                    if (!file_put_contents($filePath, $jsonData)) {
                        echo "Gagal menyimpan file sistem";
                    }
                }
            }
            if($desc == 'tambah'){
                //check if file exist
                if (!$fileExist) {
                    //if file is delete will make new json file
                    if($opt['table'] == 'seniman'){
                        $query = "SELECT id_seniman, ktp_seniman, pass_foto, surat_keterangan FROM seniman";
                    }else if($opt['table'] == 'perpanjangan'){
                        $query = "SELECT id_perpanjangan, ktp_seniman, pass_foto, surat_keterangan FROM perpanjangan";
                    }
                    $stmt[0] = self::$con->prepare($query);
                    if(!$stmt[0]->execute()){
                        $stmt[0]->close();
                        throw new Exception('Data file tidak ditemukan');
                    }
                    $result = $stmt[0]->get_result();
                    $fileData = [];
                    while ($row = $result->fetch_assoc()) {
                        $fileData[] = $row;
                    }
                    $stmt[0]->close();
                    if (!empty($fileData) && $fileData !== null) {
                        $jsonData = json_encode($fileData, JSON_PRETTY_PRINT);
                        if (!file_put_contents($filePath, $jsonData)) {
                            throw new Exception('Gagal menyimpan file sistem');
                        }
                    }
                }else{
                    //tambah data file
                    $jsonFile = file_get_contents($filePath);
                    $jsonData = json_decode($jsonFile, true);
                    array_push($jsonData, $data);
                    $jsonFile = json_encode($jsonData, JSON_PRETTY_PRINT);
                    file_put_contents($filePath, $jsonFile);
                }
            }else if($desc == 'get'){
                if(!isset($data['nama_file']) || empty($data['nama_file'])){
                    throw new Exception('Nama file harus di isi');
                }
                $jsonFile = file_get_contents($filePath);
                $jsonData = json_decode($jsonFile, true);
                $fileNameNew = $data['nama_file'];
                $fileData = array();
                if($opt['col'] == 'ktp'){
                    //get data
                    foreach($jsonData as $key => $item){
                        if (isset($item['ktp_seniman'])){
                            $file = self::getBaseFileName(pathinfo($item['ktp_seniman'])['filename']);
                            if($file['name'] == pathinfo($data['nama_file'])['filename']) {
                                array_push($fileData,['name'=>$file['name'],'number'=>$file['number']]);
                            }
                        }
                    }
                    //get number
                    $num = '';
                    if(is_null($fileData) || empty($fileData)){
                        $fileNameNew = $data['nama_file'];
                    }else{
                        foreach ($fileData as $file) {
                            if (isset($file['number']) && $file['number'] > $num) {
                                $num = $file['number'];
                            }
                        }
                        if(empty($num)){
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'(1).'.pathinfo($data['nama_file'])['extension'];
                        }else{
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'('.($num+1).').'.pathinfo($data['nama_file'])['extension'];
                        }
                    }
                }else if($opt['col'] == 'foto'){
                    foreach($jsonData as $key => $item){
                        if (isset($item['pass_foto'])){
                            $file = self::getBaseFileName(pathinfo($item['pass_foto'])['filename']);
                            if($file['name'] == pathinfo($data['nama_file'])['filename']) {
                                array_push($fileData,['name'=>$file['name'],'number'=>$file['number']]);
                            }
                        } 
                    }
                    //get number
                    $num = '';
                    if(is_null($fileData) || empty($fileData)){
                        $fileNameNew = $data['nama_file'];
                    }else{
                        foreach ($fileData as $file) {
                            if (isset($file['number']) && $file['number'] > $num) {
                                $num = $file['number'];
                            }
                        }
                        if(empty($num)){
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'(1).'.pathinfo($data['nama_file'])['extension'];
                        }else{
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'('.($num+1).').'.pathinfo($data['nama_file'])['extension'];
                        }
                    }
                }else if($opt['col'] == 'surat'){
                    foreach($jsonData as $key => $item){
                        if (isset($item['surat_keterangan'])){
                            $file = self::getBaseFileName(pathinfo($item['surat_keterangan'])['filename']);
                            if($file['name'] == pathinfo($data['nama_file'])['filename']) {
                                array_push($fileData,['name'=>$file['name'],'number'=>$file['number']]);
                            }
                        }
                    }
                    //get number
                    $num = '';
                    if(is_null($fileData) || empty($fileData)){
                        $fileNameNew = $data['nama_file'];
                    }else{
                        foreach ($fileData as $file) {
                            if (isset($file['number']) && $file['number'] > $num) {
                                $num = $file['number'];
                            }
                        }
                        if(empty($num)){
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'(1).'.pathinfo($data['nama_file'])['extension'];
                        }else{
                            $fileNameNew = pathinfo($data['nama_file'])['filename'].'('.($num+1).').'.pathinfo($data['nama_file'])['extension'];
                        }
                    }
                }
                return '/'.$fileNameNew;
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
    public function kategori($data, $desc){
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
                    throw new Exception('Gagal menyimpan file sistem');
                }
            }
            if($desc == 'check'){
                if(!isset($data['kategori']) || empty($data['kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $result = null;
                foreach($jsonData as $key => $item){
                    if (isset($item['singkatan']) && $item['singkatan'] == $data['kategori']) {
                        $result = $jsonData[$key]['id_kategori_seniman'];
                    }
                }
                if($result === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $result;
            }else if($desc == 'get'){
                if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $result = null;
                foreach($jsonData as $key => $item){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori']) {
                        $result = $jsonData[$key]['nama_kategori'];
                    }
                }
                if($result === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $result;
            }else if($desc == 'get nama'){
                if(!isset($data['NamaKategori']) || empty($data['NamaKategori'])){
                    throw new Exception('Kategori harus di isi');
                }
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $result = null;
                foreach($jsonData as $key => $item){
                    if (isset($item['nama_kategori']) && $item['nama_kategori'] == $data['NamaKategori']) {
                        $result = $jsonData[$key];
                    }
                }
                if($result === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $result;
            }else if($desc == 'get all'){
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                if($jsonData === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $jsonData;
            }else if($desc == 'getINI'){
                if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                    throw new Exception('Kategori harus di isi');
                }
                $jsonFile = file_get_contents(self::$jsonPath);
                $jsonData = json_decode($jsonFile, true);
                $result = null;
                foreach($jsonData as $key => $item){
                    if (isset($item['id_kategori_seniman']) && $item['id_kategori_seniman'] == $data['id_kategori']) {
                        $result = $jsonData[$key]['singkatan'];
                    }
                }
                if($result === null){
                    throw new Exception('Data kategori tidak ditemukan');
                }
                return $result;
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
    public function getPerpanjangan($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID seniman harus di isi !');
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
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check id_seniman
            $query = "SELECT id_kategori_seniman FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $idKategori = '';
            $stmt[1]->bind_result($idKategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data Seniman tidak ditemukan');
            }
            $stmt[1]->close();
            //get data
            if(isset($data['desc'])){
                if($data['desc'] == 'diajukan'){
                    $status = 'diajukan';
                }else if($data['desc'] == 'proses'){
                    $status = 'proses';
                }else if($data['desc'] == 'ditolak'){
                    $status = 'ditolak';
                }else if($data['desc'] == 'diterima'){
                    $status = 'diterima';
                }
                $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_user = ? AND status = '$status' ORDER BY tgl_pembuatan";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param('s', $data['id_user']);
                if ($stmt[2]->execute()) {
                    $result = $stmt[2]->get_result();
                    $senimanData = $result->fetch_assoc();
                    $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                    $stmt[2]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]);
                    exit();
                }else{
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                }
                if($data['desc'] == 'getSeniman'){
                    $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_user = ? ";
                    $stmt[2] = self::$con->prepare($query);
                    $stmt[2]->bind_param('s', $data['id_user']);
                    if ($stmt[2]->execute()) {
                        $result = $stmt[2]->get_result();
                        $senimanData = $result->fetch_assoc();
                        $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                        $stmt[2]->close();
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]);
                        exit();
                    }else{
                        $stmt[2]->close();
                        throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                    }
                    
                }
            }else{
                $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param('s', $data['id_seniman']);
                if ($stmt[2]->execute()) {
                    $result = $stmt[2]->get_result();
                    $senimanData = $result->fetch_assoc();
                    $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                    $stmt[2]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]);
                    exit();
                }else{
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                }
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['pesan'],
                    'kode'=>2,
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function getSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi !');
            }
            if(!isset($data['desc']) || !in_array($data['desc'],['getSeniman','diajukan','proses','ditolak','diterima'])){
                if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                    throw new Exception('ID seniman harus di isi !');
                }
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
            if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admin seniman'])){
                throw new Exception('Harus masyarakat');
            }
            //check seniman
            if(isset($data['desc']) || in_array($data['desc'],['getSeniman','diajukan','proses','ditolak','diterima'])){
                $query = "SELECT id_kategori_seniman FROM seniman WHERE id_user = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_user']);
            }else{
                $query = "SELECT id_kategori_seniman FROM seniman WHERE id_seniman = ? LIMIT 1";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param('s', $data['id_seniman']);
            }
            $stmt[1]->execute();
            $idKategori = '';
            $stmt[1]->bind_result($idKategori);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                throw new Exception('Data Seniman tidak ditemukan');
            }
            $stmt[1]->close();
            //get data
            if(!isset($data['desc'])){
                if($data['desc'] == 'diajukan'){
                    $status = 'diajukan';
                }else if($data['desc'] == 'proses'){
                    $status = 'proses';
                }else if($data['desc'] == 'ditolak'){
                    $status = 'ditolak';
                }else if($data['desc'] == 'diterima'){
                    $status = 'diterima';
                }
                $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_user = ? AND status = '$status' ORDER BY tgl_pembuatan DESC";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param('s', $data['id_user']);
                if ($stmt[2]->execute()) {
                    $result = $stmt[2]->get_result();
                    $senimanData = $result->fetch_assoc();
                    $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                    $stmt[2]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]);
                    exit();
                }else{
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                }
                if($data['desc'] == 'getSeniman'){
                    $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_user = ? ";
                    $stmt[2] = self::$con->prepare($query);
                    $stmt[2]->bind_param('s', $data['id_user']);
                    if ($stmt[2]->execute()) {
                        $result = $stmt[2]->get_result();
                        $senimanData = $result->fetch_assoc();
                        $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                        $stmt[2]->close();
                        header('Content-Type: application/json');
                        echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]); 
                        exit();
                    }else{
                        $stmt[2]->close();
                        throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                    }
                    
                }
            }else{
                $query = "SELECT id_seniman, nik, nomor_induk, nama_seniman, jenis_kelamin, kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, catatan FROM seniman WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $stmt[2]->bind_param('s', $data['id_seniman']);
                if ($stmt[2]->execute()) {
                    $result = $stmt[2]->get_result();
                    $senimanData = $result->fetch_assoc();
                    $senimanData['kategori'] = $this->kategori(['id_kategori'=>$idKategori],'get');
                    $stmt[2]->close();
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success', 'pesan' => 'Data Seniman berhasil didapatkan', 'data' => $senimanData,'kode'=>1]);
                    exit();
                }else{
                    $stmt[2]->close();
                    throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal didapatkan','kode'=>0]));
                }
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['pesan'],
                    'kode'=>2,
                );
            }
            header('Content-Type: application/json');
            // isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function registrasiSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                throw new Exception('Nama seniman harus di isi');
            }
            if (!isset($data['nik']) || empty($data['nik'])) {
                throw new Exception('nik seniman harus di isi');
            }
            if(!is_numeric($data['nik'])){
                throw new Exception('Nik seniman harus angka !');
            }
            if (!isset($data['alamat_seniman']) || empty($data['alamat_seniman'])) {
                throw new Exception('Alamat harus di isi');
            }
            if (!isset($data['no_telpon']) || empty($data['no_telpon'])) {
                throw new Exception('Nomor telpon harus di isi');
            }
            if (strlen($data['no_telpon']) > 16) {
                throw new Exception('Nama event maksimal 16 karakter');
            }
            if (!isset($data['jenis_kelamin']) || empty($data['jenis_kelamin'])) {
                throw new Exception('Jenis kelamin harus di isi');
            }else if(!in_array($data['jenis_kelamin'],['laki-laki','perempuan'])){
                throw new Exception('Jenis kelamin salah');
            }
            $kategori = $this->kategori(['kategori'=>$data['singkatan_kategori']],'check');
            if (!isset($data['kecamatan']) || empty($data['kecamatan'])) {
                throw new Exception('Kecamatan harus di isi');
            }else if(!in_array($data['kecamatan'],['bagor','baron','berbek','gondang','jatikalen','kertosono','lengkong','loceret','nganjuk','ngetos','ngluyu','ngronggot','pace','patianrowo','prambon','rejoso','sawahan','sukomoro','tanjunganom','wilangan'])){
                throw new Exception('Kecamatan tidak ditemukan');
            }
            if (!isset($data['tempat_lahir']) || empty($data['tempat_lahir'])) {
                throw new Exception('Tempat lahir harus di isi');
            }
            if (!isset($data['tanggal_lahir']) || empty($data['tanggal_lahir'])) {
                throw new Exception('Tanggal lahir harus di isi');
            }
            if (!isset($data['nama_organisasi']) || empty($data['nama_organisasi'])) {
                throw new Exception('Nama organisasi harus di isi');
            }
            if (!isset($data['jumlah_anggota']) || empty($data['jumlah_anggota'])) {
                throw new Exception('Jumlah anggota harus di isi');
            }
            if(!is_numeric($data['jumlah_anggota'])){
                throw new Exception('Jumlah anggota harus angka');
            }
            if (!isset($_FILES['ktp_seniman']) || empty($_FILES['ktp_seniman'])) {
                throw new Exception('foto ktp harus di isi');
            }
            if (!isset($_FILES['pass_foto']) || empty($_FILES['pass_foto'])) {
                throw new Exception('pass foto harus di isi');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keterangan harus di isi');
            }
            if ($_FILES['ktp_seniman']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload ktp file');
            }
            if ($_FILES['pass_foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload foto file');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload pdf file');
            }
            $tanggal_lahir = strtotime($data['tanggal_lahir']);
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarangDB = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            if ($tanggal_lahir > $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
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
            //get last id seniman
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'seniman' ";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->execute();
            $idSeniman = 1;
            $stmt[1]->bind_result($idSeniman);
            $stmt[1]->fetch();
            $stmt[1]->close();
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //create folder
            if (!is_dir(self::$folderPath.$folderKtp)) {
                mkdir(self::$folderPath.$folderKtp, 0777, true);
            }
            if (!is_dir(self::$folderPath.$folderPassFoto)) {
                mkdir(self::$folderPath.$folderPassFoto, 0777, true);
            }
            if (!is_dir(self::$folderPath.$folderSurat)) {
                mkdir(self::$folderPath.$folderSurat, 0777, true);
            }
            //proses file
            $fileKtp = $_FILES['ktp_seniman'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['tmp_name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus jpg, png, jpeg','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileKtp['name']],'get',['table'=>'seniman','col'=>'ktp']);
            $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
            $fileKtpDB = $nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['tmp_name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus png, jpeg, jpg','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileFoto['name']],'get',['table'=>'seniman','col'=>'foto']);
            $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
            $fileFotoDB = $nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['tmp_name']);
            if ($extension === 'pdf') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)). 'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format file harus pdf','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get',['table'=>'seniman','col'=>'surat']);
            $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
            $fileSuratDB = $nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "INSERT INTO seniman (nik, nama_seniman,jenis_kelamin,kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi,jumlah_anggota,ktp_seniman,pass_foto, surat_keterangan, tgl_pembuatan, tgl_berlaku, created_at, updated_at, status, id_kategori_seniman, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $now = date('Y-m-d');
            $end = date('Y-m-d',strtotime('12/31/' . date('Y')));
            $stmt[2]->bind_param("ssssssssssssssssssss", $data['nik'], $data['nama_seniman'], $data['jenis_kelamin'], $data['kecamatan'], $data['tempat_lahir'], $data['tanggal_lahir'], $data['alamat_seniman'],$data['no_telpon'], $data['nama_organisasi'], $data['jumlah_anggota'],$fileKtpDB,$fileFotoDB, $fileSuratDB, $now, $end, $tanggal_sekarangDB, $tanggal_sekarangDB, $status, $kategori, $data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                //tambah data to file
                self::manageFile(['id_seniman'=>self::$con->insert_id,'ktp_seniman'=>$fileKtpDB, 'pass_foto'=>$fileFotoDB, 'surat_keterangan'=>$fileSuratDB],'tambah',['table'=>'seniman']);
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                unlink($fileSuratPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal ditambahkan','code'=>500]));
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
    public function editSeniman($data){
        try{
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if (!isset($data['nik']) || empty($data['nik'])) {
                throw new Exception('NIK seniman harus di isi');
            }
            if(!is_numeric($data['nik'])){
                throw new Exception('NIK seniman harus angka');
            }
            if (!isset($data['nama_seniman']) || empty($data['nama_seniman'])) {
                throw new Exception('Nama seniman harus di isi');
            }
            if (!isset($data['jenis_kelamin']) || empty($data['jenis_kelamin'])) {
                throw new Exception('Jenis kelamin harus di isi');
            }else if(!in_array($data['jenis_kelamin'],['laki-laki','perempuan'])){
                throw new Exception('Jenis kelamin salah');
            }
            if (!isset($data['alamat_seniman']) || empty($data['alamat_seniman'])) {
                throw new Exception('Alamat harus di isi');
            }
            if (!isset($data['no_telpon']) || empty($data['no_telpon'])) {
                throw new Exception('Nomor telpon harus di isi');
            }
            if (strlen($data['no_telpon']) > 16) {
                throw new Exception('Nama event maksimal 16 karakter');
            }
            $kategori = $this->kategori(['kategori'=>$data['singkatan_kategori']],'check');
            if (!isset($data['kecamatan']) || empty($data['kecamatan'])) {
                throw new Exception('Kecamatan harus di isi');
            }else if(!in_array($data['kecamatan'],['bagor','baron','berbek','gondang','jatikalen','kertosono','lengkong','loceret','nganjuk','ngetos','ngluyu','ngronggot','pace','patianrowo','prambon','rejoso','sawahan','sukomoro','tanjunganom','wilangan'])){
                throw new Exception('Kecamatan tidak ditemukan');
            }
            if (!isset($data['tempat_lahir']) || empty($data['tempat_lahir'])) {
                throw new Exception('Tempat lahir harus di isi');
            }
            if (!isset($data['tanggal_lahir']) || empty($data['tanggal_lahir'])) {
                throw new Exception('Tanggal lahir harus di isi');
            }
            if (!isset($data['nama_organisasi']) || empty($data['nama_organisasi'])) {
                throw new Exception('Nama organisasi harus di isi');
            }
            if (!isset($data['jumlah_anggota']) || empty($data['jumlah_anggota'])) {
                throw new Exception('Jumlah anggota harus di isi');
            }
            if(!is_numeric($data['jumlah_anggota'])){
                throw new Exception('Jumlah anggota harus angka !');
            }
            if (!isset($_FILES['foto_ktp']) || empty($_FILES['foto_ktp'])) {
                throw new Exception('foto ktp harus di isi');
            }
            if (!isset($_FILES['pass_foto']) || empty($_FILES['pass_foto'])) {
                throw new Exception('pass foto harus di isi');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keternangan harus di isi');
            }
            if ($_FILES['foto_ktp']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload ktp file');
            }
            if ($_FILES['pass_foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload foto file');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload pdf file');
            }
            $tanggal_lahir = strtotime($data['tanggal_lahir']);
            if (!$tanggal_lahir) {
                throw new Exception('Format tanggal lahir tidak valid');
            }
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarangDB = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarangDB);
            if ($tanggal_lahir > $tanggal_sekarang){
                throw new Exception('Tanggal tidak boleh lebih kurang dari sekarang !');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            //check seniman
            $query = "SELECT status, tgl_berlaku, ktp_seniman, pass_foto, surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $statusDB = '';
            $berlaku = '';
            $ktpDB = '';
            $fotoDB = '';
            $suratDB = '';
            $stmt[0]->bind_result($statusDB, $berlaku, $ktpDB, $fotoDB, $suratDB);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            if(!isset($data['desc']) || $data['desc'] != 'ulang'){
                if($statusDB == 'proses'){
                    throw new Exception('Data sedang diproses');
                }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                    throw new Exception('Data sudah diverifikasi');
                }
            }
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //check if user upload file
            $updateKTP = false;
            if(isset($_FILES['foto_ktp']) && !empty($_FILES['foto_ktp']) && !empty($_FILES['foto_ktp']['name']) && $_FILES['foto_ktp']['error'] !== 4){
                //proses file
                $fileKtp = $_FILES['foto_ktp'];
                $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
                $size = filesize($fileKtp['tmp_name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format foto Ktp harus png, jpeg, jpg','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileKtp['name']],'get',['table'=>'seniman','col'=>'ktp']);
                $fileKtpPath = self::$folderPath.$folderKtp.$nameFile;
                $fileKtpDB = $nameFile;
                if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                if($extension != pathinfo($ktpDB, PATHINFO_EXTENSION)){
                    unlink(self::$folderPath.$folderKtp.$ktpDB);
                }
                $updateKTP = true;
            }

            //check if user upload file
            $updateGambar = false;
            if(isset($_FILES['pass_foto']) && !empty($_FILES['pass_foto']) && !empty($_FILES['pass_foto']['name']) && $_FILES['pass_foto']['error'] !== 4){
                //proses file
                $fileFoto = $_FILES['pass_foto'];
                $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
                $size = filesize($fileFoto['tmp_name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format pass foto harus png, jpeg, jpg','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileFoto['name']],'get',['table'=>'seniman','col'=>'foto']);
                $fileFotoPath = self::$folderPath.$folderPassFoto.$nameFile;
                $fileFotoDB = $nameFile;
                if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                    unlink($fileKtpPath);
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                if($extension != pathinfo($fotoDB, PATHINFO_EXTENSION)){
                    unlink(self::$folderPath.$folderPassFoto.$fotoDB);
                }
                $updateGambar = true;
            }

            //check if user upload file
            $updateSurat = false;
            if(isset($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']['name']) && $_FILES['surat_keterangan']['error'] !== 4){
                //proses file
                $fileSurat = $_FILES['surat_keterangan'];
                $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
                $size = filesize($fileSurat['tmp_name']);
                if ($extension === 'pdf') {
                    if ($size >= self::$sizeFile) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format surat keterangan harus pdf','code'=>500]));
                }
                //simpan file
                $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get',['table'=>'seniman','col'=>'surat']);
                $fileSuratPath = self::$folderPath.$folderSurat.$nameFile;
                $fileSuratDB = $nameFile;
                if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                    unlink($fileKtpPath);
                    unlink($fileFotoPath);
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                unlink(self::$folderPath.$folderSurat.$suratDB);
                $updateSurat = true;
            }
            if(isset($data['desc']) && $data['desc'] == 'ulang'){
                $query = "UPDATE seniman SET nik = ?, nama_seniman = ?, jenis_kelamin = ?, kecamatan = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat_seniman = ?, no_telpon = ?, nama_organisasi = ?, jumlah_anggota = ?, ktp_seniman = ?, pass_foto = ?, surat_keterangan = ?, updated_at = ?, id_kategori_seniman = ? WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param("ssssssssssssssss", $data['nik'], $data['nama_seniman'], $data['jenis_kelamin'], $data['kecamatan'], $data['tempat_lahir'],$data['tanggal_lahir'], $data['alamat_seniman'],$data['no_telpon'], $data['nama_organisasi'], $data['jumlah_anggota'], $fileKtpDB, $fileFotoDB, $fileSuratDB, $tanggal_sekarangDB, $kategori, $data['id_seniman']);
            }else{
                $query = "UPDATE seniman SET nik = ?, nama_seniman = ?, jenis_kelamin = ?, kecamatan = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat_seniman = ?, no_telpon = ?, nama_organisasi = ?, jumlah_anggota = ?, ktp_seniman = ?, pass_foto = ?, surat_keterangan = ?, updated_at = ?, id_kategori_seniman = ? WHERE id_seniman = ?";
                $stmt[1] = self::$con->prepare($query);
                $stmt[1]->bind_param("ssssssssssssssss", $data['nik'], $data['nama_seniman'], $data['jenis_kelamin'], $data['kecamatan'], $data['tempat_lahir'],$data['tanggal_lahir'], $data['alamat_seniman'],$data['no_telpon'], $data['nama_organisasi'], $data['jumlah_anggota'], $fileKtpDB, $fileFotoDB, $fileSuratDB, $tanggal_sekarangDB, $kategori, $data['id_seniman']);
            }
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','message'=>'Data Seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                if($updateKTP == true ||$updateGambar == true ||$updateSurat == true){
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'Data seniman berhasil diubah']);
                    exit();
                }
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Seniman gagal diubah','code'=>500]));
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['message'],
                );
            }
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function hapusSeniman($data){
        try{
            // if(!isset($data['id_user']) || empty($data['id_user'])){
            //     throw new Exception('ID User harus di isi !');
            // }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID seniman harus di isi !');
            }
            // //check id_user
            // $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            // $stmt[0] = self::$con->prepare($query);
            // $stmt[0]->bind_param('s', $data['id_user']);
            // $stmt[0]->execute();
            // $role = '';
            // $stmt[0]->bind_result($role);
            // if (!$stmt[0]->fetch()) {
            //     $stmt[0]->close();
            //     throw new Exception('User tidak ditemukan');
            // }
            // $stmt[0]->close();
            // if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
            //     throw new Exception('Harus masyarakat');
            // }
            //check id_seniman
            $query = "SELECT status, ktp_seniman, pass_foto, surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $statusDB = '';
            $pathKTP = '';
            $pathFoto = '';
            $pathSurat = '';
            $stmt[1]->bind_result($statusDB, $pathKTP, $pathFoto,$pathSurat);
            if (!$stmt[1]->fetch()) {
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //delete file
            $fileKtpPath = self::$folderPath.'/ktp'.$pathKTP;
            $fileFotoPath = self::$folderPath.'/pass_foto'.$pathFoto;
            $fileSuratPath = self::$folderPath.'/surat_keterangan'.$pathSurat;
            unlink($fileKtpPath);
            unlink($fileFotoPath);
            unlink($fileSuratPath);
            //delete data
            $query = "DELETE FROM seniman WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_seniman']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','pesan'=>'Data Seniman berhasil dihapus','kode'=>1]);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data Seniman gagal dihapus','kode'=>0]));
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['pesan'],
                    'kode'=>2,
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function buatPerpanjangan($data){
        try{
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if(!isset($data['nama_lengkap']) || empty($data['nama_lengkap'])){
                throw new Exception('Nama Lengkap harus di isi');
            }
            if(!isset($data['nik']) || empty($data['nik'])){
                throw new Exception('NIK harus di isi');
            }
            if(!is_numeric($data['nik'])){
                throw new Exception('NIK harus berisi angka !');
            }
            if(!isset($data['nomor_induk']) || empty($data['nomor_induk'])){
                throw new Exception('nomor induk harus di isi');
            }
            if (!isset($_FILES['ktp_seniman']) || empty($_FILES['ktp_seniman'])) {
                throw new Exception('foto ktp harus di isi');
            }
            if (!isset($_FILES['pass_foto']) || empty($_FILES['pass_foto'])) {
                throw new Exception('pass foto harus di isi');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keternangan harus di isi');
            }
            if ($_FILES['ktp_seniman']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload ktp file');
            }
            if ($_FILES['pass_foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload foto file');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload pdf file');
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
            //check seniman
            $query = "SELECT nama_seniman, nik, nomor_induk, status, tgl_berlaku, ktp_seniman, pass_foto, surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $namaDB = '';
            $nikDB = '';
            $nisDB = '';
            $statusDB = '';
            $berlaku = '';
            $ktpDB = '';
            $fotoDB = '';
            $suratDB = '';
            $stmt[0]->bind_result($namaDB, $nikDB, $nisDB, $statusDB, $berlaku, $ktpDB, $fotoDB, $suratDB);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            if(trim($data['nama_lengkap']) !== trim($namaDB)){
                throw new Exception('Nama lengkap tidak cocok !');
            }
            if (trim($data['nik']) !== trim($nikDB)) {
                throw new Exception('NIK tidak cocok !');
            }
            if(trim($data['nomor_induk']) !== trim($nisDB)){
                throw new Exception('Nomor induk invalid !');
            }
            //check tanggal berlaku
            // $berlaku = strtotime($berlaku);
            // date_default_timezone_set('Asia/Jakarta');
            // $tanggal_sekarang = date('Y-m-d');
            // $tanggal_sekarang = strtotime($tanggal_sekarang);
            // if ($tanggal_sekarang < $berlaku){
            //     throw new Exception('Anda tidak bisa update data seniman !');
            // }
            //file
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //create folder
            if (!is_dir(self::$perpanjanganPath.$folderKtp)) {
                mkdir(self::$perpanjanganPath.$folderKtp, 0777, true);
            }
            if (!is_dir(self::$perpanjanganPath.$folderPassFoto)) {
                mkdir(self::$perpanjanganPath.$folderPassFoto, 0777, true);
            }
            if (!is_dir(self::$perpanjanganPath.$folderSurat)) {
                mkdir(self::$perpanjanganPath.$folderSurat, 0777, true);
            }
            //proses file
            $fileKtp = $_FILES['ktp_seniman'];
            $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
            $size = filesize($fileKtp['tmp_name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format foto ktp harus png, jpeg, jpg','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileKtp['name']],'get',['table'=>'perpanjangan','col'=>'ktp']);
            $fileKtpPath = self::$perpanjanganPath.$folderKtp.$nameFile;
            $fileKtpDB = $nameFile;
            if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            
            //proses file
            $fileFoto = $_FILES['pass_foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['tmp_name']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format pass foto harus png, jpeg, jpg','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileFoto['name']],'get',['table'=>'perpanjangan','col'=>'foto']);
            $fileFotoPath = self::$perpanjanganPath.$folderPassFoto.$nameFile;
            $fileFotoDB = $nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                unlink($fileKtpPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }

            //proses file
            $fileSurat = $_FILES['surat_keterangan'];
            $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
            $size = filesize($fileSurat['tmp_name']);
            if ($extension === 'pdf') {
                if ($size >= self::$sizeFile) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)).'MB','code'=>500]));
                }
            } else {
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format surat keterangan harus pdf','code'=>500]));
            }
            //simpan file
            $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get',['table'=>'perpanjangan','col'=>'surat']);
            $fileSuratPath = self::$perpanjanganPath.$folderSurat.$nameFile;
            $fileSuratDB = $nameFile;
            if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
            }
            $query = "INSERT INTO perpanjangan (nik, ktp_seniman, pass_foto, surat_keterangan, status, id_seniman) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diajukan';
            $stmt[2]->bind_param("ssssss", $data['nik'], $fileKtpDB, $fileFotoDB, $fileSuratDB, $status, $data['id_seniman']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                //tambah data to file
                self::manageFile(['ktp_seniman'=>$fileKtpDB, 'pass_foto'=>$fileFotoDB, 'surat_keterangan'=>$fileSuratDB],'tambah',['table'=>'perpanjangan']);
                echo json_encode(['status'=>'success','message'=>'Data Perpanjangan Seniman berhasil ditambahkan']);
                exit();
            } else {
                $stmt[2]->close();
                unlink($fileKtpPath);
                unlink($fileFotoPath);
                unlink($fileSuratPath);
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Data Perpanjangan Seniman gagal ditambahkan','code'=>500]));
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
    public function editPerpanjangan($data){
        try{
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID Seniman harus di isi');
            }
            if(!isset($data['id_user']) || empty($data['id_user'])){
                throw new Exception('ID User harus di isi');
            }
            if (!isset($_FILES['foto_ktp']) || empty($_FILES['foto_ktp'])) {
                throw new Exception('foto ktp harus di isi');
            }
            if (!isset($_FILES['pass_foto']) || empty($_FILES['pass_foto'])) {
                throw new Exception('pass foto harus di isi');
            }
            if (!isset($_FILES['surat_keterangan']) || empty($_FILES['surat_keterangan'])) {
                throw new Exception('Surat keternangan harus di isi');
            }
            if ($_FILES['foto_ktp']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload ktp file');
            }
            if ($_FILES['pass_foto']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload foto file');
            }
            if ($_FILES['surat_keterangan']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('gagal upload pdf file');
            }
            //check user
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_user']);
            $stmt[0]->execute();
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                throw new Exception(json_encode(['status' => 'error', 'message' => 'User tidak ditemukan','code'=>500]));
            }
            $stmt[0]->close();
            //check seniman
            $query = "SELECT status, tgl_berlaku, ktp_seniman, pass_foto, surat_keterangan FROM seniman WHERE id_seniman = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_seniman']);
            $stmt[0]->execute();
            $statusDB = '';
            $berlaku = '';
            $ktpDB = '';
            $fotoDB = '';
            $suratDB = '';
            $stmt[0]->bind_result($statusDB, $berlaku, $ktpDB, $fotoDB, $suratDB);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[0]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //check tanggal berlaku
            $berlaku = strtotime($berlaku);
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if ($tanggal_sekarang < $berlaku){
                throw new Exception('Anda tidak bisa update data seniman !');
            }
            $folderKtp = '/ktp';
            $folderPassFoto = '/pass_foto';
            $folderSurat = '/surat_keterangan';
            //check if user upload file
            $updateKTP = false;
            if(isset($_FILES['foto_ktp']) && !empty($_FILES['foto_ktp']) && !empty($_FILES['foto_ktp']['name']) && $_FILES['foto_ktp']['error'] !== 4){
                //proses file
                $fileKtp = $_FILES['foto_ktp'];
                $extension = pathinfo($fileKtp['name'], PATHINFO_EXTENSION);
                $size = filesize($fileKtp['tmp_name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format foto ktp harus png, jpeg, jpg','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileKtp['name']],'get',['table'=>'perpanjangan','col'=>'ktp']);
                $fileKtpPath = self::$perpanjanganPath.$folderKtp.$nameFile;
                $fileKtpDB = $nameFile;
                if (!move_uploaded_file($fileKtp['tmp_name'], $fileKtpPath)) {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                if($extension != pathinfo($ktpDB, PATHINFO_EXTENSION)){
                    unlink(self::$folderPath.$folderKtp.$ktpDB);
                }
                $updateKTP = true;
            }

            //check if user upload file
            $updateGambar = false;
            if(isset($_FILES['pass_foto']) && !empty($_FILES['pass_foto']) && !empty($_FILES['pass_foto']['name']) && $_FILES['pass_foto']['error'] !== 4){
                //proses file
                $fileFoto = $_FILES['pass_foto'];
                $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
                $size = filesize($fileFoto['tmp_name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeImg / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format pass foto harus png, jpeg, jpg','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileFoto['name']],'get',['table'=>'perpanjangan','col'=>'foto']);
                $fileFotoPath = self::$perpanjanganPath.$folderPassFoto.$nameFile;
                $fileFotoDB = $nameFile;
                if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                    unlink($fileKtpPath);
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                if($extension != pathinfo($fotoDB, PATHINFO_EXTENSION)){
                    unlink(self::$folderPath.$folderPassFoto.$fotoDB);
                }
                $updateGambar = true;
            }

            //check if user upload file
            $updateSurat = false;
            if(isset($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']) && !empty($_FILES['surat_keterangan']['name']) && $_FILES['surat_keterangan']['error'] !== 4){
                //proses file
                $fileSurat = $_FILES['surat_keterangan'];
                $extension = pathinfo($fileSurat['name'], PATHINFO_EXTENSION);
                $size = filesize($fileSurat['tmp_name']);
                if ($extension === 'pdf') {
                    if ($size >= self::$sizeFile) {
                        throw new Exception(json_encode(['status' => 'error', 'message' => 'Ukuran file maksimal '.(self::$sizeFile / (1024 * 1024)).'MB','code'=>500]));
                    }
                } else {
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Format surat keterangan harus pdf','code'=>500]));
                }
                //replace file
                $nameFile = self::manageFile(['nama_file'=>$fileSurat['name']],'get',['table'=>'perpanjangan','col'=>'surat']);
                $fileSuratPath = self::$perpanjanganPath.$folderSurat.$nameFile;
                $fileSuratDB = $nameFile;
                if (!move_uploaded_file($fileSurat['tmp_name'], $fileSuratPath)) {
                    unlink($fileKtpPath);
                    unlink($fileFotoPath);
                    throw new Exception(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan file','code'=>500]));
                }
                unlink(self::$folderPath.$folderSurat.$suratDB);
                $updateSurat = true;
            }
            $query = "UPDATE perpanjangan SET nik = ?, ktp_seniman = ?, pass_foto = ?, surat_keterangan = ? WHERE id_seniman = ?";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("sssss", $data['nik_seniman'], $fileKtpDB, $fileFotoDB, $fileSuratDB, $data['id_seniman']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                echo json_encode(['status'=>'success','message'=>'Data perpanjangan seniman berhasil dubah']);
                exit();
            } else {
                $stmt[1]->close();
                if($updateKTP == true ||$updateGambar == true ||$updateSurat == true){
                    header('Content-Type: application/json');
                    echo json_encode(['status'=>'success','message'=>'Data perpanjangan seniman berhasil diubah']);
                    exit();
                }
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
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public function hapusPerpanjangan($data){
        try{
            // if(!isset($data['id_user']) || empty($data['id_user'])){
            //     throw new Exception('ID User harus di isi !');
            // }
            if(!isset($data['id_seniman']) || empty($data['id_seniman'])){
                throw new Exception('ID seniman harus di isi !');
            }
            // //check id_user
            // $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            // $stmt[0] = self::$con->prepare($query);
            // $stmt[0]->bind_param('s', $data['id_user']);
            // $stmt[0]->execute();
            // $role = '';
            // $stmt[0]->bind_result($role);
            // if (!$stmt[0]->fetch()) {
            //     $stmt[0]->close();
            //     throw new Exception('User tidak ditemukan');
            // }
            // $stmt[0]->close();
            // if(in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
            //     throw new Exception('Harus masyarakat');
            // }
            //check id_seniman
            $query = "SELECT status, ktp_seniman, pass_foto, surat_keterangan FROM perpanjangan WHERE id_seniman = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_seniman']);
            $stmt[1]->execute();
            $statusDB = '';
            $pathKTP = '';
            $pathFoto = '';
            $pathSurat = '';
            $stmt[1]->bind_result($statusDB, $pathKTP, $pathFoto,$pathSurat);
            if (!$stmt[1]->fetch()) {
                $stmt[1]->close();
                throw new Exception('Data seniman tidak ditemukan');
            }
            $stmt[1]->close();
            if($statusDB == 'proses'){
                throw new Exception('Data sedang diproses');
            }else if($statusDB == 'diterima' || $statusDB == 'ditolak'){
                throw new Exception('Data sudah diverifikasi');
            }
            //delete file
            $fileKtpPath = self::$folderPath.'/ktp'.$pathKTP;
            $fileFotoPath = self::$folderPath.'/pass_foto'.$pathFoto;
            $fileSuratPath = self::$folderPath.'/surat_keterangan'.$pathSurat;
            unlink($fileKtpPath);
            unlink($fileFotoPath);
            unlink($fileSuratPath);
            //delete data
            $query = "DELETE FROM perpanjangan WHERE id_seniman = ?";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('s', $data['id_seniman']);
            if ($stmt[2]->execute()) {
                $stmt[2]->close();
                header('Content-Type: application/json');
                echo json_encode(['status'=>'success','pesan'=>'Data Perpanjangan Seniman berhasil dihapus','kode'=>1]);
                exit();
            } else {
                $stmt[2]->close();
                throw new Exception(json_encode(['status' => 'error', 'pesan' => 'Data perpanjangan Seniman gagal dihapus','kode'=>0]));
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $error,
                    'kode'=>2,
                );
            }else{
                $responseData = array(
                    'status' => 'error',
                    'pesan' => $errorJson['pesan'],
                    'kode'=>2,
                );
            }
            header('Content-Type: application/json');
            isset($errorJson['code']) ? http_response_code($errorJson['code']) : http_response_code(400);
            echo json_encode($responseData);
            exit();
        }
    }
    public static function handle(){
        $contentType = isset($_SERVER["HTTP_CONTENT_TYPE"]) ? $_SERVER["HTTP_CONTENT_TYPE"] : null;
        if ($contentType === "application/json") {
            $rawData = file_get_contents("php://input");
            $requestData = json_decode($rawData, true);
            if ($requestData === null && json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'pesan' => 'Invalid JSON data']);
                exit();
            }
            return $requestData;
        } elseif ($contentType === "application/x-www-form-urlencoded") {
            $requestData = $_POST;
            return $requestData;
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            $requestData = $_POST;
            return $requestData;
        // } else {
        //     http_response_code(400);
        //     echo json_encode(['status' => 'error', 'pesan' => 'Unsupported content type']);
        //     exit();
        }
    }
}
function loadEnvSeniman($path = null){
    if($path == null){
        $path = __DIR__."/../../.env";
    }
    if (file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
                $_SERVER[trim($key)] = trim($value);
            }
        }
    }
}
loadEnvSeniman();
// if($_SERVER['REQUEST_METHOD'] == 'GET'){
//     include(__DIR__.'/../../notfound.php');
// }
$senimanMobile = new SenimanMobile;
if(isset($_SERVER['APP_TESTING']) && $_SERVER['APP_TESTING'] == 'true'){
    if($_SERVER['REQUEST_METHOD'] == 'PUT'){
        $data = SenimanMobile::handle();
            if($data['keterangan'] == 'perpanjang'){
            $senimanMobile->editPerpanjangan($data);
        }else{
            $senimanMobile->editSeniman(SenimanMobile::handle());
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
        $senimanMobile->hapusSeniman(SenimanMobile::handle());
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $data = SenimanMobile::handle();
        if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan']) && $data['keterangan'] == 'get'){
            $senimanMobile->getSeniman($data);
        }
        if(isset($_POST['_method'])){
            if($_POST['_method'] == 'PUT'){
                if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan']) && $data['keterangan'] == 'perpanjang'){
                    $senimanMobile->editPerpanjangan($data);
                }else{
                    $senimanMobile->editSeniman($data);
                }
            }else if($_POST['_method'] == 'DELETE'){
                $senimanMobile->hapusSeniman($data);
            }
        }else{
            if(isset($data['keterangan']) && !empty($data['keterangan']) && !is_null($data['keterangan']) && $data['keterangan'] == 'perpanjangan'){
                $senimanMobile->buatPerpanjangan($data);
            }else{
                $senimanMobile->registrasiSeniman($data);
            }
        }
    }
}
$getKategori = function ($data) use ($senimanMobile){
    $kategori = $senimanMobile->kategori($data,'get all');
    if(empty($kategori) && is_null($kategori)){
        header("Content-Type: application/json");
        $data = [
            'kode'=>1,
            'pesan'=>'Tidak ada data kategori',
        ];
        echo json_encode($data);
        exit();
    }
    header("Content-Type: application/json");
    foreach($kategori as $key => $item){
        unset($kategori[$key]['singkatan']);
        $kategori[$key]['id_kategori_seniman'] = (string) $kategori[$key]['id_kategori_seniman'];
    }
    echo json_encode($kategori);
    exit();
};
$getNamaKategori = function ($data) use ($senimanMobile){
    $kategori = $senimanMobile->kategori($data,'get nama');
    if(empty($kategori) && is_null($kategori)){
        header("Content-Type: application/json");
        $data = [
            'kode'=>1,
            'pesan'=>'Nama Kategori tidak tersedia',
        ];  
        echo json_encode($data);
        exit();
    }
    header("Content-Type: application/json");
    $data = [
        'kode'=>1,
        'pesan'=>'Data tersedia',
        'data'=>$kategori
    ];
    echo json_encode($data);
    exit();
};
$getSeniman = function ($data) use ($senimanMobile){
    $senimanMobile->getSeniman($data);
};
$tambahSeniman = function ($data) use ($senimanMobile){
    $senimanMobile->registrasiSeniman($data);
};
$updateSeniman = function ($data) use ($senimanMobile){
    $senimanMobile->editSeniman($data);
};
$hapusSeniman = function ($data) use ($senimanMobile){
    $senimanMobile->hapusSeniman($data);
};
$tambahPerpanjangan = function ($data) use ($senimanMobile){
    $senimanMobile->buatPerpanjangan($data);
};
$updateperpanjangan = function ($data) use ($senimanMobile){
    $senimanMobile->editPerpanjangan($data);
};
$hapusPerpanjangan = function ($data) use ($senimanMobile){
    $senimanMobile->hapusPerpanjangan($data);
};
?>    