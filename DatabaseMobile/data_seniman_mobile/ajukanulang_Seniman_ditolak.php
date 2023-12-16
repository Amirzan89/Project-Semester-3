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
$namaLengkap = $_POST['nama_seniman'];
$jenisKelamin = $_POST['jenis_kelamin'];
$tempatLahir = $_POST['tempat_lahir'];
$tanggalLahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat_seniman'];
$noHandphone = $_POST['no_telpon'];
$namaOrganisasi = $_POST['nama_organisasi'];
$jumlahAnggota = $_POST['jumlah_anggota'];
$status = $_POST['status'];
$kategori = kategori(['kategori'=>str_replace(['"', "'"], '', $_POST['singkatan_kategori'])],'check',$pathFile,$konek);
$kecamatan = $_POST['kecamatan'];
$id_user = $_POST['id_user'];
$id_seniman = $_POST['id_seniman'];


$cek_iduser = "SELECT * FROM `seniman` WHERE id_seniman = '$id_seniman'";
$eksekusi_cek = mysqli_query($konek, $cek_iduser);
$jumlah_cek = mysqli_num_rows($eksekusi_cek);

$response = array();
if ($jumlah_cek == 1) {

    $perintah = "UPDATE `seniman` 
    SET `nik` = '$nik',
     `nama_seniman` = '$namaLengkap', 
     `jenis_kelamin` = '$jenisKelamin' , `tempat_lahir` = '$tempatLahir',  `tanggal_lahir` = '$tanggalLahir',  `alamat_seniman` = '$alamat',  `no_telpon` = '$noHandphone', `no_telpon` = '$noHandphone', 
     `nama_organisasi` = '$namaOrganisasi' , `jumlah_anggota` = '$jumlahAnggota',  `status` = 'diajukan',  `id_kategori_seniman` = '$kategori',  `kecamatan` = '$kecamatan',
     `catatan` = ''
     WHERE 
      `id_seniman` = $id_seniman;";

    $eksekusi = mysqli_query($konek, $perintah);

    if ($eksekusi) {
        $response["kode"] = 1;
        $response["pesan"] = "Update Berhasil";

    } else {
        $response["kode"] = 2;
        $response["pesan"] = "Update Gagal";
    }
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Ada Kesalahan";
}

echo json_encode($response);
mysqli_close($konek);
?>