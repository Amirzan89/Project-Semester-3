<?php
require('../Koneksi.php');
$pathFile = __DIR__."/../../kategori_seniman.json";
function kategori($data, $desc, $pathFile, $konek){
    try{
        $fileExist = file_exists($pathFile);
        if (!$fileExist) {
            //if file is delete will make new json file
            $query = "SELECT * FROM kategori_seniman";
            $stmt[0] = $konek->prepare($query);
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
            if (!file_put_contents($pathFile, $jsonData)) {
                throw new Exception('Gagal menyimpan file sistem');
            }
        }
        if($desc == 'check'){
            if(!isset($data['kategori']) || empty($data['kategori'])){
                throw new Exception('Kategori harus di isi');
            }
            $jsonFile = file_get_contents($pathFile);
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
            $jsonFile = file_get_contents($pathFile);
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
            $jsonFile = file_get_contents($pathFile);
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
            $jsonFile = file_get_contents($pathFile);
            $jsonData = json_decode($jsonFile, true);
            if($jsonData === null){
                throw new Exception('Data kategori tidak ditemukan');
            }
            return $jsonData;
        }else if($desc == 'getINI'){
            if(!isset($data['id_kategori']) || empty($data['id_kategori'])){
                throw new Exception('Kategori harus di isi');
            }
            $jsonFile = file_get_contents($pathFile);
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
$namaLengkap = str_replace(['"', "'"], '', $_POST['nama_seniman']);
$jenisKelamin = str_replace(['"', "'"], '', $_POST['jenis_kelamin']);
$tempatLahir = str_replace(['"', "'"], '', $_POST['tempat_lahir']);
$tanggalLahir = str_replace(['"', "'"], '', $_POST['tanggal_lahir']);
$alamat = str_replace(['"', "'"], '', $_POST['alamat_seniman']);
$noHandphone = str_replace(['"', "'"], '', $_POST['no_telpon']);
$namaOrganisasi = str_replace(['"', "'"], '', $_POST['nama_organisasi']);
$jumlahAnggota = str_replace(['"', "'"], '', $_POST['jumlah_anggota']);
$kategori = kategori(['kategori'=>str_replace(['"', "'"], '', $_POST['singkatan_kategori'])],'check',$pathFile,$konek);
$kecamatan = str_replace(['"', "'"], '', $_POST['kecamatan']);
$id_seniman = str_replace(['"', "'"], '', $_POST['id_seniman']);

// Menerima file gambar, dokumen PDF, dan gambar
$ktpSeniman = $_FILES['ktp_seniman'];
$suratKeterangan = $_FILES['surat_keterangan'];
$passFoto = $_FILES['pass_foto'];

// Direktori penyimpanan file
$uploadDirKTP = __DIR__.'/uploads/seniman/ktp_seniman';
$uploadDirSurat = __DIR__.'/uploads/seniman/surat_keterangan';
$uploadDirPassFoto = __DIR__.'/uploads/seniman/pass_foto';

// Mendapatkan path file lama sebelum update
$query_get_old_files = "SELECT ktp_seniman, surat_keterangan, pass_foto FROM seniman WHERE id_seniman = ?";
$stmt_get_old_files = mysqli_prepare($konek, $query_get_old_files);
mysqli_stmt_bind_param($stmt_get_old_files, 'i', $id_seniman);
mysqli_stmt_execute($stmt_get_old_files);
$result_old_files = mysqli_stmt_get_result($stmt_get_old_files);

if ($row_old_files = mysqli_fetch_assoc($result_old_files)) {
    $old_ktp_path = $uploadDirKTP.$row_old_files['ktp_seniman'];
    $old_surat_path = $uploadDirSurat.$row_old_files['surat_keterangan'];
    $old_pass_path = $uploadDirPassFoto.$row_old_files['pass_foto'];

    // Hapus file lama
    if (!empty($old_ktp_path) && file_exists($old_ktp_path)) {
        unlink($old_ktp_path);
    }

    if (!empty($old_surat_path) && file_exists($old_surat_path)) {
        unlink($old_surat_path);
    }

    if (!empty($old_pass_path) && file_exists($old_pass_path)) {
        unlink($old_pass_path);
    }
}

// Mengunggah foto ktp
$ktpName = generateUniqueFileName($ktpSeniman['name'], $uploadDirKTP);
$ktpSenimanFileName = $uploadDirKTP . $ktpName;
move_uploaded_file($ktpSeniman['tmp_name'], $ktpSenimanFileName);

// Mengunggah dokumen PDF Surat Keterangan
$suratName = generateUniqueFileName2($suratKeterangan['name'], $uploadDirSurat);
$suratKeteranganFileName = $uploadDirSurat . $suratName;
move_uploaded_file($suratKeterangan['tmp_name'], $suratKeteranganFileName);

// Mengunggah gambar Pass Foto
$fotoName = generateUniqueFileName3($passFoto['name'], $uploadDirPassFoto);
$passFotoFileName = $uploadDirPassFoto . $fotoName;
move_uploaded_file($passFoto['tmp_name'], $passFotoFileName);

// Fungsi untuk menghasilkan nama file unik
function generateUniqueFileName($originalName, $uploadDirKTP) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    // Jika nama file belum ada, langsung gunakan nama asli
    if (!file_exists($uploadDirKTP . $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    // Jika nama file sudah ada, tambahkan indeks
    $counter = 1;
    while (file_exists($uploadDirKTP . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName2($originalName, $uploadDirSurat) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirSurat . $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirSurat . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName3($originalName, $uploadDirPassFoto) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirPassFoto . $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirPassFoto . $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

// Enkripsi nilai $nik dengan Base64
$encryptedNik = base64_encode($nik);

$cek_iduser = "SELECT * FROM `seniman` WHERE id_seniman = ?";
$stmt = mysqli_prepare($konek, $cek_iduser);
mysqli_stmt_bind_param($stmt, 'i', $id_seniman);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Menyimpan data ke database
$today = date('Y-m-d'); // Mengambil tanggal hari ini
$nextYear = date('Y') + 1; // Mengambil tahun berikutnya
$tgl_pembuatan = $today;
$tgl_berlaku = $nextYear . '-12-31';

$query = "UPDATE seniman SET nik = ?, nama_seniman = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, status = 'diajukan', alamat_seniman = ?, no_telpon = ?, nama_organisasi = ?, jumlah_anggota = ?, tgl_pembuatan = ?, tgl_berlaku = ?, id_kategori_seniman = ?, kecamatan = ?, ktp_seniman = ?, surat_keterangan = ?, pass_foto = ? WHERE id_seniman = ?";
$stmt = mysqli_prepare($konek, $query);
mysqli_stmt_bind_param($stmt, 'ssssssssssssssssi', $encryptedNik, $namaLengkap, $jenisKelamin, $tempatLahir, $tanggalLahir, $alamat, $noHandphone, $namaOrganisasi, $jumlahAnggota, $tgl_pembuatan, $tgl_berlaku, $kategori, $kecamatan, $ktpName, $suratName, $fotoName, $id_seniman);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_error($stmt)) {
    $response['kode'] = 0;
    $response['pesan'] = 'Database error: ' . mysqli_stmt_error($stmt);
} else {
    $response['kode'] = 1;
    $response['pesan'] = 'Data updated successfully.';
}

// Mengirim respons ke aplikasi Android dalam format JSON
header('Content-type: application/json');
echo json_encode($response);
mysqli_close($konek);
?>
