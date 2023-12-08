<?php
// Koneksi ke database MySQL
require('../Koneksi.php');
try{
// Menerima data dari aplikasi Android
// echo json_encode($_POST);
// exit();

// Direktori penyimpanan file
$uploadDirKTP = __DIR__.'/uploads/perpanjangan/ktp_seniman';
$uploadDirSurat = __DIR__.'/uploads/perpanjangan/surat_keterangan';
$uploadDirPassFoto = __DIR__.'/uploads/perpanjangan/pass_foto';

// Fungsi untuk menghasilkan nama file unik
function generateUniqueFileName($originalName, $uploadDirKTP) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    // Jika nama file belum ada, langsung gunakan nama asli
    if (!file_exists($uploadDirKTP .'/'. $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    // Jika nama file sudah ada, tambahkan indeks
    $counter = 1;
    while (file_exists($uploadDirKTP .'/'. $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName2($originalName, $uploadDirSurat) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirSurat .'/'. $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirSurat .'/'. $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

function generateUniqueFileName3($originalName, $uploadDirPassFoto) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);

    if (!file_exists($uploadDirPassFoto .'/'. $basename . '.' . $extension)) {
        return '/'.$basename . '.' . $extension;
    }

    $counter = 1;
    while (file_exists($uploadDirPassFoto .'/'. $basename . '(' . $counter . ')' . '.' . $extension)) {
        $counter++;
    }

    return '/'.$basename . '(' . $counter . ')' . '.' . $extension;
}

$id_user = $_POST['id_user'];
$id_user = str_replace(['"', "'"], '', $id_user); 
$nomor_induk = stripslashes($_POST['nomor_induk']);
$nomor_induk = str_replace(['"', "'"], '', $nomor_induk); 
$namaLengkap = stripslashes($_POST['nama_lengkap']);
$namaLengkap = str_replace(['"', "'"], '', $namaLengkap); 
$nik = stripslashes($_POST['nik']);
$nik = str_replace(['"', "'"], '', $nik); 
$id_seniman = stripslashes($_POST['id_seniman']);
$id_seniman = str_replace(['"', "'"], '', $id_seniman); 
$id_user = stripslashes($_POST['id_user']);
$id_user = str_replace(['"', "'"], '', $id_user); 
$status = stripslashes($_POST['status']);
$status = str_replace(['"', "'"], '', $status); 

// Menerima file gambar, dokumen PDF, dan gambar
$ktpSeniman = $_FILES['ktp_seniman'];
$suratKeterangan = $_FILES['surat_keterangan'];
$passFoto = $_FILES['pass_foto'];
//check user
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
$sql = "SELECT * FROM users WHERE id_user = '$id_user' LIMIT 1";
$result = $konek->query($sql);
if ($result->num_rows < 1) {
    $response['status'] = 'error';
    $response['message'] = 'Data Tidak Tersedia';
    echo json_encode($response);
    exit();
}
$sql = "SELECT * FROM seniman WHERE id_seniman = '$id_seniman' LIMIT 1";
$result = $konek->query($sql);
if ($result->num_rows == 1) {
    $senimans = $result->fetch_assoc();
    if($namaLengkap !== $senimans['nama_seniman']){
        $response['status'] = 'error';
        $response['message'] = 'Data Tidak Tersedia';
        echo json_encode($response);
        exit();
    }
    if($nik !== base64_decode($senimans['nik'])){
        $response['status'] = 'error';
        $response['message'] = 'Data Tidak Tersedia';
        echo json_encode($response);
        exit();
    }
    if($nomor_induk !== $senimans['nomor_induk']){
        $response['status'] = 'error';
        $response['message'] = 'Data Tidak Tersedia';
        echo json_encode($response);
        exit();
    }
}
$today = date('Y-m-d'); // Mengambil tanggal hari ini
$tgl_pembuatan = $today;
// Menggunakan prepared statement dengan menyebutkan nama kolom
$query = "INSERT INTO perpanjangan (id_seniman, status, ktp_seniman, surat_keterangan,pass_foto, tgl_pembuatan, id_user)
                          VALUES ('$id_seniman', '$status','$ktpName','$suratName','$fotoName','$tgl_pembuatan','$id_user')";

// Menggunakan bind_param dengan variabel
// $query->bind_param("is", $id_seniman, $status);
// $response['sql '] = $query;
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
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Exception: ' . $e->getMessage();
    header('Content-type: application/json');
    echo json_encode($response);
}
?>
