<?php
require_once(__DIR__ . '/../../web/koneksi.php');
class SenimanWebsite{
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
        // self::$folderPath = __DIR__.'/../../DatabaseMobile/data_seniman_mobile/uploads/seniman';
        // self::$perpanjanganPath = __DIR__.'/../../DatabaseMobile/data_seniman_mobile/uploads/perpanjangan';
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
                    if (isset($item['singkatan_kategori']) && $item['singkatan_kategori'] == $data['kategori']) {
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
                        $result = $jsonData[$key]['singkatan_kategori'];
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
    private function kategoriFile($data,$desc){
        try{
            $fileExist = file_exists(self::$jsonPath);
            if (!$fileExist) {
                //if file is delete will make new json file
                $query = "SELECT * FROM kategori_seniman";
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
                    if (!file_put_contents(self::$jsonPath, $jsonData)) {
                        echo "Gagal menyimpan file sistem";
                    }
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
            }else if($desc == 'tambah'){
                //check if file exist
                if (!$fileExist) {
                    //if file is delete will make new json file
                    $query = "SELECT * FROM kategori_seniman";
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
                        if (!file_put_contents(self::$jsonPath, $jsonData)) {
                            echo "Gagal menyimpan file sistem";
                        }
                    }
                }else{
                    //tambah kategori seniman
                    $jsonFile = file_get_contents(self::$jsonPath);
                    $jsonData = json_decode($jsonFile, true);
                    $new[$data['id_kategori_seniman']] = $data;
                    $jsonData = array_merge($jsonData, $new);
                    $jsonFile = json_encode($jsonData, JSON_PRETTY_PRINT);
                    file_put_contents(self::$jsonPath, $jsonFile);
                }
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
    public static function getPerpanjangan($data){
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
                    $query = "SELECT seniman.id_seniman, nama_seniman, DATE_FORMAT(perpanjangan.tgl_pembuatan, '%d %M %Y') AS tanggal, perpanjangan.status FROM perpanjangan INNER JOIN seniman ON seniman.id_seniman = perpanjangan.id_seniman WHERE perpanjangan.status = 'diajukan' OR perpanjangan.status = 'proses' ORDER BY id_seniman DESC";
                    // $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status FROM seniman WHERE status = 'diajukan' OR status = 'proses' ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'riwayat'){
                    $query = "SELECT seniman.id_seniman, nama_seniman, DATE_FORMAT(perpanjangan.tgl_pembuatan, '%d %M %Y') AS tanggal, perpanjangan.status FROM perpanjangan INNER JOIN seniman ON seniman.id_seniman = perpanjangan.id_seniman WHERE perpanjangan.status = 'ditolak' OR perpanjangan.status = 'diterima' ORDER BY id_seniman DESC";
                    // $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, kode_verifikasi FROM seniman WHERE status = 'ditolak' OR status = 'diterima' ORDER BY id_seniman DESC";
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
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan, kode_verifikasi FROM seniman WHERE (status = 'ditolak' OR status = 'diterima') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'data'){
                    if(!isset($data['kategori']) || empty($data['kategori'])){
                        throw new Exception('Kategori Seniman harus di isi !');
                    }
                    if($data['kategori'] == 'semua'){
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan, kode_verifikasi FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                    }else{
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan, kode_verifikasi FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND seniman.id_kategori_seniman = ".$data['kategori']." AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
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
    public static function getSeniman($data){
        try{
            // echo 'entok seniman'
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
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, kode_verifikasi FROM seniman WHERE status = 'ditolak' OR status = 'diterima' ORDER BY id_seniman DESC";
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
                    $query = "SELECT id_seniman, nama_seniman, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, kode_verifikasi FROM seniman WHERE (status = 'ditolak' OR status = 'diterima') AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                }else if($data['desc'] == 'data'){
                    if(!isset($data['kategori']) || empty($data['kategori'])){
                        throw new Exception('Kategori Seniman harus di isi !');
                    }
                    if($data['kategori'] == 'semua'){
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan, kode_verifikasi FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
                    }else{
                        $query = "SELECT id_seniman, nomor_induk, nama_kategori, nama_seniman, no_telpon, DATE_FORMAT(created_at, '%d %M %Y') AS tanggal, status, catatan, kode_verifikasi FROM seniman INNER JOIN kategori_seniman ON seniman.id_kategori_seniman = kategori_seniman.id_kategori_seniman WHERE status = 'diterima' AND seniman.id_kategori_seniman = ".$data['kategori']." AND MONTH(created_at) = ? AND YEAR(created_at) = ? ORDER BY id_seniman DESC";
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
            $query = "INSERT INTO kategori_seniman (nama_kategori, singkatan) VALUES (?, ?)";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param("ss",$data['nama_kategori'], $data['singkatan']);
            $stmt[1]->execute();
            if ($stmt[1]->affected_rows > 0) {
                $stmt[1]->close();
                //tambah file
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
            $query = "UPDATE kategori_seniman SET nama_kategori = ?, singkatan = ? WHERE id_kategori_seniman = ?";
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
                $nis = $kategoriData['singkatan_kategori'].'/'.$total.'/'.self::$constID.'/'.date('Y');
            }else if($desc == 'perpanjangan'){
                $nis = $kategoriData['singkatan_kategori'].'/'.$total.'/'.self::$constID.'/'.(date('Y')+1);
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
            if($data['keterangan'] ==  'ditolak' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && ($statusDB == 'diterima' || $statusDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            // if($data['keterangan'] ==  'ditolak' && $statusDB == 'diterima'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            // if($data['keterangan'] ==  'diterima' && $statusDB == 'ditolak'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            //update data
            if($data['keterangan'] == 'proses'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $redirect = '/pengajuan.php';
                $status = 'proses';
                $query = "UPDATE seniman SET kode_verifikasi = ?, status = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $code = '';
                $stmt[2]->bind_param("ssi", $code, $status, $data['id_seniman']);
            }else if($data['keterangan'] == 'diterima'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $redirect = '/pengajuan.php';
                $status = 'diterima';
                $query = "UPDATE seniman SET nomor_induk = ?, kode_verifikasi = ?, status = ?, catatan = ? WHERE id_seniman = ?";
                $nomorInduk = $this->generateNIS(['id_kategori'=>$idKategori],'diterima');
                $stmt[2] = self::$con->prepare($query);
                $code = mt_rand(0,9999999999);
                $catatan = '';
                $stmt[2]->bind_param("ssssi", $nomorInduk['nis'], $code, $status, $catatan, $data['id_seniman']);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $redirect = '/pengajuan.php';
                $status = 'ditolak';
                $query = "UPDATE seniman SET kode_verifikasi = ?, status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[2] = self::$con->prepare($query);
                $code = '';
                $stmt[2]->bind_param("sssi", $code, $status, $data['catatan'], $data['id_seniman']);
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
            http_response_code(400);
            echo "<script>alert('$error')</script>";
            echo "<script>window.history.back();</script>";
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
            $pattern = '/^[a-zA-Z \p{L}\p{M}]+$/u';
            if (!preg_match($pattern, $data['nama_seniman'])) {
                throw new Exception('Nama lengkap hanya boleh mengandung huruf dan karakter khusus!');
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
                throw new Exception('Nomor telepon maksimal 16 karakter');
            }
            if (!isset($data['jenis_kelamin']) || empty($data['jenis_kelamin'])) {
                throw new Exception('Jenis kelamin harus di isi');
            }else if(!in_array($data['jenis_kelamin'],['laki-laki','perempuan'])){
                throw new Exception('Jenis kelamin salah');
            }
            if (!isset($data['singkatan_kategori']) || empty($data['singkatan_kategori'])) {
                throw new Exception('Kategori harus di isi');
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
                $data['nama_organisasi'] = '';
            }
            if (!isset($data['jumlah_anggota']) || empty($data['jumlah_anggota'])) {
                $data['jumlah_anggota'] = 1;
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
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format foto ktp harus jpg, png, jpeg','code'=>500]));
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
                throw new Exception(json_encode(['status' => 'error', 'message' => 'Format pass foto harus png, jpeg, jpg','code'=>500]));
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
            $query = "INSERT INTO seniman (nik, nomor_induk, nama_seniman,jenis_kelamin,kecamatan, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi,jumlah_anggota,ktp_seniman,pass_foto, surat_keterangan, tgl_pembuatan, tgl_berlaku, created_at, updated_at, status, id_kategori_seniman, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt[2] = self::$con->prepare($query);
            $status = 'diterima';
            $nomorInduk = $this->generateNIS(['id_kategori'=>$kategori],'diterima');
            $now = date('Y-m-d');
            $end = date('Y-m-d',strtotime('12/31/' . date('Y')));
            $stmt[2]->bind_param("sssssssssssssssssssss", $data['nik'], $nomorInduk['nis'], $data['nama_seniman'], $data['jenis_kelamin'], $data['kecamatan'], $data['tempat_lahir'], $data['tanggal_lahir'], $data['alamat_seniman'],$data['no_telpon'], $data['nama_organisasi'], $data['jumlah_anggota'],$fileKtpDB,$fileFotoDB, $fileSuratDB, $now, $end, $tanggal_sekarangDB, $tanggal_sekarangDB, $status, $kategori, $data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->affected_rows > 0) {
                $stmt[2]->close();
                //tambah data to file
                self::manageFile(['id_seniman'=>self::$con->insert_id,'ktp_seniman'=>$fileKtpDB, 'pass_foto'=>$fileFotoDB, 'surat_keterangan'=>$fileSuratDB],'tambah',['table'=>'seniman']);
                echo "<script>alert('Data Seniman berhasil ditambahkan')</script>";
                echo "<script>window.location.href = '/seniman/data_seniman.php'; </script>";
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
            echo "<script>alert('".$responseData['message']."')</script>";
            echo "<script>window.history.back();</script>";
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
            $pattern = '/^[a-zA-Z \p{L}\p{M}]+$/u';
            if (!preg_match($pattern, $data['nama_seniman'])) {
                throw new Exception('Nama lengkap hanya boleh mengandung huruf dan karakter khusus!');
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
                    echo "<script>alert('Data Seniman berhasil diubah')</script>";
                    echo "<script>window.location.href = '/seniman/data_seniman.php'; </script>";
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
            echo "<script>alert('".$responseData['message']."')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
    public function hapusSeniman($data){
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
            if(!in_array($role,['super admin','admin tempat','admin event', 'admin pentas', 'admn seniman'])){
                throw new Exception('harus admin');
            }
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
                echo "<script>alert('Status berhasil dihapus')</script>";
                echo "<script>window.location.href = '/seniman/data_seniman.php'; </script>";
                exit();
            } else {
                $stmt[2]->close();
                echo "<script>alert('Status gagal dihapus')</script>";
                echo "<script>window.location.href = '/seniman/data_seniman.php'; </script>";
                exit();
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
                echo "<script>alert('Data Seniman sedang diajukan')</script>";
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
            $stmt[2]->close();
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
            if($data['keterangan'] ==  'ditolak' && ($statusPDB == 'diterima' || $statusPDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['keterangan'] ==  'diterima' && ($statusPDB == 'diterima' || $statusPDB == 'ditolak')){
                echo "<script>alert('Data sudah diverifikasi')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            // if($data['keterangan'] ==  'ditolak' && $statusPDB == 'diterima'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            // if($data['keterangan'] ==  'diterima' && $statusPDB == 'ditolak'){
            //     echo "<script>alert('Data sudah diverifikasi')</script>";
            //     echo "<script>window.history.back();</script>";
            //     exit();
            // }
            //update data
            $redirect = '/perpanjangan.php';
            if($data['keterangan'] == 'proses'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                $status = 'proses';
                $query = "UPDATE perpanjangan SET kode_verifikasi = ?, status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[7] = self::$con->prepare($query);
                $code = '';
                $stmt[7]->bind_param("sssi", $code, $status, $data['catatan'], $data['id_seniman']);
            }else if($data['keterangan'] == 'ditolak'){
                if(!isset($data['catatan']) || empty($data['catatan'])){
                    echo "<script>alert('Catatan harus di isi !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $status = 'ditolak';
                $query = "UPDATE perpanjangan SET kode_verifikasi = ?, status = ?, catatan = ? WHERE id_seniman = ?";
                $stmt[7] = self::$con->prepare($query);
                $code = '';
                $stmt[7]->bind_param("sssi", $code, $status, $data['catatan'], $data['id_seniman']);
            }
                $stmt[7]->execute();
                if ($stmt[7]->affected_rows > 0) {
                    $stmt[7]->close();
                    echo "<script>alert('Status berhasil diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                } else {
                    $stmt[7]->close();
                    echo "<script>alert('Status gagal diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                }
            
            if($data['keterangan'] == 'diterima'){
                if(isset($data['catatan']) || !empty($data['catatan'])){
                    $data['catatan'] = '';
                }
                //tambah histori
                $query = "INSERT INTO histori_nis (nis, tahun, id_seniman) VALUES (?, ?, ?)";
                $stmt[4] = self::$con->prepare($query);
                $tahun = explode("/", $nomorIndukDB);
                $tahun = end($tahun);
                $stmt[4]->bind_param("sss", $nomorIndukDB, $tahun, $data['id_seniman']);
                $stmt[4]->execute();
                if (!$stmt[4]->affected_rows > 0) {
                    $stmt[4]->close();
                    echo "<script>alert('Error tambah data histori nomor induk !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                //hapus data perpanjangan
                $query = "DELETE FROM perpanjangan WHERE id_seniman = ?";
                $stmt[5] = self::$con->prepare($query);
                $stmt[5]->bind_param('s', $data['id_seniman']);
                if (!$stmt[5]->execute()) {
                    $stmt[5]->close();
                    echo "<script>alert('Error hapus data perpanjangan seniman')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                $stmt[5]->close();
                //update nis 
                $query = "UPDATE seniman SET nomor_induk = ?, kode_verifikasi = ? WHERE id_seniman = ?";
                $nomorInduk = $this->generateNIS(['kategori'=>$idKategori],'perpanjangan');
                $stmt[6] = self::$con->prepare($query);
                $code = substr(uniqid(), 0 ,10);
                $stmt[6]->bind_param("ssi", $nomorInduk['nis'], $code, $data['id_seniman']);
                $stmt[6]->execute();
                if (!$stmt[6]->affected_rows > 0) {
                    $stmt[6]->close();
                    echo "<script>alert('Status gagal diubah')</script>";
                    echo "<script>window.location.href = '/seniman". $redirect . "'; </script>";
                    exit();
                }
                $stmt[6]->close();
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
            }
            if(isset($data['keterangan'])){
                if(isset($data['desc'])){
                    if($data['desc'] == 'perpanjangan'){
                        $senimanWeb->prosesPerpanjangan($data);
                    }else if($data['desc'] == 'seniman'){
                        $senimanWeb->prosesSeniman($data);
                    }
                }else{
                    $senimanWeb->editSeniman($data);
                }
            }
        }else if($data['_method'] == 'DELETE'){
            if(isset($data['desc']) && $data['desc'] == 'hapus'){
                $senimanWeb->hapusSeniman($data);
            }
            // if(isset($data['desc']) && !empty($data['desc']) && !is_null($data['desc']) && $data['desc'] == 'kategori'){
            //     $senimanWeb->hapusKategori($data);
            // }
        }
    }
    if(isset($data['desc']) && !empty($data['desc']) && !is_null($data['desc'])){
        if($data['desc'] == 'kategori'){
            $senimanWeb->tambahKategori($data);
        }
        if($data['desc'] == 'pengajuan' || $data['desc'] == 'riwayat' || $data['desc'] == 'data'){
            if(isset($data['table']) && $data['table'] == 'seniman'){
                $senimanWeb->getSeniman($data);
            }else if(isset($data['table']) && $data['table'] == 'perpanjangan'){
                $senimanWeb->getPerpanjangan($data);
            }
        }
    }
    if(isset($data['keterangan'])){
        if($data['keterangan'] == 'tambah'){
            $senimanWeb->registrasiSeniman($data);
        }else if($data['keterangan'] == 'edit'){
            $senimanWeb->editSeniman($data);
        }
    }
}
?>