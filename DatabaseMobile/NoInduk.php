<?php
// Koneksi ke database MySQL
require('Koneksi.php');
function kategori($data, $desc){
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
// Menerima data dari aplikasi Android
$nik = $_POST['nik'];
$namaLengkap = $_POST['nama_seniman'];
$jenisKelamin = $_POST['jenis_kelamin'];
$tempatLahir = $_POST['tempat_lahir'];
$tanggalLahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat_seniman'];
$noHandphone = $_POST['no_telpon'];
$namaOrganisasi = $_POST['nama_organisasi'];
$jumlahAnggota = $_POST['jumlah_anggota'];
$status = $_POST['status'];
$kategori = kategori(['kategori'=>$_POST['singkatan_kategori']],'check');
$kecamatan = $_POST['kecamatan'];
$id_user = $_POST['id_user'];

// Menerima file gambar, dokumen PDF, dan gambar
$ktpSeniman = $_FILES['ktp_seniman'];
$suratKeterangan = $_FILES['surat_keterangan'];
$passFoto = $_FILES['pass_foto'];

// Direktori penyimpanan file
$uploadDirKTP = __DIR__.'/uploads/seniman/ktp_seniman/';
$uploadDirSurat = __DIR__.'/uploads/seniman/surat_keterangan/';
$uploadDirPassFoto = __DIR__.'/uploads/seniman/pass_foto/';

$ktpSenimanFileName = $uploadDirKTP . generateUniqueFileName($ktpSeniman['name'], $uploadDirKTP);
move_uploaded_file($ktpSeniman['tmp_name'], $ktpSenimanFileName);

// Mengunggah dokumen PDF Surat Keterangan
$suratKeteranganFileName = $uploadDirSurat . generateUniqueFileName2($suratKeterangan['name'], $uploadDirSurat);
move_uploaded_file($suratKeterangan['tmp_name'], $suratKeteranganFileName);

// Mengunggah gambar Pass Foto
$passFotoFileName = $uploadDirPassFoto . generateUniqueFileName3($passFoto['name'], $uploadDirPassFoto);
move_uploaded_file($passFoto['tmp_name'], $passFotoFileName);

// Fungsi untuk menghasilkan nama file unik
function generateUniqueFileName($originalName, $uploadDirKTP) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    // Jika nama file belum ada, langsung gunakan nama asli
    if (!file_exists($uploadDirKTP . $basename . '.' . $extension)) {
        return $basename . '.' . $extension;
    }

    // Jika nama file sudah ada, tambahkan indeks
    $counter = 1;
    while (file_exists($uploadDirKTP . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return $basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName2($originalName, $uploadDirSurat) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirSurat . $basename . '.' . $extension)) {
        return $basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirSurat . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return $basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName3($originalName, $uploadDirPassFoto) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirPassFoto . $basename . '.' . $extension)) {
        return $basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirPassFoto . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return $basename . '(' . $counter . ')' . '.' . $extension;
}

// Enkripsi nilai $nik dengan Base64
$encryptedNik = base64_encode($nik);

// Menyimpan data ke database
$today = date('Y-m-d'); // Mengambil tanggal hari ini
$nextYear = date('Y') + 1; // Mengambil tahun berikutnya
$tgl_pembuatan = $today;
$tgl_berlaku = $nextYear . '-12-31';

$query = "INSERT INTO seniman (nik, nama_seniman, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat_seniman, no_telpon, nama_organisasi, jumlah_anggota, status, tgl_pembuatan, tgl_berlaku, id_user, id_kategori_seniman, kecamatan, ktp_seniman, surat_keterangan, pass_foto) 
          VALUES ('$encryptedNik', $namaLengkap, $jenisKelamin, $tempatLahir, $tanggalLahir, $alamat, $noHandphone, $namaOrganisasi, $jumlahAnggota, $status, '$tgl_pembuatan', '$tgl_berlaku', $id_user, $kategori, $kecamatan, '$ktpSenimanFileName','$suratKeteranganFileName', '$passFotoFileName')";

if ($konek->query($query) === TRUE) {
    $response['status'] = 'success';
    $response['message'] = 'Data berhasil disimpan';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Gagal menyimpan data: ' . $konek->error;
}

// Mengirim respons ke aplikasi Android dalam format JSON
header('Content-type: application/json');
echo json_encode($response);
?>
