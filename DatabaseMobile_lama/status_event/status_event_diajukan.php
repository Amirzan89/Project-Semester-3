<?php
require('../Koneksi.php');

$id_user = $_POST['id_user'];

$response = array(); 

$sql = "SELECT DISTINCT events.id_user, events.nama_pengirim, events.status, events.catatan, detail_events.*
        FROM events 
        JOIN detail_events ON events.id_event = events.id_event
        WHERE events.status = 'diajukan' AND id_user = '$id_user'
        ORDER BY detail_events.tanggal_awal DESC;";
$result = $konek->query($sql);

if ($result->num_rows > 0) {
    $surat_advis = array();

    while ($row = $result->fetch_assoc()) {
        $surat_advis[] = $row; 
    }

    $response["kode"] = 1;
    $response["pesan"] = "Data Tersedia";
    $response["data"] = $surat_advis;
} else {
    $response["kode"] = 0;
    $response["pesan"] = "Data Tidak Tersedia";
}

echo json_encode($response);

mysqli_close($konek);
?>
