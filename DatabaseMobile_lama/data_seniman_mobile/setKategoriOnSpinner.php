<?php
require('../Koneksi.php');

$idseniman = $_POST['id_seniman'];
    
    $sql = "SELECT kategori_seniman.nama_kategori  
    FROM seniman
    join kategori_seniman
    on kategori_seniman.id_kategori_seniman = seniman.id_kategori_seniman
    WHERE seniman.id_seniman = '$idseniman';";
    $result = $konek->query($sql);
 
    if ($result->num_rows == 1) {
        $TabelKategori = $result->fetch_assoc();

        $response["kode"] = 1;
        $response["pesan"] = "Data Tersedia";
        $response["data"] = $TabelKategori['nama_kategori'];
        
    } else {
        $response["kode"] = 0;
        $response["pesan"] = "Nama Kategori tidak tersedia";
    }

echo json_encode($response);
mysqli_close($konek);
?>
