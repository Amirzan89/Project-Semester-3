<?php 
require_once('koneksi.php');
class User{
    private static $sizeImg = 5 * 1024 * 1024;
    private static $database;
    private static $con;
    private static $folderPath;
    public function __construct(){
        self::$database = Koneksi::getInstance();
        self::$con = self::$database->getConnection();
        self::$folderPath = __DIR__.'/../private/profile';
    }
    //khusus admin 
    public function tambahAdmin($data){
        try{
            if (!isset($data['id_user']) || empty($data['id_user'])) {
                echo "<script>alert('ID User harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }else if (!isset($data['email']) || empty($data['email'])) {
                echo "<script>alert('Email harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Email invalid !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['pass']) || empty($data['pass'])) {
                echo "<script>alert('Password harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            } elseif (strlen($data['pass']) < 8) {
                echo "<script>alert('Password minimal 8 angka !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            } elseif (strlen($data['pass']) > 25) {
                echo "<script>alert('Password maksimal 25 angka !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['pass'])) {
                echo "<script>alert('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['nama']) || empty($data['nama'])) {
                echo "<script>alert('Nama lengkap harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['phone']) || empty($data['phone'])) {
                echo "<script>alert('nomer telepon harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!is_numeric($data['phone'])) {
                echo "<script>alert('Nomer telepon harus berisi hanya angka.')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (strlen($data['phone']) < 8) {
                echo "<script>alert('Nomer telpon minimal 8 karakter !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (strlen($data['phone']) > 15) {
                echo "<script>alert('Nomer telpon maksimal 15 karakter !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (substr($data['phone'], 0, 2) !== '08') {
                echo "<script>alert('Nomer telepon harus dimulai dengan 08.')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['jenisK']) || empty($data['jenisK'])) {
                echo "<script>alert('Jenis kelamin harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!in_array($data['jenisK'], ['laki-laki','perempuan'])){
                echo "<script>alert('Invalid jenis kelamin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['tempatL']) || empty($data['tempatL'])) {
                echo "<script>alert('Tempat lahir harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['tanggalL']) || empty($data['tanggalL'])) {
                echo "<script>alert('Tanggal lahir harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['role']) || empty($data['role'])) {
                echo "<script>alert('Role harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!in_array($data['role'], ['super admin','admin event','admin pentas', 'admin tempat', 'admin seniman'])){
                echo "<script>alert('Invalid role !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check tanggal
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_lahir = strtotime($data['tanggalL']);
            $tanggal_sekarang = date('Y-m-d H:i:s');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if (!$tanggal_lahir) {
                echo "<script>alert('Format tanggal lahir tidak valid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            // Compare the dates
            if ($tanggal_lahir > $tanggal_sekarang){
                echo "<script>alert('Tanggal lahir tidak boleh kurang dari sekarang !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
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
                echo "<script>alert('Pengguna tidak ditemukan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role != 'super admin'){
                echo "<script>alert('Anda bukan super admin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check email input
            $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['email']);
            $stmt[1]->execute();
            if ($stmt[1]->fetch()) {
                $stmt[1]->close();
                echo "<script>alert('Email sudah digunakan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //get last id user
            $query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".$_SERVER['DB_DATABASE']."' AND TABLE_NAME = 'users' ";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->execute();
            $idUser = 1;
            $stmt[2]->bind_result($idUser);
            $stmt[2]->fetch();
            $stmt[2]->close();
            $folderAdmin = '/admin';
            //create folder
            if (!is_dir(self::$folderPath.$folderAdmin)) {
                mkdir(self::$folderPath.$folderAdmin, 0777, true);
            }
            //proses file
            $fileFoto = $_FILES['foto'];
            $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
            $size = filesize($fileFoto['size']);
            if (in_array($extension,['png','jpeg','jpg'])) {
                if ($size >= self::$sizeImg) {
                    echo "<script>alert('Ukuran File maksimal '".(self::$sizeImg/1000000)."MB' !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            } else {
                echo "<script>alert('File harus jpg, jpeg, png !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //simpan file
            $nameFile = '/'.$idUser.'.'.$extension;  
            $fileFotoPath = self::$folderPath.$folderAdmin.$nameFile;
            if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                echo "<script>alert('Gagal menyimpan file')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //insert to database 
            $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);
            $query = "INSERT INTO users (email,password, nama_lengkap, no_telpon, jenis_kelamin, tempat_lahir, tanggal_lahir, role, foto, verifikasi) VALUES (?, ?, ?, ?, ? , ?, ?, ?, ?, ?)";
            $verifikasi = 1;
            $stmt[3] = self::$con->prepare($query);
            $stmt[3]->bind_param("sssssssssi", $data['email'], $hashedPassword, $data['nama'], $data['phone'], $data['jenisK'],$data['tempatL'], $data['tanggalL'], $data['role'], $nameFile, $verifikasi);
            $stmt[3]->execute();
            if ($stmt[3]->affected_rows > 0) {
                $stmt[3]->close();
                echo "<script>alert('akun berhasil dibuat');</script>";
                echo "<script>window.location.href = '/admin.php';</script>";
                exit();
            } else {
                $stmt[3]->close();
                echo "<script>alert('Akun gagal dibuat');</script>";
                echo "<script>window.location.href = '/admin.php';</script>";
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
                    'message' => $errorJson['message'],
                );
            }
            echo "<script> alert('$responseData');</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
    public function editAdmin($data){
        try{
            if (!isset($data['id_admin']) || empty($data['id_admin'])) {
                echo "<script>alert('ID Admin harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['id_user']) || empty($data['id_user'])) {
                echo "<script>alert('ID User harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['email']) || empty($data['email'])) {
                echo "<script>alert('Email harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Email invalid !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (isset($data['pass']) && !empty($data['pass'])){
                if (strlen($data['pass']) < 8) {
                    echo "<script>alert('Password minimal 8 karakter !');</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                if (strlen($data['pass']) > 25) {
                    echo "<script>alert('Password maksimal 25 karakter !');</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['pass'])) {
                    echo "<script>alert('Password harus berisi setidaknya satu huruf kecil, satu huruf besar, dan satu angka !');</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            }
            if (!isset($data['nama']) || empty($data['nama'])) {
                echo "<script>alert('Nama lengkap harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['phone']) || empty($data['phone'])) {
                echo "<script>alert('Nomer telepon harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!is_numeric($data['phone'])) {
                echo "<script>alert('Nomer telepon harus berisi hanya angka.')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (strlen($data['phone']) < 8) {
                echo "<script>alert('Nomer telpon minimal 8 angka !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (strlen($data['phone']) > 15) {
                echo "<script>alert('Nomer telpon maksimal 15 angka !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (substr($data['phone'], 0, 2) !== '08') {
                echo "<script>alert('Nomer telepon harus dimulai dengan 08.')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['jenisK']) || empty($data['jenisK'])) {
                echo "<script>alert('Jenis kelamin harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!in_array($data['jenisK'], ['laki-laki','perempuan'])){
                echo "<script>alert('Invalid jenis kelamin !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['tempatL']) || empty($data['tempatL'])) {
                echo "<script>alert('Tempat lahir harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['tanggalL']) || empty($data['tanggalL'])) {
                echo "<script>alert('Tanggal lahir harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['role']) || empty($data['role'])) {
                echo "<script>alert('Role harus di isi !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if(!in_array($data['role'], ['super admin','admin event','admin pentas', 'admin tempat', 'admin seniman'])){
                echo "<script>alert('Invalid role !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check tanggal
            date_default_timezone_set('Asia/Jakarta');
            $tanggal_lahir = strtotime($data['tanggalL']);
            $tanggal_sekarang = date('Y-m-d');
            $tanggal_sekarang = strtotime($tanggal_sekarang);
            if (!$tanggal_lahir) {
                echo "<script>alert('Format tanggal lahir tidak valid !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            // Compare the dates
            if ($tanggal_lahir > $tanggal_sekarang){
                echo "<script>alert('Tanggal lahir tidak boleh kurang dari tanggal sekarang !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check Admin
            $query = "SELECT role FROM users WHERE id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_admin']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if(!$stmt[0]->fetch()){
                $stmt[0]->close();
                echo "<script>alert('Pengguna tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role != 'super admin'){
                echo "<script>alert('Anda bukan super admin')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check data user
            $query = "SELECT foto FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            $idUser = 1;
            $stmt[1]->bind_result($idUser);
            if(!$stmt[1]->fetch()){
                $stmt[1]->close();
                echo "<script>alert('Data Admin tidak ditemukan')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            //check email input
            $query = "SELECT id_user FROM users WHERE BINARY email = ? AND id_user != ? LIMIT 1";
            $stmt[2] = self::$con->prepare($query);
            $stmt[2]->bind_param('si', $data['email'],$data['id_user']);
            $stmt[2]->execute();
            if ($stmt[2]->fetch()) {
                $stmt[2]->close();
                echo "<script>alert('Email sudah digunakan !')</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[2]->close();
            //if upload file then update file
            if (isset($_FILES['foto']) & !empty($_FILES['foto']) && !is_null($_FILES['foto']) && $_FILES['foto']['error'] !== 4) {
                // echo 'file  ';
                // echo json_encode($_FILES['foto']);
                // exit();
                $folderAdmin = '/admin';
                //proses file
                $fileFoto = $_FILES['foto'];
                $extension = pathinfo($fileFoto['name'], PATHINFO_EXTENSION);
                $size = filesize($fileFoto['name']);
                if (in_array($extension,['png','jpeg','jpg'])) {
                    if ($size >= self::$sizeImg) {
                        echo "<script>alert('Ukuran File maksimal '".(self::$sizeImg/1000000)."MB' !')</script>";
                        echo "<script>window.history.back();</script>";
                        exit();
                    }
                } else {
                    echo "<script>alert('File harus jpg, jpeg, png !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
                //simpan file
                $nameFile = '/'.$idUser.'.'.$extension;  
                $fileFotoPath = self::$folderPath.$folderAdmin.$nameFile;
                if (!move_uploaded_file($fileFoto['tmp_name'], $fileFotoPath)) {
                    echo "<script>alert('Gagal menyimpan File !')</script>";
                    echo "<script>window.history.back();</script>";
                    exit();
                }
            }
            //jika admin mengubah password
            if(isset($data['pass']) && !empty($data['pass'])){
                $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);
                $query = "UPDATE users SET email = ?, password = ?, nama_lengkap = ?, no_telpon = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, role = ? WHERE id_user = ?";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param("ssssssssi", $data['email'], $hashedPassword, $data['nama'], $data['phone'], $data['jenisK'], $data['tempatL'], $data['tanggalL'], $data['role'], $data['id_user']);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    echo "<script>alert('akun berhasil diubah')</script>";
                    echo "<script>window.location.href = '/admin.php';</script>";
                    exit();
                } else {
                    $stmt->close();
                    echo "<script>alert('akun gagal diubah')</script>";
                    echo "<script>window.location.href = '/admin.php';</script>";
                    exit();
                }
            }else{
                $query = "UPDATE users SET email = ?, nama_lengkap = ?, no_telpon = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, role = ? WHERE id_user = ?";
                $stmt = self::$con->prepare($query);
                $stmt->bind_param("sssssssi", $data['email'], $data['nama'], $data['phone'], $data['jenisK'], $data['tempatL'], $data['tanggalL'], $data['role'], $data['id_user']);
                $stmt->execute();
                if ($stmt->affected_rows > 0) {
                    $stmt->close();
                    echo "<script>alert('akun berhasil diubah')</script>";
                    echo "<script>window.location.href = '/admin.php';</script>";
                    exit();
                } else {
                    $stmt->close();
                    echo "<script>alert('akun gagal diubah')</script>";
                    echo "<script>window.location.href = '/admin.php';</script>";
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
                    'message' => $errorJson['message'],
                );
            }
            echo "<script> alert('$responseData')</script>";
            exit();
        }
    }
    public function hapusAdmin($data){
        try{
            if (!isset($data['id_admin']) || empty($data['id_admin'])) {
                echo "<script>alert('ID admin harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if (!isset($data['id_user']) || empty($data['id_user'])) {
                echo "<script>alert('ID admin harus di isi !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            if($data['id_admin'] === $data['id_user']){
                echo "<script>alert('Anda tidak boleh menghapus anda sendiri !');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check Admin
            $query = "SELECT role FROM users WHERE BINARY id_user = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $data['id_admin']);
            $stmt[0]->execute();
            $role = '';
            $stmt[0]->bind_result($role);
            if (!$stmt[0]->fetch()) {
                $stmt[0]->close();
                echo "<script>alert('Admin tidak ditemukan');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[0]->close();
            if($role != 'super admin'){
                echo "<script>alert('Anda bukan super admin');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //check id_user
            $query = "SELECT role, foto FROM users WHERE id_user = ? LIMIT 1";
            $stmt[1] = self::$con->prepare($query);
            $stmt[1]->bind_param('s', $data['id_user']);
            $stmt[1]->execute();
            $pathFoto = '';
            $roleDB = '';
            $stmt[1]->bind_result($roleDB, $pathFoto);
            if (!$stmt[1]->fetch()) {
                $stmt[1]->close();
                echo "<script>alert('Data Pengguna tidak ditemukan');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            $stmt[1]->close();
            if($role == 'masyarakat'){
                echo "<script>alert('Anda tidak boleh menghapus data masyarakat');</script>";
                echo "<script>window.history.back();</script>";
                exit();
            }
            //delete file
            $fileFotoPath = self::$folderPath.'/admin'.$pathFoto;
            unlink($fileFotoPath);
            $query = "DELETE FROM users WHERE id_user = ? ";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('i', $data['id_user']);
            if ($stmt->execute()) {
                echo "<script>alert('akun berhasil dihapus')</script>";
                echo "<script>window.location.href = '/admin.php';</script>";
                exit();
            }else{
                echo "<script>alert('akun gagal dihapus')</script>";
                echo "<script>window.location.href = '/admin.php';</script>";
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
                    'message' => $errorJson['message'],
            );
            echo "<script>alert('$error')</script>";
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
}
    public function isExistUser($email){
        if(empty($email) || is_null($email)){
            return ['status'=>'error','message'=>'email empty'];
        }else{
            $query = "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt = self::$con->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->bind_result($email);
            if ($stmt->fetch()) {
                return ['status'=>'success','data'=>true];
            }else{
                return ['status'=>'success','data'=>false];
            }
        }
    }
    public function getChangePass($data, $uri, $method, $param){
        try{
            $changePassPage = new ChangePasswordController();
            $notificationPage = new NotificationPageController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'code' =>'nullable'
            // ],[
            //     'email.required'=>'Email harus di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0];
            //     }
            //     throw new Exception(json_encode(['status' => 'error', 'message' => $errors]));
            // }
            $code = isset($data['code']) ? $data['code'] : null;
            //get path
            $path = parse_url($uri, PHP_URL_PATH);
            $path = ltrim($path, '/');
            //get relative path 
            $lastSlashPos = strrpos($path, '/');
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if($path1 == '/verifikasi/password' && $method == 'GET'){
                $email = $param['email'];
                //get link 
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY LINK = ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email,$link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check link & email is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            $now->sub(new DateInterval('PT15M'));
                            $time = $now->format('Y-m-d H:i:s');
                            // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ?  AND updated_at >= ? LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ss', $email,$time);
                            $stmt[3]->execute();
                            $name = '';
                            $stmt[3]->bind_result($name);
                            //check email is valid
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $data = [
                                    'email' => $email,
                                    'div' => 'verifyDiv',
                                    'title' => 'Reset Password',
                                    'description' => 'password',
                                    'code' => '',
                                    'link' => $link
                                ];
                                extract($data);
                                include('view/page/forgotPassword.php');
                                exit();
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY link = ?";
                                $stmt = self::$con->prepare($query);
                                $stmt->bind_param('s', $link);
                                $result = $stmt->execute();
                                return $notificationPage->showFailResetPass('Link Expired');
                            }
                        }else{
                            $stmt[2]->close();
                            return $notificationPage->showFailResetPass('Link invalid');
                        }
                    }else{
                        $stmt[1]->close();
                        return $notificationPage->showFailResetPass('Email invalid');
                    }
                }else{
                    $stmt[0]->close();
                    return $notificationPage->showFailResetPass('Link invalid');
                }
            }else{
                $email = $data['email'];
                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND binary kode_otp = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $now->sub(new DateInterval('PT15M'));
                        $time = $now->format('Y-m-d H:i:s');
                        // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            return ['status'=>'success','message'=>'otp anda benar silahkan ganti password'];
                            // return response()->json(['status'=>'success','data'=>['div'=>'verifikasi','description'=>'password']]);
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $result = $stmt[3]->execute();
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'code otp expired'];
                        }
                    }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'code otp invalid'];
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($errorJson['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson->message,
                    );
                }
            }
            return $responseData;
        }
    }
    public function changePassEmail($data, $uri){
        try{
            $jwtController = new JwtController();
            // $validator = Validator::make($data, [
            //     'email'=>'required|email',
            //     'nama'=>'nullable',
            //     'password' => [
            //         'required',
            //         'string',
            //         'min:8',
            //         'max:25',
            //         'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            //     ],
            //     'password_confirm' => [
            //         'required',
            //         'string',
            //         'min:8',
            //         'max:25',
            //         'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            //     ],
            //     'code' => 'nullable',
            //     'link' => 'nullable',
            //     'description'=>'required'
            // ],[
            //     'email.required'=>'Email wreajib di isi',
            //     'email.email'=>'Email yang anda masukkan invalid',
            //     'password.required'=>'Password harus di isi',
            //     'password.min'=>'Password minimal 8 karakter',
            //     'password.max'=>'Password maksimal 25 karakter',
            //     'password.regex'=>'Password baru harus terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'password_confirm.required'=>'Password konfirmasi konfirmasi harus di isi',
            //     'password_confirm.min'=>'Password konfirmasi minimal 8 karakter',
            //     'password_confirm.max'=>'Password konfirmasi maksimal 25 karakter',
            //     'password_confirm.regex'=>'Password konfirmasi terdiri dari 1 huruf besar, huruf kecil, angka dan karakter unik',
            //     'description.required'=>'Deskripsi harus di isi',
            // ]);
            // if ($validator->fails()) {
            //     $errors = [];
            //     foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            //         $errors = $errorMessages[0];
            //     }
            //     return ['status' => 'error', 'message' => $errors];
            // }
            // var_dump($data);
            $email = $data['email'];
            $pass = $data["password"];
            $pass1 = $data["password_confirm"];
            $link = $data['link'];
            $desc = $data['description'];
            if($pass !== $pass1){
                return ['status'=>'error','message'=>'Password Harus Sama'];
            }else{
                if(is_null($link) || empty($link)){
                    if($desc == 'createUser'){
                        $hashedPassword = password_hash($data['pass'], PASSWORD_DEFAULT);
                        $query = "INSERT INTO users (email,password, nama_lengkap, verifikasi, role) VALUES (?, ?, ?, ?, ?)";
                        $verifikasi = 1;
                        $stmt = self::$con->prepare($query);
                        // $now = Carbon::now('Asia/Jakarta');
                        $role = 'MASYARAKAT';
                        $stmt->bind_param("sssis", $data['email'], $hashedPassword, $data['nama'],$verifikasi, $role);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            $stmt->close();
                            $data = $jwtController->createJWTWebsite(['email'=>$email]);
                            if(is_null($data)){
                                return ['status'=>'error','message'=>'create token error','code'=>500];
                            }else{
                                if($data['status'] == 'error'){
                                    return ['status'=>'error','message'=>$data['message']];
                                }else{
                                    $data1 = ['email'=>$email,'number'=>$data['number']];
                                    $encoded = base64_encode(json_encode($data1));
                                    setcookie('token1', $encoded, time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                    setcookie('token2', $data['data']['token'], time() + intval($_SERVER['JWT_ACCESS_TOKEN_EXPIRED']),'/');
                                    setcookie('token3', $data['data']['refresh'], time() + intval($_SERVER['JWT_REFRESH_TOKEN_EXPIRED']),'/');
                                    return ['status'=>'success','message'=>'Login sukses silahkan masuk dashboard'];
                                }
                            }
                        }else{
                            $stmt->close();
                            return ['status'=>'error','message'=>'Akun Gagal Dibuat'];
                        }
                    }else{
                        $code = $data['code'];
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY kode_otp = ? LIMIT 1";
                        $stmt[0] = self::$con->prepare($query);
                        $stmt[0]->bind_param('s', $code);
                        $stmt[0]->execute();
                        $name = '';
                        $stmt[0]->bind_result($name);
                        //check email is valid on table verifikasi
                        if ($stmt[0]->fetch()) {
                            $stmt[0]->close();
                            $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                            $stmt[1] = self::$con->prepare($query);
                            $stmt[1]->bind_param('s', $email);
                            $stmt[1]->execute();
                            $name = '';
                            $stmt[1]->bind_result($name);
                            //check email is valid on table users
                            if ($stmt[1]->fetch()) {
                                $stmt[1]->close();
                                $query = "SELECT id_user FROM users WHERE BINARY email = ? LIMIT 1";
                                $stmt[2] = self::$con->prepare($query);
                                $stmt[2]->bind_param('s', $email);
                                $stmt[2]->execute();
                                $name = '';
                                $stmt[2]->bind_result($name);
                                //check email and code is valid on table verifikasi
                                if ($stmt[2]->fetch()) {
                                    $stmt[2]->close();
                                    $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                                    $now->sub(new DateInterval('PT15M'));
                                    $time = $now->format('Y-m-d H:i:s');
                                    // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? LIMIT 1";
                                    $stmt[3] = self::$con->prepare($query);
                                    $stmt[3]->bind_param('ss', $email, $time);
                                    $stmt[3]->execute();
                                    $name = '';
                                    $stmt[3]->bind_result($name);
                                    //check time is valid on table verifikasi
                                    if ($stmt[3]->fetch()) {
                                        $stmt[3]->close();
                                        $newPass = password_hash($pass, PASSWORD_DEFAULT,['cost'=>10]);
                                        $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                        $stmt[4] = self::$con->prepare($query);
                                        $stmt[4]->bind_param('ss', $newPass, $email);
                                        $stmt[4]->execute();
                                        $affectedRows = $stmt[4]->affected_rows;
                                        //check time is valid on table verifikasi
                                        if ($affectedRows > 0) {
                                            $stmt[4]->close();
                                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
                                            $stmt[5] = self::$con->prepare($query);
                                            $stmt[5]->bind_param('s', $email);
                                            $result = $stmt[5]->execute();
                                            if($result){
                                                $stmt[5]->close();
                                                return ['status'=>'success','message'=>'ganti password berhasil silahkan login'];
                                            }else{
                                                $stmt[5]->close();
                                                return ['status'=>'error','message'=>'error update password','code'=>500];
                                            }
                                        }else{
                                            $stmt[4]->close();
                                            return ['status'=>'error','message'=>'error update password','code'=>500];
                                        }
                                    }else{
                                        $stmt[3]->close();
                                        $query = "DELETE FROM verifikasi WHERE BINARY kode_otp = ? AND deskripsi = 'password'";
                                        $stmt[4] = self::$con->prepare($query);
                                        $stmt[4]->bind_param('s', $code);
                                        $result = $stmt[4]->execute();
                                        $stmt[4]->close();
                                        return ['status'=>'error','message'=>'token expired'];
                                    }
                                }else{
                                    $stmt[2]->close();
                                    return ['status'=>'error','message'=>'Invalid Email'];
                                }
                            }else{
                                $stmt[1]->close();
                                return ['status'=>'error','message'=>'Invalid Email'];
                            }
                        }else{
                            $stmt[0]->close();
                            return ['status'=>'error','message'=>'token invalid'];
                        }
                    }
                //
                }else{
                    $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? AND deskripsi = $desc LIMIT 1";
                    $stmt[0] = self::$con->prepare($query);
                    $stmt[0]->bind_param('s', $link);
                    $stmt[0]->execute();
                    $name = '';
                    $stmt[0]->bind_result($name);
                    //check link is valid on table verifikasi
                    if ($stmt[0]->fetch()) {
                        $stmt[0]->close();
                        $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND deskripsi = $desc LIMIT 1";
                        $stmt[1] = self::$con->prepare($query);
                        $stmt[1]->bind_param('s', $email);
                        $stmt[1]->execute();
                        $name = '';
                        $stmt[1]->bind_result($name);
                        //check email is valid on table verifikasi
                        if ($stmt[1]->fetch()) {
                            $stmt[1]->close();
                            $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY link = ? AND deskripsi = $desc LIMIT 1";
                            $stmt[2] = self::$con->prepare($query);
                            $stmt[2]->bind_param('ss', $email, $link);
                            $stmt[2]->execute();
                            $name = '';
                            $stmt[2]->bind_result($name);
                            //check email and link is valid on table verifikasi
                            if ($stmt[2]->fetch()) {
                                $stmt[2]->close();
                                $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                                $now->sub(new DateInterval('PT15M'));
                                $time = $now->format('Y-m-d H:i:s');
                                // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                                $query = "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = $desc LIMIT 1";
                                $stmt[3] = self::$con->prepare($query);
                                $stmt[3]->bind_param('ss', $email, $time);
                                $stmt[3]->execute();
                                $name = '';
                                $stmt[3]->bind_result($name);
                                //check time is valid on table verifikasi
                                if ($stmt[3]->fetch()) {
                                    $stmt[3]->close();
                                    $query = "UPDATE users SET password = ? WHERE BINARY email = ? LIMIT 1";
                                    $stmt[4] = self::$con->prepare($query);
                                    $newPass = password_hash($pass, PASSWORD_DEFAULT);
                                    $stmt[4]->bind_param('ss', $newPass, $email);
                                    $stmt[4]->execute();
                                    $affectedRows = $stmt[4]->affected_rows;
                                    //check time is valid on table verifikasi
                                    if ($affectedRows > 0) {
                                        $stmt[4]->close();
                                        $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = $desc";
                                        $stmt[5] = self::$con->prepare($query);
                                        $stmt[5]->bind_param('s', $email);
                                        $result = $stmt[5]->execute();
                                        if($result){
                                            $stmt[5]->close();
                                            return ['status'=>'success','message'=>'ganti password berhasil silahkan login'];
                                        }else{
                                            $stmt[5]->close();
                                            return ['status'=>'error','message'=>'error update password','code'=>500];
                                        }
                                    }else{
                                        $stmt[4]->close();
                                        return ['status'=>'error','message'=>'error update password','code'=>500];
                                    }
                                }else{
                                    $stmt[3]->close();
                                    $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'password'";
                                    $stmt[4] = self::$con->prepare($query);
                                    $stmt[4]->bind_param('s', $email);
                                    $result = $stmt[4]->execute();
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'link expired'];
                                }
                            }else{
                                $stmt[2]->close();
                                return ['status'=>'error','message'=>'Email invalid'];
                            }
                        }else{
                            $stmt[1]->close();
                            return ['status'=>'error','message'=>'Invalid Email1'];
                        }
                    }else{
                        $stmt[0]->close();
                        return ['status'=>'error','message'=>'link expired'];
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($errorJson['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson->message,
                    );
                }
            }
            return $responseData;
        }
    }
    public function getVerifyEmail($data, $uri,$method){
        try{
            $validator = Validator::make($data, [
                'email'=>'required|email',
                'link' => 'nullable',
            ],[
                'email.required'=>'Email harus di isi',
                'email.email'=>'Email yang anda masukkan invalid',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                    $errors = $errorMessages[0];
                }
                return ['status' => 'error', 'message' => $errors];
            }
            $email = $data['email'];
            $query =  "SELECT nama_lengkap FROM users WHERE BINARY email = ? LIMIT 1";
            $stmt[0] = self::$con->prepare($query);
            $stmt[0]->bind_param('s', $email);
            $stmt[0]->execute();
            $name = '';
            $stmt[0]->bind_result($name);
            //check email is valid on table users
            if ($stmt[0]->fetch()) {
                $stmt[0]->close();
                //get path
                $path = parse_url($uri, PHP_URL_PATH);
                $path = ltrim($path, '/');
                //get relative path 
                $lastSlashPos = strrpos($path, '/');
                $path1 = substr($uri, 1, $lastSlashPos);
                // $email = $param['email'];
                if($path1 == '/verifikasi/email' && $method == 'GET'){
                    $link = ltrim(substr($path, strrpos($path, '/')),'/');
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $link);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid on table users
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                            $data = [
                                'email' => $email,
                                'div' => 'verifyDiv',
                                'title' => 'Reset Password',
                                'description' => 'password',
                                'code' => '',
                                'link' => $link
                            ];
                            extract($data);
                            include('view/page/verifyEmail.php');
                            exit();
                        }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'invalid token'];
                    }
                }
            }else{
                $stmt[0]->close();
                return ['status'=>'error','message'=>'Email invalid'];
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($errorJson['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson->message,
                    );
                }
            }
            return $responseData;
        }
    }
    public function verifyEmail($data,$uri, $method, $param){
        try{
            $notificationPage = new NotificationPageController();
            $validator = Validator::make($data, [
                'email'=>'required|email',
                'code' =>'nullable'
            ],[
                'email.required'=>'Email harus di isi',
                'email.email'=>'Email yang anda masukkan invalid',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                    $errors = $errorMessages[0]; 
                }
                throw new Exception(json_encode(['status' => 'error', 'message' => $errors]));
            }
            //get path
            $path = parse_url($uri, PHP_URL_PATH);
            $path = ltrim($path, '/');
            //get relative path 
            $lastSlashPos = strrpos($path, '/');
            $path1 = substr($uri, 0, $lastSlashPos+1);
            if($path1 == '/verifikasi/email' && $method == 'GET'){
                $email = $param['email'];
                $link = ltrim(substr($path, strrpos($path, '/')),'/');
                // echo 'link '.$link;
                $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY link = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $link);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check link is valid on table verifikasi
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('s', $email);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email is valid on table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY link = ? AND deskripsi = 'email' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $link);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check email and link is valid on table verifikasi
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                            $now->sub(new DateInterval('PT15M'));
                            $time = $now->format('Y-m-d H:i:s');
                            // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                            $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = 'email' LIMIT 1";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('ss', $email, $time);
                            $stmt[3]->execute();
                            $name = '';
                            $stmt[3]->bind_result($name);
                            //check time is valid on table verifikasi
                            if ($stmt[3]->fetch()) {
                                $stmt[3]->close();
                                $query =  "UPDATE users SET verifikasi = true WHERE BINARY email = ?";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $stmt[4]->execute();
                                $affectedRows = $stmt[4]->affected_rows;
                                //update users
                                if ($affectedRows > 0) {
                                    $stmt[4]->close();
                                    $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
                                    $stmt[5] = self::$con->prepare($query);
                                    $stmt[5]->bind_param('s', $email);
                                    $result = $stmt[5]->execute();
                                    if($result){
                                        $stmt[5]->close();
                                        return $notificationPage->showSuccessVerifyEmail('Verifikasi email berhasil silahkan login', ['email'=>$email]);
                                    }else{
                                        $stmt[5]->close();
                                        return $notificationPage->showFailVerifyEmail('Error verifikasi Email',500);
                                    }
                                }else{
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'error verifikasi email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY link = ?";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $link);
                                $result = $stmt[4]->execute();
                                $stmt[4]->close();
                                return $notificationPage->showFailVerifyEmail('Link Expired');
                            }
                        }else{
                            $stmt[2]->close();
                            return $notificationPage->showFailVerifyEmail('Link invalid');
                        }
                    }else{
                        $stmt[1]->close();
                        return $notificationPage->showFailVerifyEmail('email invalid');
                    }
                }else{
                    $stmt[0]->close();
                    return $notificationPage->showFailVerifyEmail('Link invalid');
                }
            }else{
                $email = $data['email'];
                $code = $data['code'];
                $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? LIMIT 1";
                $stmt[0] = self::$con->prepare($query);
                $stmt[0]->bind_param('s', $email);
                $stmt[0]->execute();
                $name = '';
                $stmt[0]->bind_result($name);
                //check email is valid on table verifikasi
                if ($stmt[0]->fetch()) {
                    $stmt[0]->close();
                    $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND BINARY kode_otp = ? AND deskripsi = 'email' LIMIT 1";
                    $stmt[1] = self::$con->prepare($query);
                    $stmt[1]->bind_param('ss', $email, $code);
                    $stmt[1]->execute();
                    $name = '';
                    $stmt[1]->bind_result($name);
                    //check email and code is valid on table verifikasi
                    if ($stmt[1]->fetch()) {
                        $stmt[1]->close();
                        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
                        $now->sub(new DateInterval('PT15M'));
                        $time = $now->format('Y-m-d H:i:s');
                        // $time = Carbon::now('Asia/Jakarta')->subMinutes(15)->format('Y-m-d H:i:s');
                        $query =  "SELECT id_verifikasi FROM verifikasi WHERE BINARY email = ? AND updated_at >= ? AND deskripsi = 'email' LIMIT 1";
                        $stmt[2] = self::$con->prepare($query);
                        $stmt[2]->bind_param('ss', $email, $time);
                        $stmt[2]->execute();
                        $name = '';
                        $stmt[2]->bind_result($name);
                        //check time is valid on table verifikasi
                        if ($stmt[2]->fetch()) {
                            $stmt[2]->close();
                            $query =  "UPDATE users SET verifikasi = true WHERE BINARY email = ?";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $stmt[3]->execute();
                            $affectedRows = $stmt[3]->affected_rows;
                            //check time is valid on table verifikasi
                            if ($affectedRows > 0) {
                                $stmt[3]->close();
                                $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
                                $stmt[4] = self::$con->prepare($query);
                                $stmt[4]->bind_param('s', $email);
                                $result = $stmt[4]->execute();
                                if($result){
                                    $stmt[4]->close();
                                    return ['status'=>'success','message'=>'verifikasi email berhasil silahkan login'];
                                }else{
                                    $stmt[4]->close();
                                    return ['status'=>'error','message'=>'error verifikasi email','code'=>500];
                                }
                            }else{
                                $stmt[3]->close();
                                return ['status'=>'error','message'=>'error update password','code'=>500];
                            }
                        }else{
                            $stmt[2]->close();
                            $query = "DELETE FROM verifikasi WHERE BINARY email = ? AND deskripsi = 'email'";
                            $stmt[3] = self::$con->prepare($query);
                            $stmt[3]->bind_param('s', $email);
                            $result = $stmt[3]->execute();
                            $stmt[3]->close();
                            return ['status'=>'error','message'=>'token expired'];
                        }
                    }else{
                        $stmt[1]->close();
                        return ['status'=>'error','message'=>'token invalid'];
                    }
                }else{
                    $stmt[0]->close();
                    return ['status'=>'error','message'=>'email invalid'];
                }
            }
        } catch (Exception $e) {
            // echo $e->getTraceAsString();
            $error = $e->getMessage();
            $errorJson = json_decode($error, true);
            if ($errorJson === null) {
                $responseData = array(
                    'status' => 'error',
                    'message' => $error,
                );
            }else{
                if($errorJson['message']){
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson['message'],
                    );
                }else{
                    $responseData = array(
                        'status' => 'error',
                        'message' => $errorJson->message,
                    );
                }
            }
            return $responseData;
        }
    }
    public function updateUser(){
    }
}
$user = new User;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['tambahAdmin'])){
        $user->tambahAdmin($_POST);
    }
    if(isset($_POST['_method'])){
        if($_POST['_method'] == 'PUT'){
            if(isset($_POST['editAdmin'])){
                $user->editAdmin($_POST);
            }
        }
        if($_POST['_method'] == 'DELETE'){
            if(isset($_POST['hapusAdmin'])){
                $user->hapusAdmin($_POST);
            }
        }
    }
}
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    if(isset($_SERVER['HTTP_REFERER'])){
        $previousUrl = $_SERVER['HTTP_REFERER'];
        $path = parse_url($previousUrl, PHP_URL_PATH);
    }else{
        $path = isset($data['uri']) ? $data['uri'] : null;
    }
}
?>